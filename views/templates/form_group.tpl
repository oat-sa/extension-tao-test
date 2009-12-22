<?include('header.tpl')?>

<div id="item-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select related items')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="item-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-item" type="button" value="<?=__('Save')?>" />
	</div>
	
</div>
<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<script type="text/javascript">
$(function(){
	
	new GenerisTreeFormClass('#item-tree', "/taoTests/Tests/getItems",{
		actionId: 'item',
		saveUrl : '/taoTests/Tests/saveItems',
		checkedNodes : <?=get_data('relatedItems')?>
	});
});
</script>

<?include('footer.tpl');?>
