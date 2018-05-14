<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoTests\models\runner\features;

use oat\taoTests\models\runner\plugins\TestPlugin;
use Psr\Log\LoggerAwareInterface;
use oat\oatbox\log\LoggerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * A test runner feature is a user feature that can be expressed by one or more test runner plugins.
 * They can be toggled at the delivery level and work only in the new test runner.
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */

abstract class TestRunnerFeature implements TestRunnerFeatureInterface, LoggerAwareInterface, ServiceLocatorAwareInterface
{
    use LoggerAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string[] must match active test runner plugins Ids
     */
    protected $pluginsIds;

    /**
     * @var bool Determine if the feature will be automatically enabled upon delivery creation
     */
    protected $isEnabledByDefault;

    /**
     * @var TestPlugin[] Used to check the existence of plugins Ids
     */
    protected $allPlugins;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @param string        $id
     * @param string[]      $pluginsIds
     * @param bool          $isEnabledByDefault
     * @param TestPlugin[]  $allPlugins
     * @throws \common_exception_InconsistentData
     */
    public function __construct(
        $id,
        $pluginsIds,
        $isEnabledByDefault,
        $allPlugins,
        $active = true)
    {
        if(! is_string($id) || empty($id)) {
            throw new \common_exception_InconsistentData('id should be a valid string');
        }

        if(! is_array($pluginsIds) || empty($pluginsIds) || ! is_string($pluginsIds[0])) {
            throw new \common_exception_InconsistentData('pluginsIds should be a array of strings');
        }

        if(! is_bool($isEnabledByDefault)) {
            throw new \common_exception_InconsistentData('isEnabledByDefault should be a boolean');
        }

        if(! is_array($allPlugins) || empty($allPlugins) || ! current($allPlugins) instanceof TestPlugin) {
            throw new \common_exception_InconsistentData('allPlugins should be an array of TestPlugin');
        }

        $this->id = $id;
        $this->pluginsIds = $pluginsIds;
        $this->isEnabledByDefault = $isEnabledByDefault;
        $this->allPlugins = $allPlugins;
        $this->active = $active;

        // also check that abstract methods have been implemented correctly
        if(! is_string($this->getLabel()) || empty($this->getLabel())) {
            throw new \common_exception_InconsistentData('The test runner feature needs a label');
        }

        if(! is_string($this->getDescription()) || empty($this->getDescription())) {
            throw new \common_exception_InconsistentData('The test runner feature needs a description');
        }
    }

    /**
     * Check that the content of $pluginsIds matches existing and active plugin Ids
     * @throws \common_exception_InconsistentData
     */
    private function checkPluginsIds() {
        $allPluginIds = [];
        $inactivePluginsIds = [];

        foreach ($this->getAllPlugins() as $plugin) {
            $allPluginIds[] = $plugin->getId();
            if ($plugin->isActive() === false) {
                $inactivePluginsIds[] = $plugin->getId();
            }
        }
        foreach ($this->pluginsIds as $id) {
            if (! in_array($id, $allPluginIds)) {
                $this->logWarning('Invalid plugin Id ' . $id . ' for test runner feature ' . $this->id);
            }
            if (in_array($id, $inactivePluginsIds)) {
                $this->logWarning('Cannot include inactive plugin ' . $id . ' in test runner feature ' . $this->id);
            }
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     * @throws \common_exception_InconsistentData
     */
    public function getPluginsIds()
    {
        $this->checkPluginsIds();
        return $this->pluginsIds;
    }

    /**
     * @return bool
     */
    public function isEnabledByDefault()
    {
        return $this->isEnabledByDefault;
    }

    /**
     * Is feature activated
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return TestPlugin[]
     */
    protected function getAllPlugins()
    {
        return $this->allPlugins;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        return 'new '.get_class($this).'()';
    }
}
