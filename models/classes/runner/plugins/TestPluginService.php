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

use oat\oatbox\service\ConfigurableService;
use core_kernel_classes_Resource;
use oat\oatbox\service\ServiceManager;
use tao_models_classes_service_ConstantParameter as ConstantParameter;
use tao_models_classes_service_ServiceCall as ServiceCall;
/**
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPluginService extends ConfigurableService
{

    const CONFIG_ID = 'taoTests/TestPlugin';

    private $registry;

    public function __construct()
    {
        $this->registry = PluginRegistry::getRegistry();
    }

    public function getAllPlugins()
    {
        return array_map(function($value) {
          return TestPlugin::fromArray($value);
        }, $this->registry->getMap());
    }

    public function getPlugin($id)
    {
        foreach($this->registry->getMap() as $plugin){
            if($plugin['id'] == $id){
                return TestPlugin::fromArray($plugin);
            }
        }
        return null;
    }

    public function activatePlugin(TestPlugin $plugin)
    {
        if(!is_null($plugin)){
            $plugin->setActive(true);
            $this->registry->register($plugin);
        }
    }

    public function deactivatePlugin(TestPlugin $plugin)
    {
        if(!is_null($plugin)){
            $plugin->setActive(false);
            $this->registry->register($plugin);
        }
    }

    public function setRegistry(PluginRegistry $registry)
    {
        $this->registry = $registry;
    }

}
