<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;

// глобальная переменная $APPLICATION
global $APPLICATION;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// получим права доступа текущего пользователя на модуль
$MODULE_RIGHT = $APPLICATION->GetGroupRight('hmarketing.d7');

// если нет прав, не будем показывать модуль в админке
if ($MODULE_RIGHT > "D") {
    // сформируем верхний пункт меню
    $aMenu = array(
        // пункт меню в разделе Контент
        'parent_menu' => 'global_menu_services',
        // сортировка
        'sort' => 1,
        // название пункта меню
        'text' => "Модули Эйч Маркетинг",
        // идентификатор ветви
        "items_id" => "menu_webforms",
        // иконка
        "icon" => "form_menu_icon",
    );

    // дочерния ветка меню
    $aMenu["items"][] =  array(
        // название подпункта меню
        'text' => 'Страница модуля',
        // ссылка для перехода
        'url' => 'hmarketing.php?lang=' . LANGUAGE_ID
    );

    // дочерния ветка меню
    $aMenu["items"][] =  array(
        // название подпункта меню
        'text' => 'Админка модуля',
        // ссылка для перехода
        'url' => 'settings.php?lang=ru&mid=hmarketing.d7'
    );

    // возвращаем основной массив $aMenu
    return $aMenu;
} else {
    return false;
}