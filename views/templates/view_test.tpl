<?php
use oat\tao\helpers\Template;
Template::inc('form_context.tpl', 'tao');
?>

<header class="section-header flex-container-full">
    <h2><?= get_data('label') ?></h2>
</header>

<div class="main-container flex-container-main-form">
    <div class="form-content">
        <div class="xhtml_form">
            <form>
                <div class="property-container">
                    <div class="form-group property-block-first property-block readonly-property">
                        <span class="property-heading-label"><?=__('Label')?></span>
                        <div>
                            <div><?= get_data('label') ?></div>
                        </div>
                    </div>
                    <div class="form-group property-block readonly-property">
                        <span class="property-heading-label"><?=__('Test Model')?></span>
                        <div>
                            <div><?= get_data('model') ?></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php Template::inc('footer.tpl', 'tao'); ?>
