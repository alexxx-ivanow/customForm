<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// проверка идентификатора сессии
if (!check_bitrix_sessid()) {
    return;
}

// проверяем была ли выброшена ошибка при установке, если да, то записываем её в переменную $errorException
if ($errorException = $APPLICATION->getException()) {
    // вывод сообщения об ошибке при установке модуля
    CAdminMessage::showMessage(
        Loc::getMessage('INSTALL_FAILED') . ': ' . $errorException->GetString()
    );
} else {
    // вывод уведомления при успешной установке модуля
    CAdminMessage::showNote(
        Loc::getMessage('INSTALL_SUCCESS')
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