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
namespace oat\taoTests\models\runner\plugins;

use core_kernel_classes_Resource;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use tao_models_classes_service_ConstantParameter as ConstantParameter;
use tao_models_classes_service_ServiceCall as ServiceCall;

/**
 * Manage test plugins
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPluginService extends ConfigurableService
{

    const CONFIG_ID = 'taoTests/TestPlugin';

    /**
     * @var PluginRegistry
     */
    private $registry;

    public function __construct()
    {
        $this->registry = PluginRegistry::getRegistry();
    }

    /**
     * Retrieve the list of all available plugins (from the registry)
     *
     * @return TestPlugin[] the avaialble plugins
     */
    public function getAllPlugins()
    {
        $plugins = array_map(function($value) {
            return $this->loadPlugin($value);
        }, $this->registry->getMap());

        return array_filter($plugins, function($plugin){
            return !is_null($plugin);
        });
    }

    /**
     * Retrieve the given plugin from the registry
     *
     * @param string $id the identifier of the plugin to retrieve
     * @return TestPlugin|null the plugin
     */
    public function getPlugin($id)
    {
        foreach($this->registry->getMap() as $plugin){
            if($plugin['id'] == $id){
                return $this->loadPlugin($plugin);
            }
        }
        return null;
    }

    /**
     * Load a test plugin from the given data
     * @param array $data
     * @return TestPlugin|null
     */
    private function loadPlugin(array $data)
    {
        $plugin = null;
        try {
            $plugin = TestPlugin::fromArray($data);
        } catch(\common_exception_InconsistentData $dataException) {
            \common_Logger::w('Got inconsistent plugin data, skipping.');
        }
        return $plugin;
    }

    /**
     * Change the state of a plugin to active
     *
     * @param TestPlugin $plugin the plugin to activate
     * @return boolean true if activated
     */
    public function activatePlugin(TestPlugin $plugin)
    {
        if(!is_null($plugin)){
            $plugin->setActive(true);
            return $this->registry->register($plugin);
        }

        return false;
    }

    /**
     * Change the state of a plugin to inactive
     *
     * @param TestPlugin $plugin the plugin to deactivate
     * @return boolean true if deactivated
     */
    public function deactivatePlugin(TestPlugin $plugin)
    {
        if(!is_null($plugin)){
            $plugin->setActive(false);
            $this->registry->register($plugin);
        }
    }

    /**
     * Registry setter
     * @param PlguinRegistry $registry
     */
    public function setRegistry(PluginRegistry $registry)
    {
        $this->registry = $registry;
    }
}
