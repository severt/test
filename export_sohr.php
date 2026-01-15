<?php
/**
 * Скрипт выборки данных из highload блока с экспортом в XLSX
 * Используется Bitrix D7 API и PhpSpreadsheet для пошагового экспорта
 */

// ============================================================================
// 1. АВТОМАТИЧЕСКОЕ ОПРЕДЕЛЕНИЕ КОРНЯ САЙТА (ДЛЯ CRON/CONSOLE)
// ============================================================================
if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $dir = __DIR__;
    // Ищем папку bitrix вверх по иерархии
    while ($dir != '/' && !file_exists($dir . '/bitrix')) {
        $dir = dirname($dir);
    }
    if (file_exists($dir . '/bitrix')) {
        $_SERVER["DOCUMENT_ROOT"] = $dir;
    } else {
        // ФОЛЛБЭК: Если не нашли, попробуйте раскомментировать и указать путь вручную
        // $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
        die("Критическая ошибка: Не удалось найти корневую директорию сайта (DOCUMENT_ROOT).");
    }
}

// ============================================================================
// 2. ПОДКЛЮЧЕНИЕ ЯДРА BITRIX (PROLOG_BEFORE)
// ============================================================================
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_WITH_ON_AFTER_EPILOG', true);
define('BX_NO_ACCELERATOR_RESET', true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// ============================================================================
// 3. ПОДКЛЮЧЕНИЕ МОДУЛЕЙ И ИМПОРТ КЛАССОВ
// ============================================================================
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Highload\HighloadBlockTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ПРИНУДИТЕЛЬНАЯ ЗАГРУЗКА МОДУЛЯ HIGHLOADBLOCK
if (!Loader::includeModule('highloadblock')) {
    die("Критическая ошибка: Модуль Highload Blocks (highloadblock) не установлен.");
}

// ============================================================================
// 4. КОНФИГУРАЦИОННЫЕ ПАРАМЕТРЫ
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
// 5. НАСТРОЙКА ОКРУЖЕНИЯ
// ============================================================================

// Предотвращение ограничения времени выполнения
set_time_limit(0);
ini_set('max_execution_time', 0);

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

    public function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        // Пишем в файл
        error_log($logMessage, 3, $this->logFile);
        
        // Дублируем в вывод (консоль или браузер)
        echo $logMessage;
        if (php_sapi_name() !== 'cli') {
            echo "<br>";
        }
    }

    public function logInfo(string $message): void { $this->log($message, 'INFO'); }
    public function logWarning(string $message): void { $this->log($message, 'WARNING'); }
    public function logError(string $message): void { $this->log($message, 'ERROR'); }
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
    
    // Свойства без строгой типизации для избежания конфликтов namespace
    private $entity;
    private $spreadsheet;
    private $worksheet;
    
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

    private function getHighloadBlockEntity()
    {
        if (isset($this->entity)) {
            return $this->entity;
        }

        // Повторная проверка загрузки модуля (на всякий случай)
        if (!Loader::includeModule('highloadblock')) {
            throw new Exception("Модуль highloadblock не загружен");
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

        // Компиляция сущности (Самый надежный метод)
        $entity = HighloadBlockTable::compileEntity($block);
        $this->entity = $entity->getDataClass();
        $entityClass = $this->entity;

        $this->logger->logInfo("Используется класс сущности: {$entityClass}");
        
        return $this->entity;
    }

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

    private function buildFilter(): array
    {
        $filter = [];
        foreach ($this->filterParams as $field => $value) {
            if (strpos($value, '@') !== false || strlen($value) > 0) {
                $filter[$field] = $value;
            }
        }
        $this->logger->logInfo('Применен фильтр: ' . json_encode($filter, JSON_UNESCAPED_UNICODE));
        return $filter;
    }

    private function initializeHeaders(): void
    {
        $entity = $this->getHighloadBlockEntity();
        
        $result = $entity::getList(['limit' => 1]);
        $row = $result->fetch();

        if ($row) {
            $this->headers = array_keys($row);
            
            $col = 1;
            foreach ($this->headers as $header) {
                $this->worksheet->setCellValueByColumnAndRow($col, $this->currentRow, $header);
                $col++;
            }

            // Стилизация заголовков
            $range = '1:1';
            try {
                $this->worksheet->getStyle($range)->getFont()->setBold(true);
                $this->worksheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $this->worksheet->getStyle($range)->getFill()->getStartColor()->setRGB('CCCCCC');
            } catch (\Exception $e) {
                // Игнорируем ошибки стилизации, если библиотека старая
            }

            $this->currentRow++;
            $this->logger->logInfo('Инициализированы заголовки: ' . implode(', ', $this->headers));
        }
    }

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

    private function writeBatch(array $batch): void
    {
        foreach ($batch as $rowData) {
            $col = 1;
            foreach ($this->headers as $header) {
                $value = $rowData[$header] ?? '';
                
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

    private function autoFitColumns(): void
    {
        try {
            foreach ($this->worksheet->getColumnIterator() as $column) {
                $this->worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
            $this->logger->logInfo('Подстроена ширина колонок');
        } catch (\Exception $e) {
            // Игнорируем ошибки
        }
    }

    private function saveFile(): void
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->exportFilePath);
        $this->logger->logInfo("Файл сохранен: {$this->exportFilePath}");
    }

    public function export(): bool
    {
        try {
            $startTime = time();

            $this->countTotalRecords();

            if ($this->totalRecords === 0) {
                $this->logger->logWarning('Записей с примененным фильтром не найдено');
                return false;
            }

            $this->initializeHeaders();

            $offset = 0;
            $batchNumber = 0;

            while ($offset < $this->totalRecords) {
                $batchNumber++;
                $this->logger->logInfo("Обработка партии {$batchNumber} (смещение: {$offset})");

                $batch = $this->fetchBatch($offset);

                if (empty($batch)) {
                    break;
                }

                $this->writeBatch($batch);

                $percentage = round(($this->processedRecords / $this->totalRecords) * 100, 2);
                $this->logger->logInfo("Обработано: {$this->processedRecords}/{$this->totalRecords} ({$percentage}%)");

                $elapsedTime = time() - $startTime;
                if ($elapsedTime > MAX_EXECUTION_TIME) {
                    $this->logger->logWarning("Превышено максимальное время выполнения: {$elapsedTime} сек");
                    break;
                }

                $offset += $this->batchSize;
                usleep(100000); 
            }

            $this->autoFitColumns();
            $this->saveFile();

            $this->logger->logInfo("Экспорт завершен успешно. Обработано записей: {$this->processedRecords}/{$this->totalRecords}");
            return true;

        } catch (\Throwable $e) {
            $this->logger->logError("Ошибка при экспорте: " . $e->getMessage());
            $this->logger->logError("Trace: " . $e->getTraceAsString());
            return false;
        }
    }
}

// ============================================================================
// ЗАПУСК
// ============================================================================

try {
    $logger = new ExportLogger(EXPORT_DIR);
    $logger->logInfo('=== НАЧАЛО ЭКСПОРТА ДАННЫХ ===');

    $exporter = new HighloadBlockExporter(
        HIGHLOAD_BLOCK_CODE,
        FILTER_PARAMS,
        EXPORT_DIR,
        EXPORT_FILENAME_FORMAT,
        BATCH_SIZE,
        $logger
    );

    if ($exporter->export()) {
        echo "SUCCESS";
    } else {
        echo "ERROR";
    }

} catch (\Throwable $e) {
    echo "CRITICAL ERROR: " . $e->getMessage();
    if (defined('EXPORT_DIR')) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . EXPORT_DIR . 'critical_error.log', (string)$e);
    }
}
?>
