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

use common_Logger;
use common_ext_ExtensionsManager;
use oat\oatbox\AbstractRegistry;
use oat\taoTests\models\runner\plugins\TestPlugin;

/**
 * Store the <b>available</b> test runner plugins, even if not activated,
 * plugins have to be activated.
 *
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class PluginRegistry extends AbstractRegistry
{
    /**
     * @see \oat\oatbox\AbstractRegistry::getConfigId()
     */
    protected function getConfigId()
    {
        return 'test_runner_plugin_registry';
    }

    /**
     * @see \oat\oatbox\AbstractRegistry::getExtension()
     */
    protected function getExtension()
    {
        return common_ext_ExtensionsManager::singleton()->getExtensionById('taoTests');
    }

    /**
     * Register a plugin
     * @param TestPlugin $plugin the plugin to register
     * @return boolean true if registered
     */
    public function register(TestPlugin $plugin)
    {
        if(!is_null($plugin) && ! empty($plugin->getModule()) ) {

            //encode the plugin into an assoc array
            $pluginData = json_decode(json_encode($plugin), true);

            self::getRegistry()->set($plugin->getModule(),  $pluginData);

            return true;
        }
        return false;
    }
}
