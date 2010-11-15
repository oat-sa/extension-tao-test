<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){

	<?if(get_data('uri') && get_data('classUri') && (get_data('authoringMode')=='advanced') ):?>
		updateTabUrl(UiBootstrap.tabs, 'tests_authoring', "<?=_url('authoring', 'Tests', 'taoTests', array('uri' => get_data('uri'), 'classUri' => get_data('classUri') ))?>");
	<?else:?>
		UiBootstrap.tabs.tabs('disable', getTabIndexByName('tests_authoring'));
	<?endif?>

	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>
	
	setAuthoringModeButtons();
	EventMgr.bind('actionInitiated', function(event, response){
		setAuthoringModeButtons();
	});
	
});

function setAuthoringModeButtons(){
	$advContainer = $('#action_advanced_mode').parent();
	$simpleContainer = $('#action_simple_mode').parent();
	$advContainer.hide();
	$simpleContainer.hide();
	<?if(get_data('uri') && get_data('classUri')):?> 
		<?if(get_data('authoringMode')=='advanced'):?>
			$simpleContainer.insertAfter($advContainer);
			$simpleContainer.show();
			$('#action_simple_mode').unbind('click');
			$('#action_simple_mode').click(function(e){
				e.preventDefault();
				if(!confirm('Are you sure to switch back to the simple mode? \n The delivery process will be linearized.')){
					// console.log('getting simple');
					// $(this).find('a').click();
					
					return false;
				}
			});
		<?else:?>
			$advContainer.show();
		<?endif;?>
	<?endif;?>
}
</script>