<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$MESS['GROUPS_FIELDS_NAME'] = 'Параметры формы';
$MESS['GROUPS_ADDITIONAL_NAME'] = 'Отправка почты';
$MESS['GROUPS_ADD_DATA_NAME'] = 'Сохранение данных';
$MESS['GROUPS_AGREE_NAME'] = 'Согласие';
$MESS['GROUPS_FILE_DATA_NAME'] = 'Отправка файла';

$MESS['IBLOCK_TYPE_NAME'] = 'Тип инфоблока для данных формы';
$MESS['IBLOCK_ID_NAME'] = 'ID инфоблока для данных формы';
$MESS['FORM_TITLE_NAME'] = 'Название формы (обязательно уникальное для нескольких форм на странице)';
$MESS['FIELDS_NAME'] = 'Список полей для формы';
$MESS['REQUIRED_NAME'] = 'Обязательные поля';
$MESS['FIELDS_ORDER_NAME'] = 'Сортировка полей';
$MESS['FIELD_COMMENT_TO_END_NAME'] = 'Вывести поле комментария после остальных полей';
$MESS['IS_PHONE_MASK_NAME'] = 'Включить маску для поля телефона';
$MESS['SUCCESS_TEXT_NAME'] = 'Сообщение об успешной отправке';
$MESS['ERROR_TEXT_NAME'] = 'Сообщение при ошибке отправки формы';
$MESS['IS_FILE_NAME'] = 'Добавить поле отправки файла';
$MESS['FILE_REQUIRED_NAME'] = 'Поле файла обязательно';
$MESS['FILE_SIZE_NAME'] = 'Размер загружаемого файла, Мб';
$MESS['FILE_TYPE_NAME'] = 'Тип загружаемого файла';

$MESS['FILE_FIELD_CODE_NAME'] = 'Код свойства инфоблока для сохранения файла';
$MESS['IS_POLITICS_NAME'] = 'Добавить чекбокс согласия с политикой конфиденциальности';
$MESS['POLITICS_TEXT_NAME'] = 'Текст согласия с политикой конфиденциальности';
$MESS['IS_AGREE_NAME'] = 'Добавить чекбокс согласия с обработкой персональных данных';
$MESS['AGREE_TEXT_NAME'] = 'Текст согласия с обработкой персональных данных';
$MESS['IS_BOOTSTRAP_NAME'] = 'Подключить Bootstrap 5';
$MESS['IS_ANTISPAM_NAME'] = 'Включить антиспам';
$MESS['IS_SEND_EMAIL_NAME'] = 'Отправлять письмо';
$MESS['EVENT_NAME_NAME'] = 'Почтовое событие вместо установленного модулем';
$MESS['EMAIL_TO_NAME'] = 'Куда отправлять письмо';


$MESS["IBLOCK_TYPE_TIP"] = 'Из выпадающего списка выбирается один из созданных в системе типов инфоблоков. После 
нажатия кнопки <b><i>ок</i></b> будут подгружены инфоблоки, созданные для выбранного типа.';

$MESS['EVENT_NAME_TIP'] = 'По умолчанию ставится событие ABCWWW_CUSTOM_FORM_FILLING';
$MESS['FILE_FIELD_CODE_TIP'] = 'Свойство инфоблока должно иметь тип файл и допустимые типы файлов для сохранения';
$MESS['IS_ANTISPAM_TIP'] = 'Добавляется проверка ботов по наличию js и сохранению сессии';
$MESS['AGREE_TEXT_TIP'] = "Пример: 'Подтверждаю своё согласие на обработку персональных данных'. Можно использовать html-теги.";
$MESS['POLITICS_TEXT_TIP'] = "Пример: 'Подтверждаю своё согласие на обработку политики конфиденциальности'. Можно использовать html-теги.";