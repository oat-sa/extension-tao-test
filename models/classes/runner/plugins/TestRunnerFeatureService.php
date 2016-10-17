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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoTests\models\runner\plugins;

use oat\oatbox\service\ConfigurableService;

/**
 * @author Christophe Noël <christophe@taotesting.com>
 */
class TestRunnerFeatureService extends ConfigurableService {

    const SERVICE_ID = 'taoTests/testRunnerFeature';

    const OPTION_AVAILABLE = 'available';

    /**
     * Register a feature
     *
     * @param TestRunnerFeature $testRunnerFeature
     * @return string Id of the registered feature
     */
    public function register(TestRunnerFeature $testRunnerFeature)
    {
        $registeredFeatures = $this->getOption(self::OPTION_AVAILABLE);
        $baseId = method_exists($testRunnerFeature, 'getId') ? $testRunnerFeature->getId() : '';
        $nr = 0;
        while (isset($registeredFeatures[$baseId.$nr])) {
            $nr++;
        }
        $registeredFeatures[$baseId.$nr] = $testRunnerFeature;
        $this->setOption(self::OPTION_AVAILABLE, $registeredFeatures);
        return $baseId.$nr;
    }


    /**
     * Return all available features
     *
     * @return TestRunnerFeature[]
     */
    public function getAll()
    {
        return $this->getOption(self::OPTION_AVAILABLE);
    }

}
