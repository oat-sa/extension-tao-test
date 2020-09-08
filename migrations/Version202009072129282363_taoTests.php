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

namespace oat\taoTests\migrations;

use Doctrine\DBAL\Schema\Schema;
use common_ext_Extension as Extension;
use common_ext_ExtensionsManager as ExtensionsManager;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoTests\models\preview\TestPreviewerRegistryService;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\taoTests\scripts\install\RegisterTestPreviewerRegistryService;

/**
 * Class Version202009072129282363_taoTests
 *
 * @package oat\taoTests\migrations
 */
final class Version202009072129282363_taoTests extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Register ' . TestPreviewerRegistryService::class;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->propagate(new RegisterTestPreviewerRegistryService())([]);
    }

    /**
     * @param Schema $schema
     *
     * @throws InvalidServiceManagerException
     */
    public function down(Schema $schema): void
    {
        $serviceManager = $this->getServiceManager();

        $serviceManager->unregister(TestPreviewerRegistryService::SERVICE_ID);

        /** @var Extension $extension */
        $extension = $this->getServiceManager()->get(ExtensionsManager::SERVICE_ID)->getExtensionById('tao');

        $config = $extension->getConfig('client_lib_config_registry');
        unset($config['taoTests/controller/tests/action']);

        $extension->setConfig('client_lib_config_registry', $config);
    }
}
