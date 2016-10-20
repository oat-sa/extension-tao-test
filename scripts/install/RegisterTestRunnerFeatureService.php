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
 *
 */

namespace oat\taoTests\scripts\install;

use oat\oatbox\service\ServiceManager;
use oat\taoTests\models\runner\features\TestRunnerFeatureService;

/**
 * Installation action that registers the TestPluginService
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */
class RegisterTestRunnerFeatureService extends \common_ext_action_InstallAction
{
    /**
     * @param $params
     */
    public function __invoke($params)
    {
        $serviceManager = ServiceManager::getServiceManager();

        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->setServiceManager($serviceManager);
        $serviceManager->register(TestRunnerFeatureService::SERVICE_ID, $testRunnerFeatureService);
    }
}

