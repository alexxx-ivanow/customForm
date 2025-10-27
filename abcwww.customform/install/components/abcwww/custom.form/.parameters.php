<?

use Bitrix\Main\Mail\Internal\EventTypeTable,
    Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\TypeTable,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Web\Json;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
    return;

CBitrixComponent::includeComponentClass('bitrix:catalog.element');

// типы инфоблоков
$types = TypeTable::getList([
    'select' => ['ID', 'NAME' => 'TYPE_LANG.NAME'],
    'order' => ['SORT' => 'ASC'],
    'runtime' => [
        'TYPE_LANG' => [
            'data_type' => \Bitrix\Iblock\TypeLanguageTable::class,
            'reference' => [
                '=this.ID' => 'ref.IBLOCK_TYPE_ID',
            ]
        ],
    ],
])->fetchAll();
$iblockTypes = ["-" => " "];
foreach ($types as $type) {
    $iblockTypes[$type['ID']] = '[' . $type['ID'] . '] ' . $type['NAME'];
}

// инфоблоки для типа
$arIBlocks = [];
$arIblockList = IblockTable::getList([
    'filter' => [
        "IBLOCK_TYPE_ID" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")
    ],
    'order' => [
        'SORT' => 'ASC'
    ],
    'select' => [
        'ID',
        'NAME',
    ],
]);
while ($arIblock = $arIblockList->Fetch()) {
    $arIBlocks[$arIblock["ID"]] = $arIblock["NAME"];
}

// список почтовых событий
$eventsList = EventTypeTable::getList(array(
    'filter' => ['LID' => 'ru'],
    'select' => ['EVENT_NAME', 'NAME'],
    'order' => ['ID' => 'DESC']
));
$resultEventsList = [];
while ($arRes = $eventsList->Fetch()) {
    $resultEventsList[$arRes["EVENT_NAME"]] = $arRes["NAME"];
}

$arComponentParameters = [
    "GROUPS" => [
        "FIELDS" => [
            "NAME" => Loc::getMessage('GROUPS_FIELDS_NAME'),
            "SORT" => "100"
        ],
        "ADDITIONAL" => [
            "NAME" => Loc::getMessage('GROUPS_ADDITIONAL_NAME'),
            "SORT" => "200"
        ],
        "ADD_DATA" => [
            "NAME" => Loc::getMessage('GROUPS_ADD_DATA_NAME'),
            "SORT" => "300"
        ],
        "AGREE" => [
            "NAME" => Loc::getMessage('GROUPS_AGREE_NAME'),
            "SORT" => "180"
        ],
        "FILE_DATA" => [
            "NAME" => Loc::getMessage('GROUPS_FILE_DATA_NAME'),
            "SORT" => "150"
        ],
    ],
    "PARAMETERS" => [
        "IBLOCK_TYPE" => [
            "PARENT" => "ADD_DATA",
            "NAME" => Loc::getMessage('IBLOCK_TYPE_NAME'),
            "TYPE" => "LIST",
            "VALUES" => $iblockTypes,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "ADD_DATA",
            "NAME" => Loc::getMessage('IBLOCK_ID_NAME'),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ],
        "FORM_TITLE" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('FORM_TITLE_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        "FIELDS" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('FIELDS_NAME'),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => "5",
            "VALUES" => [
                "NAME" => "ФИО",
                "EMAIL" => "E-mail",
                "PHONE" => "Телефон",
                "COMMENT" => "Комментарий",
            ],
            "DEFAULT" => ["NAME","EMAIL","PHONE","COMMENT"],
            "ADDITIONAL_VALUES" => "Y",
        ],
        "REQUIRED" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('REQUIRED_NAME'),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => "5",
            "VALUES" => [
                "NAME" => "ФИО",
                "EMAIL" => "E-mail",
                "PHONE" => "Телефон",
                "COMMENT" => "Комментарий",
            ],
            "DEFAULT" => ["NAME","EMAIL","PHONE"],
            "ADDITIONAL_VALUES" => "Y",
        ],
        "FIELDS_ORDER" => [
            'PARENT' => 'FIELDS',
            'NAME' => Loc::getMessage('FIELDS_ORDER_NAME'),
            'TYPE' => 'CUSTOM',
            'JS_FILE' => CatalogElementComponent::getSettingsScript($componentPath, 'dragdrop_order'),
            'JS_EVENT' => 'initDraggableOrderControl',
            'JS_DATA' => Json::encode([
                "NAME" => "ФИО",
                "EMAIL" => "E-mail",
                "PHONE" => "Телефон",
                "COMMENT" => "Комментарий",
            ]),
            "DEFAULT" => "NAME,EMAIL,PHONE,COMMENT",
        ],
        "FIELD_COMMENT_TO_END" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('FIELD_COMMENT_TO_END_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "IS_PHONE_MASK" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('IS_PHONE_MASK_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "BUTTON_TEXT" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('BUTTON_TEXT_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "Отправить",
        ],
        "SUCCESS_TEXT" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('SUCCESS_TEXT_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "Спасибо! Ваше сообщение отправлено.",
        ],
        "ERROR_TEXT" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('ERROR_TEXT_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "Ошибка при отправке формы",
        ],
        "IS_FILE" => [
            "PARENT" => "FILE_DATA",
            "NAME" => Loc::getMessage('IS_FILE_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],
        "FILE_REQUIRED" => [
            "PARENT" => "FILE_DATA",
            "NAME" => Loc::getMessage('FILE_REQUIRED_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "",
            "HIDDEN" => $arCurrentValues['IS_FILE'] === "Y" ? "N" : "Y",
        ],
        "FILE_SIZE" => [
            "PARENT" => "FILE_DATA",
            "NAME" => Loc::getMessage('FILE_SIZE_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "10",
            "HIDDEN" => $arCurrentValues['IS_FILE'] === "Y" ? "N" : "Y",
        ],
        "FILE_TYPE" => [
            "PARENT" => "FILE_DATA",
            "NAME" => Loc::getMessage('FILE_TYPE_NAME'),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => "5",
            "VALUES" => [
                "image/jpeg" => "JPG",
                "image/png" => "PNG",
                "application/pdf" => "PDF",
                "text/plain" => "TXT",
                "application/vnd.ms-excel" => "XLS",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => "XLSX",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => "DOCX",
            ],
            "DEFAULT" => "",
            "HIDDEN" => $arCurrentValues['IS_FILE'] === "Y" ? "N" : "Y",
        ],
        "FILE_FIELD_CODE" => [
            "PARENT" => "FILE_DATA",
            "NAME" => Loc::getMessage('FILE_FIELD_CODE_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "HIDDEN" => $arCurrentValues['IS_FILE'] === "Y" ? "N" : "Y",
        ],

        "IS_POLITICS" => [
            "PARENT" => "AGREE",
            "NAME" => Loc::getMessage('IS_POLITICS_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],
        "POLITICS_TEXT" => [
            "PARENT" => "AGREE",
            "NAME" => Loc::getMessage('POLITICS_TEXT_NAME'),
            "HIDDEN" => $arCurrentValues['IS_POLITICS'] === "Y" ? "N" : "Y",
            "TYPE" => "STRING",
            "DEFAULT" => "Согласен на обработку политики конфиденциальности"
        ],
        "IS_AGREE" => [
            "PARENT" => "AGREE",
            "NAME" => Loc::getMessage('IS_AGREE_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],
        "AGREE_TEXT" => [
            "PARENT" => "AGREE",
            "NAME" => Loc::getMessage('AGREE_TEXT_NAME'),
            "HIDDEN" => $arCurrentValues['IS_AGREE'] === "Y" ? "N" : "Y",
            "TYPE" => "STRING",
            "DEFAULT" => "Согласен на обработку персональных данных"
        ],
        "IS_BOOTSTRAP" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('IS_BOOTSTRAP_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "IS_ANTISPAM" => [
            "PARENT" => "FIELDS",
            "NAME" => Loc::getMessage('IS_ANTISPAM_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "IS_SEND_EMAIL" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => Loc::getMessage('IS_SEND_EMAIL_NAME'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ],
        "EVENT_NAME" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => Loc::getMessage('EVENT_NAME_NAME'),
            "HIDDEN" => $arCurrentValues['IS_SEND_EMAIL'] === "Y" ? "N" : "Y",
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ],
        "EMAIL_TO" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => Loc::getMessage('EMAIL_TO_NAME'),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "HIDDEN" => $arCurrentValues['IS_SEND_EMAIL'] === "Y" ? "N" : "Y",
        ],
    ],
];