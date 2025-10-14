<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<form method="post" class="custom_form jsCustomFrom" action="<?=$APPLICATION->GetCurPage()?>">
    <input type="hidden" name="bot_field" value="">

    <?foreach($arParams['FIELDS'] as $key => $field):?>    
    	<?if(in_array($field, $arResult['EXCLUDE'])) continue;?>	
    	<div class="form-group">
		    <label>
		    	<span class="form_caption"><?=Loc::getMessage('CF_' . $field . '_CAPTION')?></span>
		    	<input type="text" class="form_input" name="CF_<?=$field?>">
		    </label>
	    </div>
    <?endforeach;?>

    <?if($arResult['IS_COMMENT']):?>
	    <div class="form-group">
		    <label>
		    	<span class="form_caption"><?=Loc::getMessage('CF_COMMENT_CAPTION')?></span>
		    	<textarea type="tel" class="form_input" name="CF_COMMENT"></textarea>
		    </label>
	    </div>
    <?endif;?>

    <?if($arParams['IS_AGREE'] === 'Y'):?>
	    <div class="form-group">
		    <label>
		    	<input type="hidden" name="CF_AGREE" value="">
		        <input class="form_caption" type="checkbox" name="CF_AGREE" id="agree-checkbox" value="Y">
		        <span class="form_input">Согласен на обработку персональных данных</span>
		    </label>
	    </div>
    <?endif;?>

    <button type="submit" id="custom-form-button">Отправить</button>
    <div class="custom_result jsCustomResult"></div>
</form>
