<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для управления (регистрации/удалении) модуля в системе/базе
use Bitrix\Main\ModuleManager;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
// пространство имен с абстрактным классом для любых приложений, любой конкретный класс приложения является наследником этого абстрактного класса
use Bitrix\Main\Application;
// пространство имен для работы c ORM
use \Bitrix\Main\Entity\Base;
// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;
// пространство имен для событий
use \Bitrix\Main\EventManager;
// пространство имен для почтовых событий
use mg15\customform\Events;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

class Mg15_Customform extends CModule
{
    // переменные модуля
    public  $MODULE_ID;
    public  $MODULE_SHORT_ID;
    public  $MODULE_VERSION;
    public  $MODULE_VERSION_DATE;
    public  $MODULE_NAME;
    public  $MODULE_DESCRIPTION;
    public  $PARTNER_NAME;
    public  $PARTNER_URI;
    public  $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public  $MODULE_GROUP_RIGHTS;
    public  $errors;

    // конструктор класса, вызывается автоматически при обращение к классу
    function __construct()
    {
        // создаем пустой массив для файла version.php
        $arModuleVersion = array();
        // подключаем файл version.php
        include_once(__DIR__ . '/version.php');

        // версия модуля
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        // дата релиза версии модуля
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        // id модуля
        $this->MODULE_ID = "mg15.customform";
        // ID вендора
        $this->MODULE_SHORT_ID = "mg15";
        // название модуля
        $this->MODULE_NAME = "Кастомная форма обратной связи";
        // описание модуля
        $this->MODULE_DESCRIPTION = "Форма обратной связи с отправкой через ajax";
        // имя партнера выпустившего модуль
        $this->PARTNER_NAME = "АБВ сайт";
        // ссылка на рисурс партнера выпустившего модуль
        $this->PARTNER_URI = "https://www.abcwww.ru";
        // если указано, то на странице прав доступа будут показаны администраторы и группы
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        // если указано, то на странице редактирования групп будет отображаться этот модуль
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    // метод отрабатывает при установке модуля
    function DoInstall()
    {
        //*************************************//
        // Пример с установкой в один шаг      //
        //*************************************//

        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // регистрируем модуль в системе
        ModuleManager::RegisterModule("mg15.customform");
        // создаем таблицы баз данных, необходимые для работы модуля
        //$this->InstallDB();
        // создаем первую и единственную запись в БД
        //$this->addData();
        // регистрируем обработчики событий
        //$this->InstallEvents();
        $this->InstallEmailEvents();
        // копируем файлы, необходимые для работы модуля
        $this->InstallFiles();
        // устанавливаем агента
        //$this->installAgents();
        // подключаем скрипт с административным прологом и эпилогом
        $APPLICATION->includeAdminFile(
            Loc::getMessage('INSTALL_TITLE'),
            __DIR__ . '/instalInfo.php'
        );

        //*************************************//
        // Пример с установкой в два шага      //
        //*************************************//

        // получаем контекст и из него запросы
        /*$context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // проверяем какой сейчас шаг, если он не существует или меньше 2, то выводим первый шаг установки
        if ($request["step"] < 2) {
            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('INSTALL_TITLE_STEP_1'),
                __DIR__ . '/instalInfo-step1.php'
            );
        }
        // проверяем какой сейчас шаг, усли 2, производим установку
        if ($request["step"] == 2) {
            // регистрируем модуль в системе
            ModuleManager::RegisterModule("hmarketing.d7");
            // создаем таблицы баз данных, необходимые для работы модуля
            $this->InstallDB();
            // регистрируем обработчики событий
            $this->InstallEvents();
            // копируем файлы, необходимые для работы модуля
            $this->InstallFiles();
            // устанавливаем агента
            $this->installAgents();
            // проверяим ответ формы введеный пользователем на первом шаге
            if ($request["add_data"] == "Y") {
                // создаем первую и единственную запись в БД
                $this->addData();
            }
            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('INSTALL_TITLE_STEP_2'),
                __DIR__ . '/instalInfo-step2.php'
            );
        }*/

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод отрабатывает при удалении модуля
    function DoUninstall()
    {
        //*************************************//
        // Пример с удалением в один шаг       //
        //*************************************//

        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // удаляем таблицы баз данных, необходимые для работы модуля
        //$this->UnInstallDB();
        // удаляем обработчики событий
        //$this->UnInstallEvents();
        $this->UnInstallEmailEvents();
        // удаляем файлы, необходимые для работы модуля
        $this->UnInstallFiles();
        // удаляем агента
        //$this->unInstallAgents();
        // удаляем регистрацию модуля в системе
        ModuleManager::UnRegisterModule("mg15.customform");
        // подключаем скрипт с административным прологом и эпилогом
        $APPLICATION->includeAdminFile(
            Loc::getMessage('DEINSTALL_TITLE'),
            __DIR__ . '/deInstalInfo.php'
        );

        //*************************************//
        // Пример с удалением в два шага       //
        //*************************************//

        // получаем контекст и из него запросы
        /*$context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // проверяем какой сейчас шаг, если он не существует или меньше 2, то выводим первый шаг удаления
        if ($request["step"] < 2) {
            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('DEINSTALL_TITLE_1'),
                __DIR__ . '/deInstalInfo-step1.php'
            );
        }
        // проверяем какой сейчас шаг, усли 2, производим удаление
        if ($request["step"] == 2) {
            // удаляем таблицы баз данных, необходимые для работы модуля
            //$this->UnInstallDB();
            // проверяим ответ формы введеный пользователем на первом шаге
            if ($request["save_data"] == "Y") {
                // удаляем таблицы баз данных, необходимые для работы модуля
                $this->UnInstallDB();
            }

            // удаляем обработчики событий
            $this->UnInstallEvents();
            // удаляем файлы, необходимые для работы модуля
            $this->UnInstallFiles();
            // удаляем агента
            $this->unInstallAgents();
            // удаляем регистрацию модуля в системе
            ModuleManager::UnRegisterModule("hmarketing.d7");

            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('DEINSTALL_TITLE_2'),
                __DIR__ . '/deInstalInfo-step2.php'
            );
        }*/

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для создания таблицы баз данных
    function InstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Hmarketing\d7\DataTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\mg15\customform\DataTable")->createDbTable();
        }

        if (!Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Hmarketing\d7\AuthorTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            Base::getInstance("\mg15\customform\AuthorTable")->createDbTable();
        }
    }

    // метод для удаления таблицы баз данных
    function UnInstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        Application::getConnection(\mg15\customform\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\mg15\customform\DataTable")->getDBTableName());

        Application::getConnection(\mg15\customform\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\mg15\customform\AuthorTable")->getDBTableName());

        // удаляем параметры модуля из базы данных битрикс
        Option::delete($this->MODULE_ID);
    }

    // метод для установки почтовых событий и шаблонов
    function InstallEmailEvents()
    {
        Loader::includeModule($this->MODULE_ID);
        Events::InstallEvents();
        Events::InstallTemplates();
    }

    // метод для удаления почтовых событий и шаблонов
    function UnInstallEmailEvents()
    {
        Loader::includeModule($this->MODULE_ID);
        Events::UnInstallEvents();
    }

    // метод для создания обработчика событий
    function InstallEvents()
    {
        // для произвольной работы
        EventManager::getInstance()->registerEventHandler(
            // идентификатор модуля-источника события
            $this->MODULE_ID,
            // событие на которое мы подписываемся, OnSomeEvent для произвольной работы
            "OnSomeEvent",
            // идентификатор модуля, который подписывается
            $this->MODULE_ID,
            // класс выполняющий обработку (для callback-обработчика, если файловый - пустая строка)
            "\mg15\customform\Main",
            // метод класса выполняющий обработку (для callback-обработчика, если файловый - пустая строка)
            'get'
        );

        // для работы с ORM, есть три типа событий: onBefore<Action> - перед вызовом запроса (можно изменить входные параметры), после следуют валидаторы. on<Action> - уже нельзя изменить входные параметры, после выполняется SQL-запрос. onAfter<Action> - после выполнения операции, операция уже совершена
        // три события <Action> итого 9 событий: Add, Update, Delete
        EventManager::getInstance()->registerEventHandler(
            // идентификатор модуля, для которого регистрируется событие
            $this->MODULE_ID,
            // тип события, класс называется DataTable, но должно передаваться по имени файла, то есть просто Data
            "\mg15\customform\Data::OnBeforeUpdate",
            // идентификатор модуля к которому относится регистрируемый обработчик, из какого модуля берется класс, нужно если необходимо связать 2 модуля, если используем один, то дублируем поле с первым
            $this->MODULE_ID,
            // класс обработчика
            "\mg15\customform\Events",
            // метод обработчика
            'eventHandler'
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для удаления обработчика событий
    function UnInstallEvents()
    {
        // удаление событий, аналогично установке
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "OnSomeEvent",
            $this->MODULE_ID,
            "\mg15\customform\Main",
            'get'
        );

        // удаление событий, аналогично установке
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "\mg15\customform\Data::OnBeforeUpdate",
            $this->MODULE_ID,
            "\mg15\customform\Events",
            'eventHandler'
        );

        // для успешного завершения, метод должен вернуть true
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
            $_SERVER["DOCUMENT_ROOT"] . "/local/components",
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

        // для успешного завершения, метод должен вернуть true
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
        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/components/" . $this->MODULE_SHORT_ID)) {
            // удаляет папку из указанной директории, функция работает рекурсивно
            DeleteDirFilesEx(
                "/local/components/" . $this->MODULE_SHORT_ID
            );
        }

        // удалим файлы страниц, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/files",
            $_SERVER["DOCUMENT_ROOT"] . "/"
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // заполнение таблиц тестовыми данными
    function addData()
    {
        // подключаем модуль для видимости ORM класса
        Loader::includeModule($this->MODULE_ID);

        // добавляем запись в таблицу БД
        \mg15\customform\DataTable::add(
            array(
                "ACTIVE" => "N",
                "SITE" => '["s1"]',
                "LINK" => " ",
                "LINK_PICTURE" => "/local/components/mg15.customform/popup.baner/templates/.default/img/banner.jpg",
                "ALT_PICTURE" => " ",
                "EXCEPTIONS" => " ",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
                "TARGET" =>  "self",
                "AUTHOR_ID" =>  "1",
            )
        );

        // добавляем запись в таблицу БД
        \mg15\customform\AuthorTable::add(
            array(
                "NAME" => "Иван",
                "LAST_NAME" => "Иванов",
            )
        );

        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // установка агентов
    function installAgents()
    {
        \CAgent::AddAgent(
            // строка PHP для запуска агента-функции
            "\mg15\customform\Agent::superAgent();",
            // идентификатор модуля, необходим для подключения файлов модуля (необязательный) 
            $this->MODULE_ID,
            // период, нужен для агентов, которые должны выполняться точно в срок. Если агент пропустил запуск, то он сделает его столько раз, сколько он пропустил. Если значение N, то агент после первого запуска будет запускаться с заданным интервалам (необязательный, по умолчанию N)                   
            "N",
            // интервал в секундах (необязательный, по умолчанию 86400 (сутки))                                
            120,
            // дата первой проверки (необязательный, по умолчанию текущее время)
            "",
            // активность агента (необязательный, по умолчанию Y) 
            "Y",
            // дата первого запуска (необязательный, по умолчанию текущее время)
            "",
            // сортировка (влияет на порядок выполнения агентов (очередность), для тех, которые запускаются в одно время) (необязательный, по умолчанию 100)  
            100
        );
    }

    // удаление агентов
    function unInstallAgents()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}
