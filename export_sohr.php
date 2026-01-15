<?php
/**
 * Скрипт выборки данных из highload блока с экспортом в XLSX
 * Используется Bitrix D7 API и PhpSpreadsheet для пошагового экспорта
 */
// ============================================================================
// ИНИЦИАЛИЗАЦИЯ И НАСТРОЙКА
// ============================================================================
// Предотвращение ограничения времени выполнения
set_time_limit(0);
ini_set('max_execution_time', 0);

// ============================================================================
// 1. ОПРЕДЕЛЕНИЕ КОРНЯ САЙТА (Для работы через CRON/Console)
// ============================================================================
// Если скрипт лежит в корне сайта или в папке /local/, /admin/
// Мы пытаемся найти DOCUMENT_ROOT автоматически
if (empty($_SERVER["DOCUMENT_ROOT"])) {
    // Ищем папку bitrix, поднимаясь вверх по директориям
    $dir = __DIR__;
    while ($dir != '/' && !file_exists($dir . '/bitrix')) {
        $dir = dirname($dir);
    }
    if (file_exists($dir . '/bitrix')) {
        $_SERVER["DOCUMENT_ROOT"] = $dir;
    } else {
        // Если не нашли, указываем жестко (раскомментируйте и укажите свой путь, если скрипт падает)
        // $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
        die("Ошибка: Не удалось определить DOCUMENT_ROOT. Запустите скрипт из папки сайта или укажите путь вручную.");
    }
}

// ============================================================================
// 2. ПОДКЛЮЧЕНИЕ ЯДРА БИТРИКС
// ============================================================================
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_WITH_ON_AFTER_EPILOG', true);
define('BX_NO_ACCELERATOR_RESET', true);

// Подключаем пролог (используем prolog_before, чтобы не грузить HTML админки)
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// ============================================================================
// 3. ПОДКЛЮЧЕНИЕ МОДУЛЕЙ И ПРОСТРАНСТВ ИМЕН
// ============================================================================
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Highload\HighloadBlockTable; // Пространство имен объявляем здесь
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ПРИНУДИТЕЛЬНАЯ ЗАГРУЗКА МОДУЛЯ HIGHLOADBLOCK
// Это должно происходить ДО любого использования классов этого модуля
if (!Loader::includeModule('highloadblock')) {
    echo "ОШИБКА: Модуль Highload Blocks не установлен или не загружен.\n";
    die();
}

// ============================================================================
// КОНФИГУРАЦИОННЫЕ ПАРАМЕТРЫ (настраиваются в начале скрипта)
// ============================================================================

// Код highload блока
const HIGHLOAD_BLOCK_CODE = 'Sohr';

// Параметры фильтра (поле => значение)
const FILTER_PARAMS = [
    'UF_SLEMAIL' => '@adm.local.ru',
    'UF_SLUDDOSTUP' => 't',
    'UF_SLVID' => 'ноутбук',
];

// Путь для сохранения XLSX файла (относительно корня сайта)
const EXPORT_DIR = '/upload/adm_sohr/';

// Формат имени файла (используется для генерации имени)
const EXPORT_FILENAME_FORMAT = 'Y-m-d_H-i.xlsx';

// Количество элементов, обрабатываемых за один шаг
const BATCH_SIZE = 100;

// Максимальное время выполнения скрипта (в секундах)
const MAX_EXECUTION_TIME = 3600; // 1 час

// ============================================================================
// КЛАСС ЛОГИРОВАНИЯ
// ============================================================================

class ExportLogger
{
    private string $logFile;

    public function __construct(string $exportDir)
    {
        $this->logFile = $_SERVER['DOCUMENT_ROOT'] . $exportDir . 'export.log';
    }

    /**
     * Логирование сообщения с временем
     * @param string $message Сообщение для логирования
     * @param string $level Уровень логирования (INFO, WARNING, ERROR)
     */
    public function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        error_log($logMessage, 3, $this->logFile);
        echo $logMessage; // Вывод в консоль
    }

    public function logInfo(string $message): void
    {
        $this->log($message, 'INFO');
    }

    public function logWarning(string $message): void
    {
        $this->log($message, 'WARNING');
    }

    public function logError(string $message): void
    {
        $this->log($message, 'ERROR');
    }
}

// ============================================================================
// ГЛАВНЫЙ КЛАСС ЭКСПОРТА
// ============================================================================

class HighloadBlockExporter
{
    private string $blockCode;
    private array $filterParams;
    private string $exportDir;
    private string $filenameFormat;
    private int $batchSize;
    private ExportLogger $logger;
    private DataManager $entity;
    private Spreadsheet $spreadsheet;
    private Worksheet $worksheet;
    private int $currentRow = 1;
    private array $headers = [];
    private int $totalRecords = 0;
    private int $processedRecords = 0;
    private string $exportFilePath;

    public function __construct(
        string $blockCode,
        array $filterParams,
        string $exportDir,
        string $filenameFormat,
        int $batchSize,
        ExportLogger $logger
    ) {
        $this->blockCode = $blockCode;
        $this->filterParams = $filterParams;
        $this->exportDir = $exportDir;
        $this->filenameFormat = $filenameFormat;
        $this->batchSize = $batchSize;
        $this->logger = $logger;

        // Инициализация spreadsheet
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->worksheet->setTitle('Данные');

        // Подготовка директории
        $this->prepareExportDirectory();

        // Генерация пути файла
        $filename = date($this->filenameFormat);
        $this->exportFilePath = $_SERVER['DOCUMENT_ROOT'] . $this->exportDir . $filename;

        $this->logger->logInfo("Инициализация экспортера для блока '{$this->blockCode}'");
    }

    /**
     * Создание директории для экспорта если не существует
     */
    private function prepareExportDirectory(): void
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . $this->exportDir;
        
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new Exception("Не удалось создать директорию: {$dir}");
            }
            $this->logger->logInfo("Создана директория: {$this->exportDir}");
        }
    }

    /**
     * Получение сущности highload блока
     */
    private function getHighloadBlockEntity(): DataManager
    {
        if (isset($this->entity)) {
            return $this->entity;
        }

        // Получение информации о highload блоке
        $result = HighloadBlockTable::getList([
            'filter' => ['=NAME' => $this->blockCode],
            'limit' => 1,
        ]);

        $block = $result->fetch();

        if (!$block) {
            throw new Exception("Highload блок '{$this->blockCode}' не найден");
        }

        // Получение класса сущности
        $entityClass = '\\Bitrix\\Highload\\' . $block['NAME'] . 'Table';
        
        if (!class_exists($entityClass)) {
            // Динамическое получение класса таблицы
            $entityClass = HighloadBlockTable::compileEntity($block)->getDataClass();
        }

        $this->logger->logInfo("Используется сущность: {$entityClass}");
        
        $this->entity = $entityClass;
        return $this->entity;
    }

    /**
     * Подсчет общего количества записей с применением фильтра
     */
    private function countTotalRecords(): int
    {
        $entity = $this->getHighloadBlockEntity();
        
        $result = $entity::getList([
            'select' => ['ID'],
            'filter' => $this->buildFilter(),
            'count_total' => true,
        ]);

        $this->totalRecords = $result->getCount();
        $this->logger->logInfo("Найдено записей: {$this->totalRecords}");

        return $this->totalRecords;
    }

    /**
     * Построение фильтра для запроса
     */
    private function buildFilter(): array
    {
        $filter = [];

        foreach ($this->filterParams as $field => $value) {
            // Для полей, содержащих текст, используется фильтр LIKE
            if (strpos($value, '@') !== false || strlen($value) > 0) {
                $filter["{$field}"] = $value;
            }
        }

        $this->logger->logInfo('Применен фильтр: ' . json_encode($filter, JSON_UNESCAPED_UNICODE));

        return $filter;
    }

    /**
     * Инициализация заголовков листа
     */
    private function initializeHeaders(): void
    {
        $entity = $this->getHighloadBlockEntity();
        
        // Получение всех полей (primary key + user fields)
        $result = $entity::getList([
            'limit' => 1,
        ]);

        $row = $result->fetch();

        if ($row) {
            $this->headers = array_keys($row);
            
            // Запись заголовков в первую строку
            $col = 1;
            foreach ($this->headers as $header) {
                $this->worksheet->setCellValueByColumnAndRow($col, $this->currentRow, $header);
                $col++;
            }

            // Стилизация заголовков (полужирный текст)
            $range = '1:1';
            $this->worksheet->getStyle($range)->getFont()->setBold(true);
            $this->worksheet->getStyle($range)->getFill()->setFillType('solid');
            $this->worksheet->getStyle($range)->getFill()->getStartColor()->setRGB('CCCCCC');

            $this->currentRow++;

            $this->logger->logInfo('Инициализированы заголовки: ' . implode(', ', $this->headers));
        }
    }

    /**
     * Выборка данных партией
     */
    private function fetchBatch(int $offset): array
    {
        $entity = $this->getHighloadBlockEntity();

        $result = $entity::getList([
            'select' => array_keys($this->headers),
            'filter' => $this->buildFilter(),
            'offset' => $offset,
            'limit' => $this->batchSize,
            'order' => ['ID' => 'ASC'],
        ]);

        $batch = [];
        while ($row = $result->fetch()) {
            $batch[] = $row;
        }

        return $batch;
    }

    /**
     * Запись партии данных в лист
     */
    private function writeBatch(array $batch): void
    {
        foreach ($batch as $rowData) {
            $col = 1;
            
            foreach ($this->headers as $header) {
                $value = $rowData[$header] ?? '';
                
                // Преобразование значений для корректного отображения
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                } elseif ($value === true) {
                    $value = 't';
                } elseif ($value === false) {
                    $value = 'f';
                }

                $this->worksheet->setCellValueByColumnAndRow($col, $this->currentRow, $value);
                $col++;
            }

            $this->currentRow++;
            $this->processedRecords++;
        }
    }

    /**
     * Автоматическая подстройка ширины колонок
     */
    private function autoFitColumns(): void
    {
        foreach ($this->worksheet->getColumnIterator() as $column) {
            $this->worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $this->logger->logInfo('Подстроена ширина колонок');
    }

    /**
     * Сохранение файла XLSX
     */
    private function saveFile(): void
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->exportFilePath);

        $this->logger->logInfo("Файл сохранен: {$this->exportFilePath}");
    }

    /**
     * Главный метод экспорта с пошаговой обработкой
     */
    public function export(): bool
    {
        try {
            $startTime = time();

            // Подсчет общего количества записей
            $this->countTotalRecords();

            if ($this->totalRecords === 0) {
                $this->logger->logWarning('Записей с примененным фильтром не найдено');
                return false;
            }

            // Инициализация заголовков
            $this->initializeHeaders();

            // Пошаговая обработка партий данных
            $offset = 0;
            $batchNumber = 0;

            while ($offset < $this->totalRecords) {
                $batchNumber++;

                $this->logger->logInfo("Обработка партии {$batchNumber} (смещение: {$offset})");

                // Получение партии данных
                $batch = $this->fetchBatch($offset);

                if (empty($batch)) {
                    break;
                }

                // Запись партии в лист
                $this->writeBatch($batch);

                $percentage = round(($this->processedRecords / $this->totalRecords) * 100, 2);
                $this->logger->logInfo(
                    "Обработано: {$this->processedRecords}/{$this->totalRecords} ({$percentage}%)"
                );

                // Проверка лимита времени выполнения
                $elapsedTime = time() - $startTime;
                if ($elapsedTime > MAX_EXECUTION_TIME) {
                    $this->logger->logWarning(
                        "Превышено максимальное время выполнения: {$elapsedTime} сек"
                    );
                    break;
                }

                $offset += $this->batchSize;

                // Небольшая пауза между партиями для снижения нагрузки
                usleep(100000); // 0.1 секунды
            }

            // Подстройка ширины колонок перед сохранением
            $this->autoFitColumns();

            // Сохранение файла
            $this->saveFile();

            $this->logger->logInfo(
                "Экспорт завершен успешно. " .
                "Обработано записей: {$this->processedRecords}/{$this->totalRecords}"
            );

            return true;

        } catch (Exception $e) {
            $this->logger->logError("Ошибка при экспорте: " . $e->getMessage());
            return false;
        }
    }
}

// ============================================================================
// ОСНОВНОЙ КОД ВЫПОЛНЕНИЯ
// ============================================================================

try {
    // Создание логгера
    $logger = new ExportLogger(EXPORT_DIR);
    $logger->logInfo('=== НАЧАЛО ЭКСПОРТА ДАННЫХ ===');
    $logger->logInfo("Блок: " . HIGHLOAD_BLOCK_CODE);
    $logger->logInfo("Размер партии: " . BATCH_SIZE);
    $logger->logInfo("Максимальное время: " . MAX_EXECUTION_TIME . " сек");

    // Создание и запуск экспортера
    $exporter = new HighloadBlockExporter(
        HIGHLOAD_BLOCK_CODE,
        FILTER_PARAMS,
        EXPORT_DIR,
        EXPORT_FILENAME_FORMAT,
        BATCH_SIZE,
        $logger
    );

    // Запуск экспорта
    if ($exporter->export()) {
        $logger->logInfo('=== ЭКСПОРТ УСПЕШНО ЗАВЕРШЕН ===');
        echo "✓ Экспорт завершен успешно\\n";
    } else {
        $logger->logError('=== ЭКСПОРТ ЗАВЕРШЕН С ОШИБКАМИ ===');
        echo "✗ Экспорт завершен с ошибками\\n";
    }

} catch (Exception $e) {
    echo "Критическая ошибка: " . $e->getMessage() . "\\n";
    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . EXPORT_DIR . 'critical_error.log',
        date('Y-m-d H:i:s') . " | " . $e->getMessage() . PHP_EOL,
        FILE_APPEND
    );
}

// Отключение пролога (если требуется)
// require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>
