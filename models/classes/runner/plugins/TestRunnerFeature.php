<?php

namespace oat\taoTests\models\runner\plugins;


//todo: write unit test
abstract class TestRunnerFeature  {

    public function __construct() {
        if(! is_string($this->getId()) || empty($this->getId())) {
            throw new \common_exception_InconsistentData('The test runner feature needs an id');
        }
        if(! is_array($this->getPluginsIds()) || empty($this->getPluginsIds())) {
            throw new \common_exception_InconsistentData('The test runner feature needs some plugin id');
        }

        return true;
    }

    abstract function getId();
    abstract function getPluginsIds();
    abstract function getLabel();

    public function getDescription() {
        return '';
    }

    public function isEnabledByDefault() {
        return true;
    }

}