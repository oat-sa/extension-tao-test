<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>
<?php if(has_data('authoring')):?>
<div id="test-left-container">
	<?= get_data('authoring')?>
	<div class="breaker"></div>
</div>
<?php endif;?>
<div class="main-container">
	<h2><?=get_data('formTitle')?></h2>
	<div class="form-content">
		<?=get_data('myForm')?>
	</div>
</div>
<?php 
Template::inc('footer.tpl', 'tao');
?>
