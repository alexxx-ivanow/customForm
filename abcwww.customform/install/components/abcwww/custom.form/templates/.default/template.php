<?

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<? if ($arParams['FORM_TITLE']): ?>
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-end">
                <h2 class="text-uppercase font-weight-bold">
                    <?= $arParams['FORM_TITLE'] ?>
                </h2>
            </div>
        </div>
    </div>
<? endif; ?>

<form class="custom_form jsCustomForm" <?=$arResult['FORM_ATTRIBUTES']?>>
    <?=$arResult['FORM_HIDDENS']?>

    <? foreach ($arParams['FIELDS'] as $key => $field): ?>
        <? if ($arParams['FIELD_COMMENT_TO_END'] === 'Y' && $field === 'COMMENT') continue; ?>

        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption"><?= Loc::getMessage($arResult['FIELD_PREFIX'] . $field . '_CAPTION') ?: $arResult['ALIASES'][$field] ?: $field?></span>

                <?if($field === 'COMMENT'):?>
                    <textarea class="form_input form-control" placeholder="<?= Loc::getMessage($arResult['FIELD_PREFIX'] . $field . '_CAPTION') ?: $arResult['ALIASES'][$field] ?: $field?>"
                              name="<?= $arResult['INPUT_COMMENT_NAME'] ?>"></textarea>
                <?else:?>
                    <input type="text" class="form_input form-control<? if ($field === 'PHONE' &&
                        $arParams['IS_PHONE_MASK'] === 'Y'): ?> jsCFTel<? endif; ?>"
                           placeholder="<?= Loc::getMessage($arResult['FIELD_PREFIX'] . $field . '_CAPTION') ?: $arResult['ALIASES'][$field] ?: $field ?>"
                           name="<?= $arResult['FIELD_PREFIX'] . $field ?>">
                <?endif;?>

                <span class="form-text <?= $arResult['FIELD_PREFIX'] ?>form_error <?= $arResult['FIELD_PREFIX'] . $field ?>_error"></span>
            </label>
        </div>

    <? endforeach; ?>

    <? if ($arResult['IS_COMMENT'] && $arParams['FIELD_COMMENT_TO_END'] === 'Y'): ?>
        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption"><?= $arResult['ALIASES']['COMMENT'] ?: $arResult['INPUT_COMMENT_NAME'] ?></span>
                <textarea class="form_input form-control" placeholder="<?= $arResult['ALIASES']['COMMENT'] ?: $arResult['INPUT_COMMENT_NAME'] ?>"
                          name="<?= $arResult['INPUT_COMMENT_NAME'] ?>"></textarea>
                <span class="form-text <?= $arResult['FIELD_PREFIX'] ?>form_error <?= $arResult['INPUT_COMMENT_NAME'] ?>_error"></span>
            </label>
        </div>
    <? endif; ?>

    <? if ($arParams['IS_FILE'] === 'Y'): ?>
        <div class="mb-3">
            <label class="form-label">
                <span class="form_caption">Загрузите файл</span>
                <input class="form-control" type="file" name="<?= $arResult['INPUT_FILE_NAME'] ?>">
                <span class="form-text <?= $arResult['FIELD_PREFIX'] ?>form_error <?= $arResult['INPUT_FILE_NAME'] ?>_error"></span>
            </label>
        </div>
    <? endif; ?>

    <? if ($arParams['IS_POLITICS'] === 'Y'): ?>
        <div class="form-group form-check">
            <label class="form-label">
                <input class="form_caption form-check-input" type="checkbox"
                       name="<?= $arResult['INPUT_POLITICS_NAME'] ?>" value="Y">
                <span class="form_input"><?= $arParams['~POLITICS_TEXT'] ?: 'Согласен на обработку политики' ?></span>
            </label>
            <span class="form-text <?= $arResult['FIELD_PREFIX'] ?>form_error <?= $arResult['INPUT_POLITICS_NAME'] ?>_error"></span>
        </div>
    <? endif; ?>

    <? if ($arParams['IS_AGREE'] === 'Y'): ?>
        <div class="form-group form-check">
            <label class="form-label">
                <input class="form_caption form-check-input" type="checkbox"
                       name="<?= $arResult['INPUT_AGREE_NAME'] ?>" value="Y">
                <span class="form_input"><?= $arParams['~AGREE_TEXT'] ?: 'Согласен на обработку персональных' ?></span>
            </label>
            <span class="form-text <?= $arResult['FIELD_PREFIX'] ?>form_error <?= $arResult['INPUT_AGREE_NAME'] ?>_error"></span>
        </div>
    <? endif; ?>

    <button type="submit" class="btn btn-primary" id="custom-form-button">Отправить</button>
    <div class="custom_result jsCustomResult"></div>
</form>
