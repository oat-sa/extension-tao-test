<html>
<head>
	<title><?=__('Test Preview')?></title>
	<script type='text/javascript' src='/tao/views/js/jquery-1.3.2.min.js' ></script> 
	<script type='text/javascript'>
	$(function(){
		$.ajax({
			url: '/taoDelivery/delivery/compile',
			type: 'POST',
			data: {uri: "<?=get_data('uri')?>"},
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
					window.location = '/taoDelivery/Delivery/preview?uri=<?=urlencode(get_data('uri'))?>';
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
