<script type="text/javascript">
$(function(){

	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>
	
	<?if(has_data('message')):?>
	   helpers.createMessage(<?=json_encode(get_data('message'))?>);
	<?endif?>

});

</script>