<script type="text/javascript">
$(function(){

	<?if(get_data('uri') && get_data('classUri') && (get_data('authoringMode')=='advanced') ):?>
		helpers.updateTabUrl(uiBootstrap.tabs, 'tests_authoring', "<?=_url('authoring', 'Tests', 'taoTests', array('uri' => get_data('uri'), 'classUri' => get_data('classUri') ))?>");
	<?else:?>
		uiBootstrap.tabs.tabs('disable', helpers.getTabIndexByName('tests_authoring'));
	<?endif?>

	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>

	setAuthoringModeButtons();
	eventMgr.bind('actionInitiated', function(event, response){
		setAuthoringModeButtons();
	});
});

function setAuthoringModeButtons(){
	$advContainer = $('#action_advanced_mode');
	$simpleContainer = $('#action_simple_mode');
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
					return false;
				}
			});
		<?else:?>
			$advContainer.show();
		<?endif;?>
	<?else:?>
			$advContainer.show();
	<?endif;?>
}
</script>