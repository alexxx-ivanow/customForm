<?
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?//DD($arParams);?>

<?if($arParams['FORM_TITLE']):?>
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-end">
                <h2 class="text-uppercase font-weight-bold">
                    <?=$arParams['FORM_TITLE']?>
                </h2>
            </div>
        </div>
    </div>
<?endif;?>

<form method="post" class="custom_form jsCustomForm" action="<?=$APPLICATION->GetCurPage()?>">
    <?if($arParams['IS_ANTISPAM'] === 'Y'):?>
        <input type="hidden" name="<?=$arResult['FIELD_PREFIX']?>B_FIELD" value="<?=$arResult['BOT_CODE']?>">
    <?endif;?>

    <?foreach($arParams['FIELDS'] as $key => $field):?>
        <?if(in_array($field, $arResult['EXCLUDE'])) continue;?>
        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption"><?=$arResult['ALIASES'][$field] ?: $arResult['FIELD_PREFIX'] . $field?></span>
                <input type="text" class="form_input form-control<?if($field === 'PHONE' &&
                $arParams['IS_PHONE_MASK'] === 'Y'):?> jsCFTel<?endif;?>"
                       placeholder="<?=$arResult['ALIASES'][$field]
                    ?: $arResult['FIELD_PREFIX'] . $field?>" name="<?=$arResult['FIELD_PREFIX'] . $field?>">
                <span class="form-text <?=$arResult['FIELD_PREFIX']?>form_error <?=$arResult['FIELD_PREFIX'] . $field?>_error"></span>
            </label>
        </div>
    <?endforeach;?>

    <?if($arResult['IS_COMMENT']):?>
        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption"><?=$arResult['ALIASES']['COMMENT'] ?:
                        (Loc::getMessage('CF_' . $field . '_CAPTION') ?: $arResult['FIELD_PREFIX'] . $field)?></span>
                <textarea type="tel" class="form_input form-control" placeholder="<?=$arResult['ALIASES']['COMMENT'] ?:
                    (Loc::getMessage('CF_' . $field . '_CAPTION') ?: $arResult['FIELD_PREFIX'] . $field)?>" name="<?=$arResult['FIELD_PREFIX']?>COMMENT"></textarea>
                <span class="form-text <?=$arResult['FIELD_PREFIX']?>form_error <?=$arResult['FIELD_PREFIX']?>COMMENT_error"></span>
            </label>
        </div>
    <?endif;?>

    <?if($arParams['IS_POLITICS'] === 'Y'):?>
        <div class="form-group form-check">
            <label class="form-label">
                <input class="form_caption form-check-input" type="checkbox" name="<?=$arResult['FIELD_PREFIX']?>POLITICS" value="Y">
                <span class="form_input">Согласен на обработку политики конфиденциальности</span>
            </label>
            <span class="form-text <?=$arResult['FIELD_PREFIX']?>form_error <?=$arResult['FIELD_PREFIX']?>POLITICS_error"></span>
        </div>
    <?endif;?>

    <?if($arParams['IS_AGREE'] === 'Y'):?>
        <div class="form-group form-check">
            <label class="form-label">
                <input class="form_caption form-check-input" type="checkbox" name="<?=$arResult['FIELD_PREFIX']?>AGREE" value="Y">
                <span class="form_input">Согласен на обработку персональных данных</span>
            </label>
            <span class="form-text <?=$arResult['FIELD_PREFIX']?>form_error <?=$arResult['FIELD_PREFIX']?>AGREE_error"></span>
        </div>
    <?endif;?>

    <button type="submit" class="btn btn-primary" id="custom-form-button">Отправить</button>
    <div class="custom_result jsCustomResult"></div>
</form>
