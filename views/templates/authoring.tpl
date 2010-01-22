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
	
	<div class="main-container" style="display:none;"></div>
	<?include('form.tpl')?>
		
<?endif?>

<?include('footer.tpl')?>