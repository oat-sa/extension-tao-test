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
			<br />
			<ul id="item-sequence" class="sortable-list">
			<?foreach(get_data('itemSequence') as $index => $item):?>
				<li class="ui-state-default" id="item_<?=$item['uri']?>" >
					<span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>
					<span class="ui-icon ui-icon-grip-dotted-vertical" ></span>
					<?=$index?>. <?=$item['label']?>
				</li>
			<?endforeach?>
			</ul>
		</div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-item-sequence" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	
	var sequence = <?=get_data('relatedItems')?>;
	var labels = <?=get_data('allItems')?>;
	
	function buildItemList(id, items, labels){
		html = '';
		for (i in items) {
			itemId = items[i];
			html += "<li class='ui-state-default' id='" + itemId + "' >";
			html += "<span class='ui-icon ui-icon-arrowthick-2-n-s' /><span class='ui-icon ui-icon-grip-dotted-vertical' />";
			html += i + ". " + labels[itemId];
			html += "</li>";
		}
		$("#" + id).html(html);
	}
	
	new GenerisTreeFormClass('#item-tree', "/taoTests/Tests/getItems",{
		actionId: 'item',
		saveUrl : '/taoTests/Tests/saveItems',
		saveCallback: function (data){
			if (buildItemList != undefined) {
				items = {};
				for(attr in data){
					if(/^instance_/.test(attr)){
						items[parseInt(attr.replace('instance_', ''))+1] = 'item_'+ data[attr];
					}
				}
				buildItemList("item-sequence", items, labels);
			}
		},
		checkedNodes : sequence
	});
	
	$("#item-sequence").sortable({
		axis: 'y',
		opacity: 0.6,
		placeholder: 'ui-state-error',
		tolerance: 'pointer',
		update: function(event, ui){
			listItems = $(this).sortable('toArray');
			newSequence = {};
			sequence = {};
			for (i = 0; i < listItems.length; i++){
				index = i+1;
				newSequence[index] = listItems[i];
				sequence[index] = listItems[i].replace('item_', '');
			}
			buildItemList('item-sequence', newSequence, labels);
		}
	});
	
	$("#item-sequence li").bind('mousedown', function(){
		$(this).css('cursor', 'move');
	});
	$("#item-sequence li").bind('mouseup',function(){
		$(this).css('cursor', 'pointer');
	});
	
	$("#saver-action-item-sequence").click(function(){
		toSend = {};
		for(index in sequence){
			toSend['instance_'+index] = sequence[index];
		}
		toSend.uri = $("input[name=uri]").val();
		toSend.classUri = $("input[name=classUri]").val();
		$.ajax({
			url: '/taoTests/Tests/saveItems',
			type: "POST",
			data: toSend,
			dataType: 'json',
			success: function(response){
				if (response.saved) {
					createInfoMessage("<?=__('Sequence saved successfully')?>");
				}
			},
			complete: function(){
				loaded();
			}
		});
	});
	
});
</script>