<?include(TAO_TPL_PATH . 'messages.tpl')?>
<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/form_test.css" />

<div id="test-left-container">
	<?if(get_data('authoringMode') == 'simple'):?>
	<?include('items.tpl')?>
	<div class="breaker"></div>
	<?endif;?>
</div>

<div class="main-container<?if(get_data('authoringMode') == 'advanced'):?> main-container-alone<?endif;?> medium" id="test-main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<?include('footer.tpl');?>
