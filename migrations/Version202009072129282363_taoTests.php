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
use common_exception_Error as Error;
use oat\taoTests\scripts\install\SetupProvider;
use common_ext_ExtensionsManager as ExtensionsManager;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use common_ext_ExtensionException as ExtensionException;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\taoTests\scripts\install\RegisterTestPreviewerRegistryService;
use oat\taoTests\models\preview\TestPreviewerRegistryServiceInterface;

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
        return 'Register ' . TestPreviewerRegistryServiceInterface::class;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->propagate(new RegisterTestPreviewerRegistryService())([]);
        $this->propagate(new SetupProvider())([]);
    }

    /**
     * @param Schema $schema
     *
     * @throws Error
     * @throws InvalidServiceManagerException
     * @throws ExtensionException
     */
    public function down(Schema $schema): void
    {
        $serviceManager = $this->getServiceManager();

        $serviceManager->unregister(TestPreviewerRegistryServiceInterface::SERVICE_ID);

        /** @var ExtensionsManager $extensionManager */
        $extensionManager = $serviceManager->get(ExtensionsManager::SERVICE_ID);
        $extension = $extensionManager->getExtensionById('tao');

        $config = $extension->getConfig('client_lib_config_registry');
        unset($config['taoTests/controller/tests/action']);

        $extension->setConfig('client_lib_config_registry', $config);
    }
}
