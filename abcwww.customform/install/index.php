<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Loader;
use \Bitrix\Main\EventManager;
use abcwww\customform\Events;

Loc::loadMessages(__FILE__);

class abcwww_customform extends CModule
{
    // переменные модуля
    public $MODULE_ID;
    public $MODULE_SHORT_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public $MODULE_GROUP_RIGHTS;
    public $errors;

    function __construct()
    {
        // создаем пустой массив для файла version.php
        $arModuleVersion = [];
        // подключаем файл version.php
        include_once(__DIR__ . '/version.php');

        // версия модуля
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        // дата релиза версии модуля
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        // id модуля
        $this->MODULE_ID = "abcwww.customform";
        // ID вендора
        $this->MODULE_SHORT_ID = "abcwww";
        // название модуля
        $this->MODULE_NAME = "Кастомная форма обратной связи";
        // описание модуля
        $this->MODULE_DESCRIPTION = "Форма обратной связи с отправкой через ajax";
        // имя партнера выпустившего модуль
        $this->PARTNER_NAME = "АБВ сайт";
        // ссылка на ресурс партнера выпустившего модуль
        $this->PARTNER_URI = "https://www.abcwww.ru";
        // если указано, то на странице прав доступа будут показаны администраторы и группы
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        // если указано, то на странице редактирования групп будет отображаться этот модуль
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    function DoInstall()
    {
        global $APPLICATION;
        // регистрируем модуль в системе
        ModuleManager::RegisterModule("abcwww.customform");
        // создаем таблицы баз данных, необходимые для работы модуля
        //$this->InstallDB();
        // создаем первую и единственную запись в БД
        //$this->addData();
        // регистрируем обработчики событий
        $this->InstallEmailEvents();
        // копируем файлы, необходимые для работы модуля
        $this->InstallFiles();
        // подключаем скрипт с административным прологом и эпилогом
        $APPLICATION->includeAdminFile(
            Loc::getMessage('INSTALL_TITLE'),
            __DIR__ . '/instalInfo.php'
        );
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION;
        // удаляем таблицы баз данных, необходимые для работы модуля
        //$this->UnInstallDB();
        // удаляем обработчики событий
        $this->UnInstallEmailEvents();
        // удаляем файлы, необходимые для работы модуля
        $this->UnInstallFiles();
        // удаляем регистрацию модуля в системе
        ModuleManager::UnRegisterModule("abcwww.customform");
        // подключаем скрипт с административным прологом и эпилогом
        $APPLICATION->includeAdminFile(
            Loc::getMessage('DEINSTALL_TITLE'),
            __DIR__ . '/deInstalInfo.php'
        );
        return true;
    }

    // метод для создания таблицы баз данных
    function InstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\abcwww\customform\DataTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\abcwww\customform\DataTable")->createDbTable();
        }

        if (!Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\abcwww\d7\customform")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\abcwww\customform\AuthorTable")->createDbTable();
        }
    }

    // метод для удаления таблицы баз данных
    function UnInstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\abcwww\customform\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\abcwww\customform\DataTable")->getDBTableName());

        Application::getConnection(\abcwww\customform\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\abcwww\customform\AuthorTable")->getDBTableName());

        // удаляем параметры модуля из базы данных битрикс
        Option::delete($this->MODULE_ID);
    }

    // метод для установки почтовых событий и шаблонов
    function InstallEmailEvents()
    {
        Loader::includeModule($this->MODULE_ID);
        Events::InstallEvents();
        Events::InstallTemplates();
        return true;
    }

    // метод для удаления почтовых событий и шаблонов
    function UnInstallEmailEvents()
    {
        Loader::includeModule($this->MODULE_ID);
        Events::UnInstallEvents();
        return true;
    }

    // метод для копирования файлов модуля при установке
    function InstallFiles()
    {
        // скопируем файлы на страницы админки из папки в битрикс, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );

        // скопируем компоненты из папки в битрикс, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . "/components",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );

        // копируем файлы страниц, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . '/files',
            $_SERVER["DOCUMENT_ROOT"] . '/',
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );
        return true;
    }

    // метод для удаления файлов модуля при удалении
    function UnInstallFiles()
    {
        // удалим файлы из папки в битрикс на страницы админки, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );

        // удалим компонент из папки в битрикс 
        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/" . $this->MODULE_SHORT_ID)) {
            // удаляет папку из указанной директории, функция работает рекурсивно
            DeleteDirFilesEx(
                "/bitrix/components/" . $this->MODULE_SHORT_ID
            );
        }

        // удалим файлы страниц, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/files",
            $_SERVER["DOCUMENT_ROOT"] . "/"
        );
        return true;
    }

    // заполнение таблиц тестовыми данными
    function addData()
    {
        // подключаем модуль для видимости ORM класса
        Loader::includeModule($this->MODULE_ID);

        // добавляем запись в таблицу БД
        \abcwww\customform\DataTable::add(
            array(
                "ACTIVE" => "N",
                "SITE" => '["s1"]',
                "LINK" => " ",
                "LINK_PICTURE" => "/bitrix/components/abcwww.customform/popup.baner/templates/.default/img/banner.jpg",
                "ALT_PICTURE" => " ",
                "EXCEPTIONS" => " ",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
                "TARGET" => "self",
                "AUTHOR_ID" => "1",
            )
        );

        // добавляем запись в таблицу БД
        \abcwww\customform\AuthorTable::add(
            array(
                "NAME" => "Иван",
                "LAST_NAME" => "Иванов",
            )
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }
}
?>