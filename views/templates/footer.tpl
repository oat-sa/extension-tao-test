<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){

	<?if(get_data('action') != 'authoring'):?>
		<?if(get_data('uri') && get_data('classUri')):?>
		index = getTabIndexByName('tests_authoring');
		tabs.tabs('url', index, "/taoTests/Tests/authoring?uri=<?=get_data('uri')?>&classUri=<?=get_data('classUri')?>");
		tabs.tabs('enable', index);
		<?else:?>
			tabs.tabs('disable', getTabIndexByName('tests_authoring'));
		<?endif?>
	<?endif?>

	<?if(get_data('reload') === true):?>	
		
	loadControls();
	
	<?else:?>
	
	initActions();
	
	<?endif?>
});
</script>