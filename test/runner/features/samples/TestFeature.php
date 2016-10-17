<?php

namespace oat\taoTests\test\runner\features\samples;

use oat\taoTests\models\runner\features\TestRunnerFeature;

class TestFeature extends TestRunnerFeature
{
    public function getLabel()
    {
        return __('testFeature');
    }

    public function getDescription()
    {
        return __('A simple feature used for unit testing');
    }
}
