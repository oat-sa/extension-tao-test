<?include('header.tpl')?>

<div id="item-container" class="data-container" >
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
<div id="item-order-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Items sequence')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="item-list">
			<ul id="item-sequence" class="sortable-list">
			<?foreach(get_data('itemSequence') as $index => $item):?>
				<li class="ui-state-default" id="item_<?=$index?>_<?=$item['uri']?>" ><?=$item['label']?></li>
			<?endforeach?>
			</ul>
		</div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="" type="button" value="<?=__('Up')?>" />
		<input id="" type="button" value="<?=__('Down')?>" />
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
	
	$("#item-sequence").sortable({
		axis: 'y',
		opacity: 0.6
	});
});
</script>

<?include('footer.tpl');?>
