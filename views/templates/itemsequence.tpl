<?include(TAO_TPL_PATH . 'header.tpl')?>


<?if(get_data('error')):?>
	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?=__('Please select a test before!')?>
		</div>
		<br />
		<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
			<a href="#" onclick="selectTabByName('manage_tests');"><?=__('Back')?></a>
		</span>
	</div>
<?else:?>
	<table id="citem" ></table>
	<div id="citem-pager"></div>
	
	<input type="button" value="<?=__('Save')?>" id="citem-saver" />
	
	<script type="text/javascript">
		$(function(){
		
			var lastRow = null;
			$("#citem").jqGrid({
				url: "<?_url('itemSequenceData', 'Tests', 'taoTests', array('uri' => get_data('uri'), 'classUri' => get_data('classUri')) )?>", 
				datatype: "json", 
				colNames:[__('sequence'), __('uri'), __('label'), __('weight'), __('difficulty'), __('discrimination'), __('guessing'), __('model')], 
				colModel:[ 
					{name:'sequence', 	index:'sequence', 	width:100, align:"center", editable: true, editoptions: {size:4}},
					{name:'uri', 		index:'uri', 		width:0},
					{name:'label', 		index:'label', 		width:250},
					{name:'weight',		index:'weight', 	width:100, align:"center", editable: true, editoptions: {size:4}},
					{name:'difficulty',	index:'difficulty', width:100, align:"center", editable: true, editoptions: {size:4}},
					{name:'discrimination',index:'discrimination', width:100, align:"center", editable: true, editoptions: {size:4}},
					{name:'guessing',	index:'guessing', 	width:100, align:"center", editable: true, editoptions: {size:4}},
					{name:'model',		index:'model', 		width:250, align:"center", editable: true, edittype:"select",editoptions:{value:":;GUESSINGMODDEL:GUESSINGMODDEL;BIRNBAUMODEL:BIRNBAUMODEL;RACHMODEL:RACHMODEL;discrete:discrete"}}
				], 
				onSelectRow: function(id){ 
					if(id && id != lastRow){
						$("#citem").saveRow(lastRow, false, 'clientArray'); 
						$("#citem").restoreRow(lastRow);
						$("#citem").editRow(id, true); 
						$("input:text").numeric();
						$("input:text").attr('title', "<?=__('Numeric value')?>");
						lastRow = id;
					}
				},
				rowNum:20, 
				width:'100%', 
				height:400, 
				pager: '#citem-pager', 
				sortname: 'sequence', 
				viewrecords: true, 
				sortorder: "asc", 
				caption: __("Items")
			});
			$("#citem").navGrid('#citem-pager', {edit:false, add:false, del:false});
			$("#citem").hideCol(["uri"]); 
			$("#citem-saver").click(function(){
				var params = {
					'uri'		: "<?=get_data('uri')?>",
					'classUri'	: "<?=get_data('classUri')?>"
				};
				$.each($("#citem").getDataIDs(), function(){
					$("#citem").saveRow(this, false, 'clientArray'); 
					$("#citem").restoreRow(this);
					row = $("#citem").getRowData(this);
					for(i in row){
						params['item_' + this + '_' + i] =  row[i];
					}
				});
				$.postJson(
					"<?=_url('saveItemSequence', 'Tests', 'taoTests')?>", 
					params, 
					function(response){
						if(response.saved){
							createInfoMessage(__('Sequence saved'));
						}
						else{
							createErrorMessage(__('An error occured while saving the sequence'));
						}
					}
				);
			});
		});
	</script>
<?endif?>

<?include(TAO_TPL_PATH . 'footer.tpl')?>