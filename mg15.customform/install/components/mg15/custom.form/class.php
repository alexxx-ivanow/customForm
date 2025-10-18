<?php
use Bitrix\Main\HttpResponse,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Mail\Internal\EventTypeTable,
    mg15\customform\AntiSpam,
    Bitrix\Main\Loader,
    Bitrix\Main\Mail\Event;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loader::includeModule('mg15.customform');
class CustomFormComponent extends CBitrixComponent
{

    private $fields = [];
    private static $fieldPrefix = 'CF_';
    private static $eventName = 'MG15_CUSTOM_FORM_FILLING';

    public function executeComponent()
    {
        $this->arResult['FIELD_PREFIX'] = self::$fieldPrefix;
        $this->arResult['MESSAGE'] = [];
        $this->arResult['ERRORS'] = [];
        $this->arResult['EXCLUDE'] = [];
        $this->arResult['BOT_CODE'] = ($this->arParams['IS_ANTISPAM'] === 'Y') ? AntiSpam::getBotValue() : '';
        $this->arResult['IS_COMMENT'] = false;

        foreach($this->arParams['FIELDS'] as $key => $field) {
            if($field === 'COMMENT') { // если есть поле комментария
                $this->arResult['IS_COMMENT'] = true;
                $this->arResult['EXCLUDE'][] = 'COMMENT';
            }
        }

        if($this->request->isAjaxRequest()) {
            $this->manageRequest();
        }

        $this->IncludeComponentTemplate();
    }

    private function manageRequest()
    {
        // антибот
        if($this->arParams['IS_ANTISPAM'] === 'Y') {
            if(!AntiSpam::checkBots($this->request->getPost(self::$fieldPrefix . 'B_FIELD'))) {
                $this->arResult['ERRORS']['B_FIELD'] = Loc::getMessage('error_bot_field');
            }
        }

        //валидируем на пустоту и корректность
        foreach($this->arParams['FIELDS'] as $field) {
            if(!$this->request->getPost(self::$fieldPrefix . $field) && in_array($field, $this->arParams['REQUIRED'])) { // проверка
                // поля на обязательность
                $this->arResult['ERRORS'][$field] = Loc::getMessage('form_required_field', ['FIELD' => $field]);
            } elseif($this->request->getPost(self::$fieldPrefix . $field)) { // валидация заполненного поля
                $this->validateField($field);
            }
        }

        // чекбокс согласия
        if($this->arParams['IS_AGREE'] === 'Y') {
            $this->validateAgree($this->request->getPost(self::$fieldPrefix . 'AGREE'));
        }

        if(!count($this->arResult['ERRORS'])) { // если нет ошибок
            if($this->arParams['IS_SEND_EMAIL'] === 'Y') {
                $this->sendEmail();
            }

            if((int)$this->arParams['IBLOCK_ID']) {
                $this->writeToIblock();
            }

            $this->arResult['MESSAGE'][] = Loc::getMessage('form_message_success');
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
            Event::send([
                "EVENT_NAME" => self::$eventName,
                "LID" => SITE_ID,
                "C_FIELDS" => $this->fields,
            ]);
        }
    }

    private function writeToIblock()
    {
        Loader::includeModule('iblock');
        $el = new CIBlockElement;
        $arLoadProductArray = [
            "IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
            "IBLOCK_ID"      => $this->arParams['IBLOCK_ID'],
            "NAME"           => "Форма заполнена " . date('Y-m-d H:i:s'),
            "ACTIVE"         => "N",
            "PREVIEW_TEXT"   => 'ФИО: ' . $this->fields['NAME'] . PHP_EOL . 'Email: ' . $this->fields['EMAIL'] . PHP_EOL . 'Телефон: ' . $this->fields['PHONE'] . PHP_EOL . 'Комментарий: ' . $this->fields['COMMENT'],
        ];
        if(!$PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $this->arResult['ERRORS']['IBLOCK'] = Loc::getMessage('form_iblock_add_error');
        }
    }

    private function validateField($field)
    {
        if($field === 'NAME') {
            $this->fields['NAME'] = htmlspecialchars(strip_tags($this->request->getPost(self::$fieldPrefix . 'NAME')));
        }
        if($field === 'EMAIL') {
            $this->fields['EMAIL'] = $this->request->getPost(self::$fieldPrefix . 'EMAIL');
            $this->validateEmail($this->fields['EMAIL']);
        }
        if($field === 'PHONE') {
            $this->fields['PHONE'] = $this->request->getPost(self::$fieldPrefix . 'PHONE');
            $this->validatePhone($this->fields['PHONE']);
        }
        if($field === 'COMMENT') {
            $this->fields['COMMENT'] = strip_tags($this->request->getPost(self::$fieldPrefix . 'COMMENT'));
        }
    }

    private function validateAgree($agree = null)
    {
        if (!$agree || $agree !== 'Y'){
            $this->arResult['ERRORS']['AGREE'] = Loc::getMessage('validate_agree_error');
            return false;
        }
        return true;
    }

    private function validateEmail(string $email = '')
    {
        if (!preg_match("/^(?:[a-z0-9_+.-]{3,64}+@[a-z0-9_.-]{2,59}.[a-z]{2,5})$/i", $email)){
            $this->arResult['ERRORS']['EMAIL'] = Loc::getMessage('validate_email_error');
            return false;
        }
        return true;
    }

    private function validatePhone(string $phone = '')
    {
        if (!preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', $this->clearCharPhone($phone))){
            $this->arResult['ERRORS']['PHONE'] = Loc::getMessage('validate_phone_error');
            return false;
        }
        return true;
    }

    private function clearCharPhone($phone){
        return preg_replace('/[\(\) -]/', '', trim($phone));
    }

}