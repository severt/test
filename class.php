<?
namespace Gpi;

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\SystemException;
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Iblock\Elements\ElementCallbackTable;
use \lib\HelperFunc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CallbackMedical extends \CBitrixComponent
{
	public $arParams;
	public $arResult;

	protected const PHONE = "UsersPhoneSpr";
	protected const MC = 'MedicalCenter';

	private $_request;

	/**
	 * Проверка наличия модулей требуемых для работы компонента
	 * @return bool
	 * @throws Exception
	 */
	private function _checkModules()
	{
		if (!Loader::includeModule('iblock')) {
			throw new \Exception('Не загружен модуль инфоблоков необходимый для работы компонента');
		}

		return true;
	}

	/**
	 * Обертка над глобальной переменной
	 * @return CAllMain|CMain
	 */
	private function _app()
	{
		global $APPLICATION, $USER;

		$hl = HighloadBlockTable::getList(['filter' => ['NAME' => self::MC]])->fetch();
		$arItems = HighloadBlockTable::compileEntity($hl["ID"])->getDataClass()::getList([
            'select' => ['*'],
			'cache' => ["ttl" => 0, 'cache_joins' => true],
			'order' => [
				'UF_CITY' => 'desc',
				'UF_NAME' => 'asc',
			],
			'filter' => [
				'!UF_CITY' => ''
			],
		])->fetchAll();
		foreach ($arItems as $mc) {
			$this->arResult['MC'][] = $mc['UF_NAME'];
			//$this->arResult['SPEC'][] = $mc['UF_SPECIALIST'];
			$this->arResult['EMAIL'][] = $mc['UF_EMAIL'];
			$this->arResult['CITY'][$mc['UF_CITY']][] = $mc['UF_NAME'];
		}
		$this->arResult['SPEC'] = [
			'Запись к врачу', 'Запись на исследования', 'Запись на процедуры', 'Получение справочной информации'
		];
		$blockID = HelperFunc::getInfoBlockIDbyName($this->arParams['IBLOCK_NAME']);

		$arFilter = ['IBLOCK_ID' => $blockID, 'ACTIVE => Y', 'PROPERTY_USER' => $USER->GetID()];
		$rs = \CIBlockElement::GetList(
			['ID' => 'DESC'],
			$arFilter,
			false,
			['nTopCount' => 5],
			[
				'ID',
				'DATE_CREATE',
				'TIMESTAMP_X',
				'CREATED_BY',
				'NAME',
				'PROPERTY_USER',
				'PROPERTY_PHONE',
				'PROPERTY_MEDICAL_CENTER',
				'PROPERTY_SPECIALIST'
			]
		);

		$i = 0;
		while($item = $rs->Fetch()) {
			$this->arResult['CALL'][$i]['DATA'] = $item['DATE_CREATE'];
			$this->arResult['CALL'][$i]['PHONE'] = $item['PROPERTY_PHONE_VALUE'];
			$this->arResult['CALL'][$i]['NAME'] = $item['NAME'];
			$this->arResult['CALL'][$i]['MEDICAL_CENTER'] = $item['PROPERTY_MEDICAL_CENTER_VALUE'];
			$this->arResult['CALL'][$i]['SPECIALIST'] = $item['PROPERTY_SPECIALIST_VALUE'];
			$i++;
		}

		return $APPLICATION;
	}

	/**
	 * Обертка над глобальной переменной
	 * @return CAllUser|CUser
	 */
	private function _user()
	{
		global $USER;
		$hl = HighloadBlockTable::getList(['filter' => ['NAME' => self::PHONE]])->fetch();
		$rsUsers = UserTable::getList(array(
			'filter' => ['ID' => $USER->GetID()],
			'select' => ['ID', 'LAST_NAME', 'NAME', 'SECOND_NAME', 'WORK_PHONE', 'PERSONAL_CITY', 'S.UF_ADDRESS'],
			"runtime" => [
                "S" => [
                    'data_type' => HighloadBlockTable::compileEntity($hl)->getDataClass(),
                    'reference' => [
                        '=this.EMAIL' => 'ref.UF_EMAIL',
                    ],
                    'join_type' => 'LEFT',
                ]
            ]
		));

		while ($arUser = $rsUsers->Fetch()) {

			$this->arResult['USER']['ID'] = $arUser['ID'];
			$this->arResult['USER']['NAME'] = rtrim($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']);
			$this->arResult['USER']['CITY'] = ($arUser['PERSONAL_CITY'] != '') ? $arUser['PERSONAL_CITY'] : $arUser['MAIN_USER_S_UF_ADDRESS'];
			$this->arResult['USER']['CITY'] = ($this->arResult['USER']['CITY'] == '') ? 'Не определён' : $this->arResult['USER']['CITY'];

			$phone = $arUser['WORK_PHONE'];
			$strings_to_match = ['Моб', 'Гор', '?:?'];
			$strings_replace = ['Газовый телефон:','(700)', '+7(700)', '-', ' '];
			$match = false;
			foreach ($strings_to_match as $string_to_match){
				if (strpos($phone, $string_to_match) !== false){
					$match = true;
					break;
				}
			}
			if(!$match){
				$phone = str_ireplace($strings_replace, '', $phone);
				$this->arResult['USER']['PHONE'] = trim($phone);;
			}
		}

		return $USER;
	}

	/**
	 * Подготовка параметров компонента
	 * @param $arParams
	 * @return mixed
	 */
	public function onPrepareComponentParams($arParams){
		$this->_request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getPost('submit');

		if ($this->_request && check_bitrix_sessid()) {
			$arParams["IBLOCK_ID"] = HelperFunc::getInfoBlockIDbyName('callback');
			$arParams["USER"] = $arParams["FIELDS"]["user"];
			$arParams['USER_ID'] = $this->arResult['USER']['ID'];
			$arParams["PHONE"] = $arParams["FIELDS"]["phone"] ?? '';
			$arParams["CODE"] = 'GAZ';

			$arParams['MEDICAL_CENTER'] = $arParams['FIELDS']['center'] ?? '';
			$arParams['CITY'] = $arParams['FIELDS']['city'] ?? '';
			$arParams['SPECIALIST'] = $arParams['FIELDS']['specialist'] ?? '';
		}
		return $arParams;
	}

	protected function insertElement()
	{
		$ELEMENT = [];
		$this->_request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getPost('submit');
		if ($this->_request) {
			$el = new \CIBlockElement;

			$ELEMENT['EMAIL'] =  $this->arResult['EMAIL'][$this->arParams['MEDICAL_CENTER']];
		
			if(!$ELEMENT['EMAIL']){
				$index = array_search($this->arParams['MEDICAL_CENTER'], $this->arResult['MC']);
				$ELEMENT['EMAIL'] = $this->arResult['EMAIL'][$index];
			}

			$PROP = [];
			$PROP["USER"] = $this->arResult['USER']['ID'];
			$PROP['CODE'] = $this->arParams['CODE'];
			$PROP["PHONE"] = $this->arParams["PHONE"];
			$PROP['MEDICAL_CENTER'] = $this->arParams['MEDICAL_CENTER'];
			$PROP['SPECIALIST'] = $this->arParams['SPECIALIST'];
			$PROP['EMAIL'] = $ELEMENT['EMAIL'];

			$arLoadElementArray = [
				"MODIFIED_BY" => $this->arResult["USER"]["ID"],
				"IBLOCK_SECTION_ID" => false,
				"IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
				"PROPERTY_VALUES" => $PROP,
				"NAME" => $this->arParams["USER"],
				"ACTIVE" => "Y",
			];
			
			$ELEMENT['ID']  = $el->Add($arLoadElementArray);
		}
		//logtofile($ELEMENT);
		return $ELEMENT;
	}

	protected function sendData(array $ELEMENT)
	{
		$message = 'Обращение №' . $ELEMENT['ID'] . 'на обратный звонок пациенту отправлено';

		if (!is_numeric($ELEMENT['ID'])) {
			return 'Номер обращения не соответствует формату: ' . $ELEMENT['ID'];
		}

		\CModule::IncludeModule('iblock');
		$blockID = HelperFunc::getInfoBlockIDbyName('callback');

		if ($ELEMENT['ID']) {
			// $rsItem = ElementCallbackTable::getList(
			// 	[
			// 		'select' => [
			// 			'ID', 'DATE_CREATE', 'CREATED_BY', 'NAME',
			// 			'PHONE_' => 'PHONE',
			// 			'CODE_' => 'CODE',
			// 			'MEDICAL_CENTER_' => 'MEDICAL_CENTER',
			// 			'SPECIALIST_' => 'SPECIALIST',
			// 		],
			// 		'filter' => ['ID' => $ELEMENT['ID'], 'IBLOCK_ID' => $blockID],
			// 		'cache' => ['ttl' => 0, 'cache_joins' => true]
			// 	]
			// );

			$rsItem = \CIBlockElement::GetList(
				[],
				['ID' => $ELEMENT['ID'], 'IBLOCK_ID' => $blockID],
				false, 
				[],
				[
				'ID', 'DATE_CREATE', 'CREATED_BY', 'NAME',
				//'COMMENT_' => 'COMMENT',
				'PROPERTY_PHONE',
				// 'PROPERTY_FROM_TIME',
				// 'PROPERTY_TILL_TIME',
				'PROPERTY_MEDICAL_CENTER',
				'PROPERTY_SPECIALIST',
				'PROPERTY_CODE',
				],
			);

			$arItem = $rsItem->fetch();
			$message =
				"<p>ФИО пациента: {$this->arParams['FIELDS']['user']}</p>".
				"<p>Город (адрес) пациента: {$this->arParams['FIELDS']['city']}</p>".
				"<p>Телефон: {$this->arParams['FIELDS']['phone']}</p>".
				"<p>Цель обращения: {$this->arParams['FIELDS']['specialist']}</p>".
				"<p>Выбранный медицинский центр: {$this->arParams['FIELDS']['center']}</p>";
				//'<p>Специалист: ' . $arItem['SPECIALIST_VALUE'] . '</p>';
			if ($this->arParams['FIELDS']['phone'] == '70039051') {
				dump_t($message);
				die();
			}
			try {
				$siteId = SITE_ID;
				$sender = \COption::GetOptionString("main", "email_from");
				$eventName = 'BIZPROC_HTML_MAIL_TEMPLATE';
				$title = 'Заявка на ' . mb_strtolower($arItem['PROPERTY_SPECIALIST_VALUE']) . ', ' . $this->arParams['FIELDS']['center'];
				$arFields = [
					"SENDER" => 'Корпоративный портал <' . $sender . '>', // отправитель
					"REPLY_TO" => "",
					"RECEIVER" => implode(',', [$ELEMENT['EMAIL']]), // получатель
					"TITLE" => $title,
					"MESSAGE" => $message
				];
				$event = new \CEvent;
				$result = $event->Send($eventName, $siteId, $arFields, "N", '', '');
				\CEventLog::Add(
					[
						'SEVERITY' => 'INFO', // SECURITY, ERROR, WARNING, INFO, DEBUG
						'AUDIT_TYPE_ID' => 'CPGP',
						'MODULE_ID' => 'main',
						'ITEM_ID' => $ELEMENT['ID'],
						'DESCRIPTION' => 'Обратный звонок: ' . $ELEMENT['ID'] . ' отправлен на почту:'.$ELEMENT['EMAIL'],
					]
				);
			}
			catch (SystemException $e) {
				ShowError($e->getMessage());
			}
		}
		
		return $result;
	}

	public function executeComponent()
	{
		$this->_checkModules();
		$this->_app();
		$this->_user();

		$elem = $this->insertElement();
		if (is_array($elem) && $elem['ID']) {
			$this->sendData($elem);
			//LocalRedirect("/cpgp/services/callback/med/");
		}
		$this->IncludeComponentTemplate();
	}
}
