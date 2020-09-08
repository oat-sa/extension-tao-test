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

namespace oat\taoTests\scripts\install;

use common_Exception as Exception;
use common_exception_Error as Error;
use common_ext_Extension as Extension;
use oat\oatbox\extension\InstallAction;
use common_ext_ExtensionsManager as ExtensionsManager;
use oat\taoTests\models\preview\TestPreviewerRegistryService;
use oat\oatbox\service\exception\InvalidServiceManagerException;

/**
 * Class RegisterTestPreviewerRegistryService
 *
 * @package oat\taoTests\scripts\install
 */
class RegisterTestPreviewerRegistryService extends InstallAction
{
    /**
     * @param $params
     *
     * @throws Error
     * @throws Exception
     * @throws InvalidServiceManagerException
     */
    public function __invoke($params)
    {
        $serviceManager = $this->getServiceManager();

        $serviceManager->register(
            TestPreviewerRegistryService::SERVICE_ID,
            new TestPreviewerRegistryService()
        );

        /** @var Extension $extension */
        $extension = $serviceManager->get(ExtensionsManager::SERVICE_ID)->getExtensionById('tao');

        $config = $extension->getConfig('client_lib_config_registry');
        $config['taoTests/controller/tests/action'] = [
            'provider' => 'qtiTest',
        ];

        $extension->setConfig('client_lib_config_registry', $config);
    }
}
