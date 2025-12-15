<?
/**
 * 1) В модуле gpi.eo проверить id почтовых ящиков
 * 2) В файлах крона проверить $_SERVER
 * 3) В настройках CRM проверить ID и код поля. Пройти по БП. Настроить роботов
 * 4) crontab #* /1 * * * * php /var/www/312/cron/check_mail.php
 * 5) Проверить STORAGE_ID
 */
namespace Gpi;

use \Bitrix\Crm\Service\Container;
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Bizproc\Workflow\Entity\WorkflowStateTable;

class Eo
{
	public const SMART = 'EO';
	public const MANAGER = 'EoManager'; // Группа пользователей, с правами настройки Единого окна (формы, организации, смарты)
	public const HL_FORMS = 'EoForms';
	public const HL_ORGS = 'EoORg';
	public const HL_NEWS = 'EoNews';
	public const HL_DOCS = 'EoDocStatus';
	public const HL_COUNTRIES = 'EoCountries';
	public const STORAGE_ID = 39591;
	public const PARENT_FOLDER = 'Единое окно';
	public const CONTENT_TYPES = [
		'application/vnd.ms-excel', 
		'applicatiom/msword',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/zip',
		'application/gzip',
		'application/x-gzip',
		'application/pdf',
		'application/x-zip-compressed',
		'application/x-7z-compressed',
		'application/x-tar',
		'application/vnd.rar',
		'application/x-rar',
		'application/x-rar-compressed',
	];
	public const VID_TYPE = [
		'corp' => ['pred' , 'obj'],
		'stat' => ['gaz', 'god']
	];

	public const NEW_STATE = 'Начало';
	public const PREPARE_STATE = 'Подготовка';
	public const APPROVE_STATE = 'Согласование';
	public const DONE_STATE = 'Успех';

	public $arParams;
	public function __construct($type = '')
	{
		$arParams['FORM']['HL'] = self::getHL(self::HL_FORMS);
		$arParams['ORGS']['HL'] = self::getHL(self::HL_ORGS);
		$arParams['NEWS']['HL'] = self::getHL(self::HL_NEWS);
		$arParams['DOCS']['HL'] = self::getHL(self::HL_DOCS);
		$arParams['COUNTRIES']['HL'] = self::getHL(self::HL_COUNTRIES);

		$arParams['FORM']['UF'] = Eo\Helper::getUf($arParams, 'FORM');
		$arParams['ORGS']['UF'] = Eo\Helper::getUf($arParams, 'ORGS');
		$arParams['NEWS']['UF'] = Eo\Helper::getUf($arParams, 'NEWS');
		$arParams['DOCS']['UF'] = Eo\Helper::getUf($arParams, 'DOCS');
		$arParams['COUNTRIES']['UF'] = Eo\Helper::getUf($arParams, 'COUNTRIES');

		$string = json_encode(self::VID_TYPE);
		foreach ($arParams['FORM']['UF']['UF_TYPE']['xml'] as $key => $val) {
			$string = str_replace($val, $key, $string);
		}
		foreach ($arParams['FORM']['UF']['UF_VID']['xml'] as $key => $val) {
			$string = str_replace($val, $key, $string);
		}
		$arParams['FORM']['VIDTYPE'] = json_decode($string);

		if ($type == self::MANAGER)
		{
			$arParams['SMART'] = Eo\Helper::getSmartProcIDbyCode(self::SMART);
			$factory = Container::getInstance()->getFactory(
				$arParams['SMART']["ENTITY_TYPE_ID"]
			);
			$arParams['SMART']['CLASS'] = $factory->getDataClass();
			$arParams['SMART_UF_SETTINGS'] = $factory->getUserFieldsInfo();
			// Получим список статусов смарт-процесса
			$category = \Bitrix\Crm\Model\ItemCategoryTable::getList([
				'select' => ['*'],
				'filter' => [
					"ENTITY_TYPE_ID" => $arParams['SMART']["ENTITY_TYPE_ID"],
					"IS_DEFAULT"     => "Y",
				],
				'order'  => [],
				'cache'  => [],
			])->fetch();
			$arParams['SMART']['CATEGORY'] = $category['ID'];
			$statuses = \CCrmStatus::GetList(['SORT' => 'ASC'], ['ENTITY_ID' => 'DYNAMIC_' . $arParams['SMART']["ENTITY_TYPE_ID"] . '_STAGE_' . $category['ID']]);
			while ($status = $statuses->GetNext())
			{
				$arParams['STATUSES'][$status['STATUS_ID']] = $status['NAME'];
			}
		}

		$this->arParams = $arParams;
	}

	public static function checkAccess($group = Eo::MANAGER, $strict = false)
	{
		global $USER;
		$access = false;

		if (!$strict && $USER->isAdmin())
		{
			$access = true;
		}
		else
		{
			$currentUser = $USER->getId();
			$userGroups = \CUser::GetUserGroup($currentUser);
			$groups = \Bitrix\Main\GroupTable::getList([
				'select'  => ['ID'],
				'filter'  => ['STRING_ID' => $group],
			]);
			while ($elem = $groups->fetch())
			{
				if (in_array($elem['ID'], $userGroups))
				{
					$access = true;
				}
			}
		}
		// Проверим, что пользователь - куратор формы / организации
		$users = [];
		if (!$strict && !$access) {
			$hlForms = self::getHL(self::HL_FORMS);
			$hlOrgs = self::getHL(self::HL_ORGS);
			$arForms = self::getHlData($hlForms['CLASS'], [], ['UF_NAME' => 'ASC']);
			$arORgs = self::getHlData($hlOrgs['CLASS'], [], ['UF_NAME' => 'ASC']);
			$arUserForms = array_column($arForms, 'UF_APPROVERS');
			foreach($arUserForms as $ar) {
				$users = array_merge($users, (array)$ar);
			}
			$arUserOrgs = array_column($arORgs, 'UF_CURATOR');
			foreach($arUserOrgs as $ar) {
				$users = array_merge($users, (array)$ar);
			}
			$users = array_unique($users);
			if (in_array($currentUser, $users)) $access = true;
		}
		return $access;
	}

	public static function getHL($code)
	{
		\CModule::IncludeModule('highloadblock');
		$hl = HighloadBlockTable::getList([
			'filter' => [
				'NAME' => $code
			]
		])->fetch();
		return ['ID' => $hl['ID'], 'CLASS' => HighloadBlockTable::compileEntity($hl)->getDataClass()];
	}

	public static function getHlData($hlClass, $arFilter = [], $arSort = ['ID' => 'ASC'])
	{
		return $hlClass::getList([
			'select' => ['*'],
			'filter' => $arFilter,
			'order' => $arSort
		])->fetchAll();
	}

	public static function create($formId = '', $period = '', $orgId = '', $year, $planDate = '', $uids = false, $state = '', $debug = false)
	{
		$arMonth = [
			'02.' . $year => 'Январь',
			'03.' . $year => 'Февраль',
			'04.' . $year => 'Март',
			'05.' . $year => 'Апрель',
			'06.' . $year => 'Май',
			'07.' . $year => 'Июнь',
			'08.' . $year => 'Июль',
			'09.' . $year => 'Август',
			'10.' . $year => 'Сентябрь',
			'11.' . $year => 'Октябрь',
			'12.' . $year => 'Ноябрь',
			'01.' . ($year +1) =>  'Декабрь'
		];
		$arQuart = [
			'04.' . $year => '1 квартал',
			'07.' . $year => '2 квартал',
			'10.' . $year => '3 квартал',
			'01.' . ($year + 1) => '4 квартал'
		];

		// Собираем данные из HL-блока
		$eo = new Eo(Eo::MANAGER);
		$arParams = $eo->arParams;
		$arParams['YEAR'] = $year;
		$arParams['PERIOD'] = $period;
		$arParams['FORM_ID'] = $formId;
		$arParams['ORG_ID'] = $orgId;
		$pType = $arParams['FORM']['UF']['UF_PERIOD_TYPE'];
		$arForms = Eo::getHlData($arParams['FORM']['HL']['CLASS'], [], ['UF_NAME' => 'ASC']);
		$arORgs = Eo::getHlData($arParams['ORGS']['HL']['CLASS'], [], ['UF_NAME' => 'ASC']);
		$orgIDs = array_keys($arParams['FORM']['UF']['UF_ORG']['items']);
		$org = $arORgs[array_search($orgId, $orgIDs)]['UF_NAME'];
		
		// Проверим существуюющие записи
		$filter = [
			'UF_CRM_' . $arParams['SMART']['ID'] . '_FORM' => $arParams['FORM_ID'],
			'UF_CRM_' . $arParams['SMART']['ID'] . '_PERIOD' => $arParams['PERIOD'],
			'UF_CRM_' . $arParams['SMART']['ID'] . '_ORG' => $org,
			'UF_CRM_' . $arParams['SMART']['ID'] . '_YEAR' => $arParams['YEAR']
		];
		if ($formId == '') {
			unset($filter['UF_CRM_' . $arParams['SMART']['ID'] . '_FORM']);
		}
		
		if ($period == '')
		{
			unset($filter['UF_CRM_' . $arParams['SMART']['ID'] . '_PERIOD']);
		}
		
		if ($org == '')
		{
			unset($filter['UF_CRM_' . $arParams['SMART']['ID'] . '_ORG']);
		}

		$arExist = $arParams['SMART']['CLASS']::getList(['filter' => $filter,
		])->fetchAll();
		$res = [];
		
		if ($debug)
		{
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/eo_debug_create.log', print_r($arParams, true) . PHP_EOL);
		}

		foreach ($arForms as $form)
		{
			if ($formId != '' && $formId != $form['UF_XML_ID']) continue;
			switch ($pType['items'][$form['UF_PERIOD_TYPE']])
			{
				case "Ежемесячная":
					$r = Eo::createPeriod($arMonth, $form, $arParams, $arORgs, $arExist, $uids, $debug);
					$res = array_merge($res, (array)$r);
					break;
				case "Ежеквартальная":
					$r = Eo::createPeriod($arQuart, $form, $arParams, $arORgs, $arExist, $uids, $debug);
					$res = array_merge($res, (array)$r);
					break;
				case "Ежегодная":
					break;
				case "По запросу":
					$arCustom = [];
					if ($planDate != '') {
						$planDate = date('d.m.Y', strtotime($planDate));
						$arCustom = [
							$planDate => 'По запросу', // В месяц создания
						];
					}
					else {
						$m = date('m');
						$planDate = sprintf('%02d', $form['UF_DATE_PLAN']) . '.' . $m . '.' . $year;
						$arCustom = [
							$planDate => 'По запросу', // В месяц создания
						];
					}

					$r = Eo::createPeriod($arCustom, $form, $arParams, $arORgs, [], $uids, $debug);
					$res = array_merge($res, (array)$r);
					break;
			}
		}
		if ($state != '' && count($res)) {
			$factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($arParams['SMART']['ENTITY_TYPE_ID']);
			$smartClass = $factory->getDataClass();
			$filter = ['ID' => $res];

			$arItems = $smartClass::getList([
				'select' => ['ID'],
				'filter' => $filter,
			])->fetchAll();
			$result = [];
			foreach ($arItems as $item)
			{
				$el = $factory->getItem($item['ID']);
				$el['STAGE_ID'] = array_search($state, $arParams['STATUSES']);

				$operation = $factory->getUpdateOperation($el);
				$operation->disableCheckAccess();
				$result[] = $operation->launch();
			}
		}
		return (count($res) > 0) ? print_r($res, true) : 'Ничего не создано';
	}

	public static function createPeriod($arPeriod, $form, $arParams, $arORgs, $arExist, $uids, $debug = false)
	{
		global $USER;
		$res = [];
		$prefix = 'UF_CRM_' . $arParams['SMART']['ID'] . '_';
		$arPeriodNames = array_column($arParams['SMART_UF_SETTINGS'][$prefix . 'PERIOD']['ITEMS'], 'VALUE');
		$arPeriodKeys = array_column($arParams['SMART_UF_SETTINGS'][$prefix . 'PERIOD']['ITEMS'], 'ID');
		$pKey = array_search($arParams['PERIOD'], $arPeriodKeys);
		$pName = $arPeriodNames[$pKey];

		if ($form['UF_FILE_TEMPLATE'] > 0) {
			$arTemplate = \CFile::GetByID($form['UF_FILE_TEMPLATE'])->Fetch();
			$arFileName = explode('.', $arTemplate['ORIGINAL_NAME']);
			$fileExt = end($arFileName);
		}

		$orgIDs = array_keys($arParams['FORM']['UF']['UF_ORG']['items']);
		$orgNames = array_values($arParams['FORM']['UF']['UF_ORG']['items']);	

		foreach ($arPeriod as $i => $period)
		{
			if ($arParams['PERIOD'] != '' && $pName != $period) continue; // Пропускаем все периоды, если на входе указан период
			$date = ($pName != 'По запросу') ? sprintf('%02d', $form['UF_DATE_PLAN']) . '.' . $i : $i;

			foreach ($form['UF_ORG'] as $org)
			{
				$orgIndex = array_search($org, $orgIDs);
				$orgName = $arORgs[$orgIndex]['UF_NAME'];
				if ($arParams['ORG_ID'] != '' && $arParams['ORG_ID'] != $org) continue; // Пропускаем все организации, если на входе указана организация

				if (is_array($uids) && count($uids)) {
					$approvers = $uids;
				}
				elseif (is_array($uids)) {
					$form['UF_NEED_TO_APPROVE'] = 0;
				}
				else {
					$approvers = array_merge((array)$arORgs[$orgIndex]['UF_CURATOR'], (array)$form['UF_APPROVERS']);
					$approvers = array_unique($approvers);
				}
				$el = [];
				$xml = $arParams['FORM']['UF']['UF_ORG']['xml'][$org];

				//нужно подготовить template для каждой формы периода и организации из шаблона формы
				//при этом дубликат файла в файловой системе не создаётся
				$name = implode('_', [$form['UF_XML_ID'], $xml, $period, $arParams['YEAR']]);

				$arFile = null;
				$form['HAVE_TEMPLATE'] = 0;
				if ($form['UF_FILE_TEMPLATE'] > 0) {
					$form['HAVE_TEMPLATE'] = 1;
					$arFile = \CFile::MakeFileArray($arTemplate['ID']);
					$arFile['MODULE_ID'] = 'gpi.eo';
					$arFile['name'] = $name . '.' . $fileExt;
				}

				$el = [
					'TITLE' => $name,
					'CREATED_BY' => $USER->getId(),
					'UPDATED_BY' => $USER->getId(),
					'ASSIGNED_BY_ID' => $USER->getId(),
					$prefix . 'FORM' => $form['UF_XML_ID'],
					$prefix . 'YEAR' => $arParams['YEAR'],
					$prefix . 'TARGET_USER' => $approvers,
					$prefix . 'USERS' => $form['UF_USERS'],
					$prefix . 'DEPART' => $form['UF_DEPART'],
					$prefix . 'PERIOD' => $arParams['PERIOD'],
					$prefix . 'PLAN_DATE' => $date,
					$prefix . 'ORG' => $orgName,
					$prefix . 'FILE_MASK' => $name, // Маска файла без расширения для проверки соответствия
					$prefix . 'TEMPLATE' => $arFile,
					$prefix . 'ORG_EMAIL' => $arORgs[$orgIndex]['UF_EMAIL'],
					$prefix . 'EMAIL_FROM' => $arORgs[$orgIndex]['UF_EMAIL_FROM'],
					$prefix . 'NEED_TO_APPROVE' => $form['UF_NEED_TO_APPROVE'],
					$prefix . 'HAVE_TEMPLATE' => $form['HAVE_TEMPLATE'],
				];
				// Проверяем, существует ли уже запись или нет
				$existFlag = false;

				foreach ($arExist as $exist)
				{
					$index = array_search($exist[$prefix . 'ORG'], $orgNames);
					$orgId = $orgIDs[$index];
					if (
						$exist[$prefix . 'FORM'] == $el[$prefix . 'FORM'] &&
						$exist[$prefix . 'YEAR'] == $el[$prefix . 'YEAR'] &&
						$exist[$prefix . 'PERIOD'] == $el[$prefix . 'PERIOD'] &&
						$orgId !== false && in_array($orgId, $form['UF_ORG'])
					)
					{
						$existFlag = $exist;
					}
				}

				if ($debug) {
					file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/eo_debug_create.log', print_r($el, true) . PHP_EOL, FILE_APPEND);
				}

				if (!$existFlag && !$debug)
				{
					$factory = Container::getInstance()->getFactory(
						$arParams['SMART']["ENTITY_TYPE_ID"]
					);
					$Item = $factory->createItem($el); // Нужно для того, чтобы триггернуть БП
					$Item->setFromCompatibleData($el);
					
					$operation = $factory->getAddOperation($Item);
					$operation->disableAllChecks();
					$result = $operation->launch();

					if ($result->isSuccess()) // Стартуем бизнес-процесс
					{
						$id = $result->getId();

						$sm = [
							'ENTITY_TYPE_ID' => $arParams['SMART']["ENTITY_TYPE_ID"],
							'ELEMENT_ID' => $id
						];
						$comment = [
							'uid' => ($USER->getId()) ? $USER->getId() : 1,
							'text' => 'Уведомление::Элемент создан'
						];
						\Gpi\Eo\Helper::addCommentToLog($sm, $comment);
						$res[] = $id;
					}
					else
					{
						$res[] = print_r($result->getErrorMessages(), true);
					}
				}
			}
		}
		return $res;
	}

	public function getTasksByArId(array $arDocId): array
	{
		$result = [];
		\Bitrix\Main\Loader::includeModule('bizproc');
		$arWF = \Bitrix\Bizproc\Workflow\Entity\WorkflowStateTable::getList([
			'select' => ['ID', 'DOCUMENT_ID', 'TASK_ID' => 'TASK.ID', 'UID' => 'TASK_U.USER_ID'],
			'filter' => [
				'DOCUMENT_ID' => $arDocId,
				'MODULE_ID' => 'crm',
				'STATE' => 'InProgress',
				'TASK_U.STATUS' => \CBPTaskUserStatus::Waiting
			],
			'runtime' => [
				'TASK' => [
					'data_type' => '\Bitrix\Bizproc\Workflow\Task\TaskTable',
					'reference' => [
						'=this.ID' => 'ref.WORKFLOW_ID'
					],
					'join_type' => 'INNER'
				],
				'TASK_U' => [
					'data_type' => '\Bitrix\Bizproc\Workflow\Task\TaskUserTable',
					'reference' => [
						'=this.TASK_ID' => 'ref.TASK_ID'
					],
					'join_type' => 'INNER'
				],
			],
			'order' => ['DOCUMENT_ID' => 'ASC', 'STARTED' => 'DESC'],
		])->fetchAll();
		foreach ($arWF as $el) {
			$result[$el['DOCUMENT_ID']][$el['TASK_ID']][] = $el['UID'];
		}
		return $result;
	}
	
	public function getTasks($documentId)
	{
		$result = [];
		\Bitrix\Main\Loader::includeModule('bizproc');

		$arWF = WorkflowStateTable::getList([
			'select' => ['ID'],
			'filter' => [
				'DOCUMENT_ID' => $documentId,
				'MODULE_ID' => 'crm',
				'STATE' => 'InProgress',
			],
			'order' => ['STARTED' => 'DESC'],
		])->fetch();

		$filter = [
			"WORKFLOW_ID" => $arWF['ID'],
			'USER_STATUS' => \CBPTaskUserStatus::Waiting,
		];

		$dbTask = \CBPTaskService::GetList([], $filter, false, false, ['*']);
		while ($task = $dbTask->GetNext())
		{
			$result[$task['USER_ID']] = [
				'task' => $task,
				'wf' => $arWF['ID'],
				'control' => \CBPDocument::getTaskControls($task)
			];
		}
		return $result;
	}

	public static function start_bp($date = null)
	{
		$eo = new \Gpi\Eo(\Gpi\Eo::MANAGER);
		$arParams = $eo->arParams;
		$prefix = 'UF_CRM_' . $arParams['SMART']['ID'] . '_';

		if (is_null($date))
		{
			$date = date('Y-m-d');
		}
		$year = date('Y', strtotime($date));
		$m = date('m', strtotime($date));
		if ($m == 12)
		{
			$newYear = $year + 1;
			$m = '01';
		}
		else
		{
			$newYear = $year;
		}

		$factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($arParams['SMART']['ENTITY_TYPE_ID']);
		$smartClass = $factory->getDataClass();
		$filter = [
			'STAGE_ID' => array_search('Начало', $arParams['STATUSES']),
			$prefix . 'YEAR' => $year,
			'>=' . $prefix . 'PLAN_DATE' => '01.' . $m . '.' . $newYear,
			'<=' . $prefix . 'PLAN_DATE' => date('t', strtotime('01.' . $m . '.' . $newYear)) . '.' . $m . '.' . $newYear,
		];

		$arItems = $smartClass::getList([
			'select' => ['ID'],
			'filter' => $filter,
		])->fetchAll();
		$result = [];
		foreach ($arItems as $item)
		{
			$el = $factory->getItem($item['ID']);
			$el['STAGE_ID'] = array_search('Подготовка', $arParams['STATUSES']);

			$operation = $factory->getUpdateOperation($el);
			$operation->disableCheckAccess();
			$res = $operation->launch();

			if (!$res->isSuccess())
			{
				$result[] = $res->getErrorMessages();
			}
			else
			{
				$result[] = $item['ID'];
			}
		}

		return print_r($result, true);
	}

	public function getSmartData($filter)
	{
		$factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($this->arParams['SMART']['ENTITY_TYPE_ID']);
		$smartClass = $factory->getDataClass();
		$items = $smartClass::getList([
			'select' => ['*'],
			'filter' => $filter,
		])->fetchAll();
		$result = [];
		foreach ($items as $item) {
			$result[$item['ID']] = $item;
		}

		return $result;
	}
}
