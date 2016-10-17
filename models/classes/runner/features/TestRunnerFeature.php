<?php

namespace oat\taoTests\models\runner\features;


//todo: write unit test
use oat\oatbox\PhpSerializable;

abstract class TestRunnerFeature implements PhpSerializable  {

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

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode() {
        return 'new '.get_class($this).'()';
    }

}