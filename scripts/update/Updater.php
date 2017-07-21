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
use oat\taoTests\scripts\install\RegisterTestPluginService;
use oat\taoTests\scripts\install\RegisterTestRunnerFeatureService;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\user\TaoRoles;

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
            $registerService([]);

            $this->setVersion('3.0.0');
        }

        $this->skip('3.0.0', '3.4.1');

        if ($this->isVersion('3.4.1')){

            //register test runner feature service
            $registerService = new RegisterTestRunnerFeatureService();
            $registerService([]);

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

        $this->skip('6.0.1', '6.4.0');
	}
}
