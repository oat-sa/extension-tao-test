<?include('header.tpl')?>


<?if(get_data('error')):?>
	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?=__('Please select an test before!')?>
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
				url:"/taoTests/Tests/itemSequenceData?uri=<?=get_data('uri')?>&classUri=<?=get_data('classUri')?>", 
				datatype: "json", 
				colNames:['sequence', 'uri', 'label', 'weight', 'difficulty','discrimination','guessing','model'], 
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
				caption: "Items"
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
					'/taoTests/Tests/saveItemSequence', 
					params, 
					function(response){
						if(response.saved){
							createInfoMessage('Sequence saved');
						}
						else{
							createErrorMessage('An error occured while saving the sequence');
						}
					}
				);
			});
		});
	</script>
<?endif?>

<?include('footer.tpl')?>