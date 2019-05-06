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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTests\scripts\update;

use oat\tao\scripts\update\OntologyUpdater;
use oat\taoTests\models\runner\providers\TestProviderService;
use oat\taoTests\scripts\install\RegisterTestPluginService;
use oat\taoTests\scripts\install\RegisterTestRunnerFeatureService;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\user\TaoRoles;
use oat\taoTests\models\runner\features\TestRunnerFeatureService;
use oat\taoTests\models\runner\features\SecurityFeature;

class Updater extends \common_ext_ExtensionUpdater
{
	/**
     *
     * @param string $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion)
    {
        if ($this->isBetween('0', '2.7')){
            $this->setVersion('2.7');
        }

		// remove active prop
		if ($this->isVersion('2.7')){
		    $deprecatedProperty = new \core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#active');
		    $iterator = new \core_kernel_classes_ResourceIterator(array(\taoTests_models_classes_TestsService::singleton()->getRootClass()));
		    foreach ($iterator as $resource) {
		        $resource->removePropertyValues($deprecatedProperty);
		    }
		    $this->setVersion('2.7.1');
		}

        $this->skip('2.7.1', '2.23.0');

        if ($this->isVersion('2.23.0')){

            //register test plugin service
            $registerService = new RegisterTestPluginService();
            $registerService->setServiceLocator($this->getServiceManager());
            $registerService([]);

            $this->setVersion('3.0.0');
        }

        $this->skip('3.0.0', '3.4.1');

        if ($this->isVersion('3.4.1')){

            //register test runner feature service
            //$registerService = new RegisterTestRunnerFeatureService();
            //$registerService([]);

            $this->setVersion('3.5.0');
        }

        $this->skip('3.5.0', '3.5.1');

        if ($this->isVersion('3.5.1')) {
            OntologyUpdater::syncModels();
            $this->setVersion('3.6.0');
        }

        $this->skip('3.6.0', '6.0.0');

        // remove anonymous access
        if ($this->isVersion('6.0.0')) {
            AclProxy::revokeRule(new AccessRule(AccessRule::GRANT, TaoRoles::ANONYMOUS, \taoTests_actions_RestTests::class));
            $this->setVersion('6.0.1');
        }

        $this->skip('6.0.1', '6.10.0');

        if ($this->isVersion('6.10.0')){

            //register test plugin service
            $serviceManager = $this->getServiceManager();
            $testProviderService = new TestProviderService();
            $serviceManager->register(TestProviderService::SERVICE_ID, $testProviderService);

            $this->setVersion('6.11.0');
        }

        $this->skip('6.11.0', '7.3.0');

        if ($this->isVersion('7.3.0')) {
            AclProxy::applyRule(new AccessRule('grant', TaoRoles::REST_PUBLISHER, array('ext'=>'taoTests', 'mod' => 'RestTests')));
            $this->setVersion('7.4.0');
        }

        $this->skip('7.4.0', '7.7.1');

        if ($this->isVersion('7.7.1')) {
            if (!$this->getServiceManager()->has(TestRunnerFeatureService::SERVICE_ID)) {
                $featureService = new TestRunnerFeatureService([
                    TestRunnerFeatureService::OPTION_AVAILABLE => []
                ]);
                $this->getServiceManager()->register(TestRunnerFeatureService::SERVICE_ID, $featureService);
            }
            $this->setVersion('7.7.2');
        }

        $this->skip('7.7.2', '7.7.3');

        if ($this->isVersion('7.7.3')) {
            $featureService = $this->getServiceManager()->get(TestRunnerFeatureService::class);
            $features = $featureService->getAll(false);
            if (isset($features[SecurityFeature::FEATURE_ID]) && get_class($features[SecurityFeature::FEATURE_ID]) === SecurityFeature::class) {
                $featureService->unregister(SecurityFeature::FEATURE_ID);
                $this->getServiceManager()->register(TestRunnerFeatureService::SERVICE_ID, $featureService);
            }
            $this->setVersion('7.8.0');
        }

        $this->skip('7.8.0', '8.4.1');
    }
}
