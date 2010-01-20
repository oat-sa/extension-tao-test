<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){

	<?if(get_data('uri') && get_data('classUri')):?>
	
		updateTabUrl(tabs, 'tests_authoring', "<?=_url('authoring', 'Tests', array('uri' => get_data('uri'), 'classUri' => get_data('classUri') ))?>");
		updateTabUrl(tabs, 'items_sequence',  "<?=_url('itemSequence', 'Tests', array('uri' => get_data('uri'), 'classUri' => get_data('classUri') ))?>");
	
	<?else:?>
	
		tabs.tabs('disable', getTabIndexByName('tests_authoring'));
		tabs.tabs('disable', getTabIndexByName('items_sequence'));
		
	<?endif?>


	<?if(get_data('reload') === true):?>	
		
		loadControls();
	
	<?else:?>
	
		initActions();
	
	<?endif?>
});
</script>