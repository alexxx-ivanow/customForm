<?
use Bitrix\Main\Mail\Internal\EventTypeTable,
    Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\TypeTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
    return;

//$arTypesEx = CIBlockParameters::GetIBlockTypes(["-" => " "]);

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
        "IBLOCK_TYPE_ID" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")
    ],
    'order' => [
        'SORT' => 'ASC'
    ],
    'select' => [
        'ID',
        'NAME',
    ],
]);
while($arIblock = $arIblockList->Fetch()) {
    $arIBlocks[$arIblock["ID"]] = $arIblock["NAME"];
}

// список почтовых событий
$eventsList = EventTypeTable::getList(array(
    'filter' => ['LID' => 'ru'],
    'select' => ['EVENT_NAME', 'NAME'],
    'order' => ['ID' => 'DESC']
));
$resultEventsList = [];
while($arRes = $eventsList->Fetch()) {
    $resultEventsList[$arRes["EVENT_NAME"]] = $arRes["NAME"];
}

$arComponentParameters = [
    "GROUPS" => [
        "FIELDS" => [
            "NAME" => 'Параметры формы',
            "SORT" => "100"
        ],
        "ADDITIONAL" => [
            "NAME" => 'Отправка почты',
            "SORT" => "200"
        ],
        "ADD_DATA" => [
            "NAME" => 'Сохранение данных',
            "SORT" => "300"
        ],
    ],
    "PARAMETERS" => [
        "IBLOCK_TYPE" => [
            "PARENT" => "ADD_DATA",
            "NAME" => "Тип инфоблока для данных формы",
            "TYPE" => "LIST",
            "VALUES" => $iblockTypes,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "ADD_DATA",
            "NAME" => "ID инфоблока для данных формы",
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ],
        "FIELDS" => [
            "PARENT" => "FIELDS",
            "NAME" => "Список полей для формы",
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => "5",
            "VALUES" => [
                "NAME" => "ФИО",
                "EMAIL" => "E-mail",
                "PHONE" => "Телефон",
                "COMMENT" => "Комментарий",
            ],
            //"ADDITIONAL_VALUES" => "Y",
        ],
        "REQUIRED" => [
            "PARENT" => "FIELDS",
            "NAME" => "Обязательные поля",
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => "4",
            "VALUES" => [
                "NAME" => "ФИО",
                "EMAIL" => "E-mail",
                "PHONE" => "Телефон",
                "COMMENT" => "Комментарий",
            ],
            "DEFAULT" => "",
        ],
        "IS_AGREE" => [
            "PARENT" => "FIELDS",
            "NAME" => 'Добавить чекбокс согласия',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "IS_BOOTSTRAP" => [
            "PARENT" => "FIELDS",
            "NAME" => 'Подключить Bootstrap',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "IS_ANTISPAM" => [
            "PARENT" => "FIELDS",
            "NAME" => 'Включить антиспам',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "IS_SEND_EMAIL" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => 'Отправлять письмо',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            //"REFRESH" => "Y",
        ],
        /*"EVENT_NAME" => [
            "PARENT" => "ADDITIONAL",
            "NAME" => "Почтовое событие",
            "HIDDEN" => $arCurrentValues['IS_SEND_EMAIL'] === "Y" ? "N" : "Y",
            "TYPE" => "LIST",
            "DEFAULT" => "",
            "VALUES" => $resultEventsList,
            "COLS" => "50",
        ],*/
    ],
];