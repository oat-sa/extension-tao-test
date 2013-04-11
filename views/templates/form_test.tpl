<? include(TAO_TPL_PATH . 'form_context.tpl') ?>
<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_test.css" />
<?if(get_data('authoringMode') == 'simple'):?>
<div id="test-left-container">
	<?include('items.tpl')?>
	<div class="breaker"></div>
</div>
<?endif;?>
<div class="main-container<?if(get_data('authoringMode') == 'simple'):?> medium<?endif;?>" id="test-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<? include('footer.tpl') ?>
