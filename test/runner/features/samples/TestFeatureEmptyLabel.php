<?php

namespace oat\taoTests\test\runner\features\samples;

use oat\taoTests\models\runner\features\TestRunnerFeature;

class TestFeatureEmptyLabel extends TestRunnerFeature
{
    public function getLabel()
    {
        return '';
    }

    public function getDescription()
    {
        return __('A simple feature used for unit testing');
    }
}