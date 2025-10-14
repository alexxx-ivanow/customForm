<?
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if($arParams['IS_BOOTSTRAP'] === 'Y')
    Asset::getInstance()->addCss('/local/components/mg15/custom.form/lib/css/bootstrap.css');
?>

<form method="post" class="custom_form jsCustomFrom" action="<?=$APPLICATION->GetCurPage()?>">
    <?if($arParams['IS_ANTISPAM'] === 'Y'):?>
        <input type="hidden" name="bot_field" value="<?=$arResult['BOT_CODE']?>">
    <?endif;?>

    <?foreach($arParams['FIELDS'] as $key => $field):?>
        <?if(in_array($field, $arResult['EXCLUDE'])) continue;?>
        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption"><?=Loc::getMessage('CF_' . $field . '_CAPTION')?></span>
                <input type="text" class="form_input form-control" name="CF_<?=$field?>">
            </label>
        </div>
    <?endforeach;?>

    <?if($arResult['IS_COMMENT']):?>
        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption"><?=Loc::getMessage('CF_COMMENT_CAPTION')?></span>
                <textarea type="tel" class="form_input form-control" name="CF_COMMENT"></textarea>
            </label>
        </div>
    <?endif;?>

    <?if($arParams['IS_AGREE'] === 'Y'):?>
        <div class="form-group form-check">
            <label class="form-label">
                <input type="hidden" name="CF_AGREE" value="">
                <input class="form_caption form-check-input" type="checkbox" name="CF_AGREE" id="agree-checkbox" value="Y">
                <span class="form_input">Согласен на обработку персональных данных</span>
            </label>
        </div>
    <?endif;?>

    <button type="submit" class="btn btn-primary" id="custom-form-button">Отправить</button>
    <div class="custom_result jsCustomResult"></div>
</form>
