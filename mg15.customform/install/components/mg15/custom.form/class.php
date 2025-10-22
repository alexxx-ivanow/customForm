<?php
use Bitrix\Main\HttpResponse,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Mail\Internal\EventTypeTable,
    mg15\customform\AntiSpam,
    mg15\customform\Validate,
    Bitrix\Main\Loader,
    Bitrix\Main\Mail\Event;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loader::includeModule('mg15.customform');
class CustomFormComponent extends CBitrixComponent
{

    private $fields = [];

    public $post = [];

    public $files = [];

    private static $fieldPrefix = 'CF_';
    private static $eventName = 'MG15_CUSTOM_FORM_FILLING';

    public function executeComponent()
    {
        // Уникальный ID формы
        $this->arParams['FORM_ID'] = "CF_" . md5($this->arParams['FORM_TITLE']);

        // инициируем arResult
        $this->arResult['FIELD_PREFIX'] = self::$fieldPrefix;
        $this->arResult['MESSAGE'] = [];
        $this->arResult['ERRORS'] = [];
        $this->arResult['ALIASES'] = [];
        $this->arResult['EXCLUDE'] = [];
        $this->arResult['BOT_CODE'] = ($this->arParams['IS_ANTISPAM'] === 'Y') ? AntiSpam::getBotValue() : '';
        $this->arResult['IS_COMMENT'] = false;

        // подключаем дефолтные стили и скрипты
        Asset::getInstance()->addCss($this->getPath() . '/lib/css/style.css');
        Asset::getInstance()->addJs($this->getPath() . '/lib/js/script.js');

        // включение маски телефона
        if($this->arParams['IS_PHONE_MASK'] === 'Y') {
            Asset::getInstance()->addJs($this->getPath() . '/lib/js/mask_phone.js');
        }

        // поключаем Bootstrap
        if($this->arParams['IS_BOOTSTRAP'] === 'Y')
            Asset::getInstance()->addCss($this->getPath() . '/lib/css/bootstrap.css');

        // предварительная санация входящих данных формы
        foreach ($this->request->getPostList()->toArray() as $key => $row) {
            $this->post[$key] = strip_tags(htmlspecialchars($row));
        }

        // обрабатываем значения параметров
        foreach($this->arParams['FIELDS'] as $key => $field) {

            // определяем названия полей (либо из параметра, лмбо из ланговых файлов)
            if(strpos($field, '==') !== false) {
                $tmpSplit = explode('==', $field);
                $this->arResult['ALIASES'][$tmpSplit[0]] = $tmpSplit[1];
                $this->arParams['FIELDS'][$key] = $tmpSplit[0];
            } else {
                $this->arResult['ALIASES'][$field] = Loc::getMessage(self::$fieldPrefix . $field . '_CAPTION');
            }

            // если в параметрах задано поле комментария
            if($field === 'COMMENT') {
                $this->arResult['IS_COMMENT'] = true;
                $this->arResult['EXCLUDE'][] = 'COMMENT';
            }

            // убираем пустые значения
            if(!$field) {
                unset($this->arParams['FIELDS'][$key]);
            } else { // если не пустое - добавляем в свойство $this->fields
                $this->fields[$this->arParams['FIELDS'][$key]] = $this->post[self::$fieldPrefix . $this->arParams['FIELDS'][$key]];
            }
        }

        // убираем пустые значения в массиве обязательных полей REQUIRED (из-за доп полей в параметрах)
        foreach($this->arParams['REQUIRED'] as $key => $field) {
            if(!$field) {
                unset($this->arParams['REQUIRED'][$key]);
            }
        }

        // сортируем основные поля по параметру FIELDS_ORDER
        $orderFieldsArr = explode(',', $this->arParams['FIELDS_ORDER']);
        $orderFieldsArrReverse = array_reverse($orderFieldsArr);
        foreach($orderFieldsArrReverse as $key => $field) {
            if(!in_array($field, $this->arParams['FIELDS'])) {
                unset($orderFieldsArrReverse[$key]);
            }
        }
        foreach($orderFieldsArrReverse as $field) {
            unset($this->arParams['FIELDS'][array_search($field, $this->arParams['FIELDS'])]);
            array_unshift($this->arParams['FIELDS'], $field);
        }

        // если приходит ajax-запрос
        if($this->request->isAjaxRequest()) {
            $this->manageRequest();
        }

        $this->IncludeComponentTemplate();
    }

    private function manageRequest()
    {

        // если несколько форм на странице
        if($this->arParams['FORM_ID'] !== $this->post[self::$fieldPrefix . 'ACTION']) return;

        // проверяем сессию
        if(!check_bitrix_sessid()) {
            $this->arResult['ERRORS']['RESULT'] = Loc::getMessage('FORM_SESSION_ERROR');
        }

        // антибот
        if($this->arParams['IS_ANTISPAM'] === 'Y') {
            if(!AntiSpam::checkBots($this->post[self::$fieldPrefix . 'B_FIELD'])) {
                $this->arResult['ERRORS']['B_FIELD'] = Loc::getMessage('ERROR_BOT_FIELD');
            }
        }

        // если включен чекбокс согласия с политикой конфиденциальности
        if($this->arParams['IS_POLITICS'] === 'Y') {
            $this->arParams['FIELDS'][] = 'POLITICS';
        }

        // если включен чекбокс согласия с обработкой персональных данных
        if($this->arParams['IS_AGREE'] === 'Y') {
            $this->arParams['FIELDS'][] = 'AGREE';
        }

        //валидируем поля формы на пустоту и корректность
        foreach($this->arParams['FIELDS'] as $field) {
            // проверка поля на обязательность
            if(in_array($field, $this->arParams['REQUIRED']) && !$this->post[self::$fieldPrefix . $field]) {
                $this->arResult['ERRORS'][$field] = Loc::getMessage('FORM_REQUIRED_FIELD', ['FIELD' =>
                    $this->arResult['ALIASES'][$field]]);
            } else { // валидация заполненного поля
                if(Validate::validateField($field, $this->post[self::$fieldPrefix . $field]) === false) {
                    $this->arResult['ERRORS'][$field] = Loc::getMessage('ERROR_VALIDATE_FIELD_' . $field);
                }
            }
        }

        //$this->arResult['FILES'] = $_FILES;

        // работа с файлами
        if($this->arParams['IS_FILE'] === 'Y') {
            if(is_array($_FILES) && count($_FILES)) {
                foreach ($_FILES as $file){
                    if (!empty($file['tmp_name'])) {
                        // проверка файла по типу
                        if(count($this->arParams['FILE_TYPE'])) {
                            if(is_array($this->arParams['FILE_TYPE']) && !in_array($file['type'], $this->arParams['FILE_TYPE'])) {
                                $this->arResult['ERRORS']['FILE'] = Loc::getMessage('ERROR_VALIDATE_FIELD_FILE_TYPE');
                                continue;
                            }
                        }

                        // проверка файла по размеру
                        if((int)$this->arParams['FILE_SIZE'] && (intval($file['size']) > intval($this->arParams['FILE_SIZE'] * 1048 * 1048))) {
                            $this->arResult['ERRORS']['FILE'] = Loc::getMessage('ERROR_VALIDATE_FIELD_FILE_SIZE');
                            continue;
                        }
                        $this->files[] = CFile::SaveFile($file, 'cf_files');
                    } elseif($this->arParams['FILE_REQUIRED'] === 'Y') { // проверка на пустоту, если задан параметр
                        $this->arResult['ERRORS']['FILE'] = Loc::getMessage('FORM_REQUIRED_FIELD', ['FIELD' => 'файл']);
                    }
                }
            }
        }

        if(!count($this->arResult['ERRORS'])) { // если нет ошибок
            if($this->arParams['IS_SEND_EMAIL'] === 'Y') {
                $this->sendEmail(); // отправляем Email
            }

            if((int)$this->arParams['IBLOCK_ID']) {
                $this->writeToIblock(); // делаем запись в инфоблок
            }

            $this->arResult['MESSAGE'][] = $this->arParams['SUCCESS_TEXT'] ?: Loc::getMessage('FORM_MESSAGE_SUCCESS');
        }

        $this->sendJsonResponse($this->arResult);
    }

    private function sendJsonResponse($data)
    {
        // Очищаем весь буфер
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $response = new HttpResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->setContent(json_encode($data, JSON_UNESCAPED_UNICODE));
        $response->send();
    }

    private function sendEmail()
    {
        $event = EventTypeTable::getList([
            'filter' => ['EVENT_NAME' => self::$eventName],
            'select' => ['ID']
        ])->fetch();

        if(!empty($event)) {
            $data = $this->fields;

            // создаем макрос для общего поля почтового шаблона
            $totalMessage = '';
            foreach($data as $key => $field) {
                if(array_key_exists($key, $this->arResult['ALIASES'])) {
                    $totalMessage .= $this->arResult['ALIASES'][$key] . ': ' . $field . PHP_EOL;
                } else {
                    $totalMessage .= $key . ': ' . $field . PHP_EOL;
                }

            }
            $data['MESSAGE'] = $totalMessage;

            // добавляем название формы
            $data['FORM_TITLE'] = $this->arParams['FORM_TITLE'] ? '"' . $this->arParams['FORM_TITLE'] . '"' : '';
            Event::send([
                "EVENT_NAME" => $this->arParams['EVENT_NAME'] ?: self::$eventName,
                "LID" => SITE_ID,
                "C_FIELDS" => $data,
                "FILE" => $this->files,
            ]);
        }
    }

    private function writeToIblock()
    {
        Loader::includeModule('iblock');
        $el = new CIBlockElement;

        $messageArr = [];
        foreach($this->fields as $key => $field) {
            $messageArr[] = $this->arResult['ALIASES'][$key] . ': ' . $field;
        }

        $PROP = [];
        // грузим файл в инфоблок
        if($this->arParams['IS_FILE'] && count($this->files) && $this->arParams['FILE_FIELD_CODE']) {
            foreach($this->files as $key => $file) {
                $PROP[$this->arParams['FILE_FIELD_CODE']] = $file;
            }
        }

        $formTitle = ($this->arParams['FORM_TITLE']) ? ('[ ' . $this->arParams['FORM_TITLE'] . ' ]') : '';
        $arLoadProductArray = [
            "IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
            "IBLOCK_ID"      => $this->arParams['IBLOCK_ID'],
            "NAME"           => "Форма " . $formTitle . " заполнена " . date('Y-m-d H:i:s'),
            "ACTIVE"         => "N",
            "PROPERTY_VALUES"=> $PROP,
            "PREVIEW_TEXT"   => implode(PHP_EOL, $messageArr),
        ];
        if(!$el->Add($arLoadProductArray)) {
            $this->arResult['ERRORS']['IBLOCK'] = Loc::getMessage('FORM_IBLOCK_ADD_ERROR');
        }
    }
}