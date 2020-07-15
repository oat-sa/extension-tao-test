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
 * Copyright (c) 2016-2017 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoTests\models\runner\plugins;

use common_ext_Extension;
use common_ext_ExtensionException;
use common_ext_ExtensionsManager;
use InvalidArgumentException;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\tao\model\modules\AbstractModuleRegistry;

/**
 * Store the <b>available</b> test runner plugins, even if not activated,
 * plugins have to be activated.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class PluginRegistry extends AbstractModuleRegistry
{
    public const CONFIG_ID = 'test_runner_plugin_registry';

    /**
     * @see \oat\oatbox\AbstractRegistry::getConfigId()
     */
    protected function getConfigId(): string
    {
        return static::CONFIG_ID;
    }

    /**
     * @return common_ext_Extension
     * @throws common_ext_ExtensionException
     * @throws InvalidServiceManagerException
     * @see \oat\oatbox\AbstractRegistry::getExtension()
     */
    protected function getExtension()
    {
        /** @var common_ext_ExtensionsManager $extensionManager */
        $extensionManager = $this->getServiceManager()->get(common_ext_ExtensionsManager::SERVICE_ID);

        return $extensionManager->getExtensionById('taoTests');
    }

    public function activate(string $pluginId): void
    {
        $this->applyPluginState($pluginId, true);
    }

    public function deactivate(string $pluginId): void
    {
        $this->applyPluginState($pluginId, false);
    }

    protected function getPluginDefinition(string $pluginId): TestPlugin
    {
        if (!$this->isRegistered($pluginId)) {
            throw new InvalidArgumentException('Requested plugin is not registered');
        }

        return TestPlugin::fromArray($this->get($pluginId));
    }

    private function applyPluginState(string $pluginId, bool $isActive): void
    {
        $definition = $this->getPluginDefinition($pluginId);

        $definition->setActive($isActive);

        $this->set($pluginId, $definition->toArray());
    }
}
