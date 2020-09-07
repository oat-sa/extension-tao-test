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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoTests\models\preview;

use oat\oatbox\AbstractRegistry;
use oat\tao\model\modules\DynamicModule;
use oat\tao\model\ClientLibConfigRegistry;
use oat\oatbox\service\ConfigurableService;

/**
 * Class TestPreviewerService
 *
 * @package oat\taoTests\models\preview
 */
class TestPreviewerService extends ConfigurableService
{
    public const SERVICE_ID = 'taoTests/TestPreviewer';

    private const REGISTRY_ENTRY_KEY = 'taoTests/previewer/factory';
    private const PREVIEWERS_KEY = 'previewers';

    /** @var AbstractRegistry */
    private $registry;

    /**
     * @return AbstractRegistry
     */
    public function getRegistry(): AbstractRegistry
    {
        if (!isset($this->registry)) {
            $this->registry = ClientLibConfigRegistry::getRegistry();
        }

        return $this->registry;
    }

    /**
     * @param AbstractRegistry $registry
     */
    public function setRegistry(AbstractRegistry $registry): void
    {
        $this->registry = $registry;
    }

    /**
     * @return array
     */
    public function getAdapters(): array
    {
        $registry = $this->getRegistry();

        if ($registry->isRegistered(self::REGISTRY_ENTRY_KEY)) {
            $config = $registry->get(self::REGISTRY_ENTRY_KEY);
        }

        return $config[self::PREVIEWERS_KEY] ?? [];
    }

    /**
     * @param DynamicModule $module
     *
     * @return bool
     */
    public function registerAdapter(DynamicModule $module): bool
    {
        if ($module === null || empty($module->getModule())) {
            return false;
        }

        $registry = $this->getRegistry();

        if ($registry->isRegistered(self::REGISTRY_ENTRY_KEY)) {
            $config = $registry->get(self::REGISTRY_ENTRY_KEY);
        }

        $config[self::PREVIEWERS_KEY][$module->getModule()] = $module->toArray();
        $registry->set(self::REGISTRY_ENTRY_KEY, $config);

        return true;
    }

    /**
     * @param string $moduleId
     *
     * @return bool
     */
    public function unregisterAdapter(string $moduleId): bool
    {
        $registry = $this->getRegistry();

        if ($registry->isRegistered(self::REGISTRY_ENTRY_KEY)) {
            $config = $registry->get(self::REGISTRY_ENTRY_KEY);
        }

        if (isset($config[self::PREVIEWERS_KEY][$moduleId])) {
            unset($config[self::PREVIEWERS_KEY][$moduleId]);
            $registry->set(self::REGISTRY_ENTRY_KEY, $config);

            return true;
        }

        return false;
    }
}
