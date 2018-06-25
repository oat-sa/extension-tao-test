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
namespace oat\taoTests\models\runner\features;

use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerAwareInterface;

/**
 * A service to register Test Runner Features
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */
class TestRunnerFeatureService extends ConfigurableService implements LoggerAwareInterface {

    use LoggerAwareTrait;

    const SERVICE_ID = 'taoTests/testRunnerFeature';

    const OPTION_AVAILABLE = 'available';

    /**
     * Register a feature
     *
     * @param TestRunnerFeature $testRunnerFeature
     * @return string Id of the registered feature
     * @throws \common_exception_InconsistentData
     */
    public function register(TestRunnerFeature $testRunnerFeature)
    {
        $registeredFeatures = $this->getOption(self::OPTION_AVAILABLE);
        if ($registeredFeatures == null) {
            $registeredFeatures = [];
        }

        $featureId = $testRunnerFeature->getId();

        if (array_key_exists($featureId, $registeredFeatures)) {
            throw new \common_exception_InconsistentData('Cannot register two features with the same id ' . $featureId);
        }

        $registeredFeatures[$featureId] = $testRunnerFeature;
        $this->setOption(self::OPTION_AVAILABLE, $registeredFeatures);

        return $featureId;
    }

    /**
     * Unregister a feature
     *
     * @param string $featureId
     * @throws \common_exception_InconsistentData
     */
    public function unregister($featureId) {
        $registeredFeatures = $this->getOption(self::OPTION_AVAILABLE);
        if (is_array($registeredFeatures) && array_key_exists($featureId, $registeredFeatures)) {
            unset($registeredFeatures[$featureId]);
            $this->setOption(self::OPTION_AVAILABLE, $registeredFeatures);
        } else {
            $this->logWarning('Cannot unregister inexistant feature ' . $featureId);
        }
    }

    /**
     * Return all available features
     * @param boolean $filterDisabled
     * @return TestRunnerFeature[]
     */
    public function getAll($filterDisabled = true)
    {
        $result = [];
        /** @var TestRunnerFeatureInterface[] $features */
        $features = $this->getOption(self::OPTION_AVAILABLE);
        if (!$features) {
            $features = [];
        }
        foreach ($features as $id => $feature) {
            $feature->setServiceLocator($this->getServiceLocator());
            $this->propagate($feature);
            if ($filterDisabled) {
                if ($feature->isActive()) {
                    $result[$id] = $feature;
                }
            } else {
                $result[$id] = $feature;
            }
        }
        return $result;
    }
}
