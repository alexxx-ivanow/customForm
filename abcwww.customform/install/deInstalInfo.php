<?
// пространство имен для подключений ланговых файлов
use \Bitrix\Main\Localization\Loc;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// проверка идентификатора сессии
if (!check_bitrix_sessid()) {
    return;
}

// метод возвращает объект класса CApplicationException, содержащий последнее исключение
if ($errorException = $APPLICATION->getException()) {
    // вывод сообщения об ошибке при удалении модуля
    CAdminMessage::showMessage(
        Loc::getMessage('DEINSTALL_FAILED') . ': ' . $errorException->GetString()
    );
} else {
    // вывод уведомления при успешном удалении модуля
    CAdminMessage::showNote(
        Loc::getMessage('DEINSTALL_SUCCESS')
    );
}
?>

<!-- выводим кнопку для перехода на страницу модулей, мы и так находимся на этой странице но с выведенным файлом, значит просто получаем текущую директорию для перенаправления -->
<form action="<?= $APPLICATION->GetCurPage() ?>">
    <!-- в форме обязательно должно быть поле lang, с айди языка, чтобы язык не сбросился -->
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <!-- MOD_BACK - системная языковая переменная для возврата -->
    <input type="submit" name="" value="<?= Loc::getMessage("MOD_BACK") ?>">
</form>