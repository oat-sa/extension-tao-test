<?php

namespace oat\taoTests\models\runner\features;


//todo: write unit test
// todo: phpdoc
use oat\oatbox\PhpSerializable;
use oat\taoTests\models\runner\plugins\PluginRegistry;

abstract class TestRunnerFeature implements PhpSerializable
{
    private $id;

    private $pluginsIds;

    private $isEnabledByDefault;

    private $pluginRegistry;

    public function __construct(
        $id,
        $pluginsIds,
        $isEnabledByDefault = true,
        PluginRegistry $pluginRegistry)
    {
        $this->id = $id;
        $this->pluginsIds = $pluginsIds;
        $this->isEnabledByDefault = $isEnabledByDefault;
        $this->pluginRegistry = $pluginRegistry;

        $this->checkData();
    }

    public function checkData() {
        if(! is_string($this->id) || empty($this->id)) {
            throw new \common_exception_InconsistentData('The test runner feature needs an id');
        }

        if(! is_array($this->pluginsIds) || empty($this->pluginsIds)) {
            throw new \common_exception_InconsistentData('The test runner feature needs some plugin id');
        }

        $this->checkPluginsIds();

        if(! is_string($this->getLabel()) || empty($this->getLabel())) {
            throw new \common_exception_InconsistentData('The test runner feature needs a label');
        }

        if(! is_string($this->getDescription()) || empty($this->getDescription())) {
            throw new \common_exception_InconsistentData('The test runner feature needs a description');
        }

    }

    public function checkPluginsIds() {
        $allPluginIds = [];
        foreach ($this->pluginRegistry->getMap() as $plugin) {
            $allPluginIds[] = $plugin['id'];
        }
        foreach ($this->pluginsIds as $id) {
            if (! in_array($id, $allPluginIds)) {
                throw new \common_exception_InconsistentData('Invalid plugin Id ' . $id);
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPluginsIds()
    {
        return $this->pluginsIds;

    }

    public function isEnabledByDefault()
    {
        return $this->isEnabledByDefault;
    }

    abstract function getLabel();

    abstract public function getDescription();

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        return 'new '.get_class($this).'()';
    }

}