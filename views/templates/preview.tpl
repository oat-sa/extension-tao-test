<html>
<head>
	<title><?=__('Test Preview')?></title>
	<script type='text/javascript' src='<?=BASE_WWW?>js/jquery-1.4.2.min.js' ></script> 
	<script type='text/javascript'>
	$(function(){
		$.ajax({
			url: "<?=_url('compile', 'Delivery', 'taoDelivery')?>",
			type: 'POST',
			data: {uri: "<?=tao_helpers_Uri::decode(get_data('uri'))?>"},
			dataType: 'json',
			success: function(response){
				compiled = false;
				if(response.success==1){
                	$('#main-container').html("<?=__('compiled')?>");
					compiled = true;
            	}
				else if(response.success==2){
                   $('#main-container').html("<?=__('compiled with warning')?>");
				   compiled = true;
				}
                else{
                   $('#main-container').html("<?=__('compilation failed')?>");
                }
				
				if(compiled){
					window.location = "<?=_url('preview', 'Delivery', 'taoDelivery', array('uri' => urlencode(tao_helpers_Uri::decode(get_data('uri')))))?>";
				}
			}
		});
	});
	</script>
</head>
<body>
	<div id="main-container" style="text-align:center;">
		<img src="<?=BASE_WWW?>img/ajax-loader.gif" /><br />
		<strong><?=__('Compiling test')?></strong>
	</div>
</body>
</html>
