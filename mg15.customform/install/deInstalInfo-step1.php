<?
// пространство имен для подключений ланговых файлов
use \Bitrix\Main\Localization\Loc;

// проверка идентификатора сессии
if (!check_bitrix_sessid()) {
    return;
}
?>

<form action="<?= $APPLICATION->GetCurPage() ?>">
    <!-- обязательное получение сессии -->
    <?= bitrix_sessid_post() ?>
    <!-- в форме обязательно должно быть поле lang, с айди языка, чтобы язык не сбросился -->
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <!-- айди модуля для удаления -->
    <input type="hidden" name="id" value="hmarketing.d7">
    <!-- обязательно указывать поле uninstall со значением Y, иначе просто перейдем на страницу модулей -->
    <input type="hidden" name="uninstall" value="Y">
    <!-- определение шага удаления модуля -->
    <input type="hidden" name="step" value="2">
    <!-- предупреждение об удалении модуля, MOD_UNINST_WARN - системная языковая переменная -->
    <?= CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN")) ?>
    <!-- чекбокс для определния параметра удаления, MOD_UNINST_SAVE - системная языковая переменная -->
    <p><?= Loc::getMessage("MOD_UNINST_SAVE") ?></p>
    <!-- MOD_UNINST_DATA - системная языковая переменная -->
    <p><input type="checkbox" name="save_data" id="save_data" value="Y" checked><label for="save_data"><?= Loc::getMessage("MOD_UNINST_DATA") ?></label></p>
    <!-- MOD_UNINST_DATA_BUTTON - системная языковая переменная -->
    <input type="submit" name="" value="<?= Loc::getMessage("MOD_UNINST_DATA_BUTTON") ?>">
</form>