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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoTests\models\Property;

use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;

class FeatureFlagExcludedPropertyMapper
{
    /** @var array */
    private $propertyIdFeatureFlagsMap;
    /** @var FeatureFlagCheckerInterface  */
    private $featureFlagChecker;

    public function __construct(array $propertyIdFeatureFlagsMap, FeatureFlagCheckerInterface $featureFlagChecker)
    {
        $this->propertyIdFeatureFlagsMap = $propertyIdFeatureFlagsMap;
        $this->featureFlagChecker = $featureFlagChecker;
    }

    public function getExcludedProperties(): array
    {
        $excludedProperties = [];

        foreach ($this->propertyIdFeatureFlagsMap as $propertyId => $relatedFeatureFlags) {
            if ($this->isAnyRelatedFeatureFlagDisabled($relatedFeatureFlags)) {
                $excludedProperties[] = $propertyId;
            }
        }

        return $excludedProperties;
    }

    public function setPropertyIdFeatureFlagsMap(array $map): void
    {
        $this->propertyIdFeatureFlagsMap = $map;
    }

    private function isAnyRelatedFeatureFlagDisabled(array $propertyFeatureFlags): bool
    {
        foreach ($propertyFeatureFlags as $featureFlagVariableName) {
            if (!$this->featureFlagChecker->isEnabled($featureFlagVariableName)) {
                return true;
            }
        }

        return false;
    }
}
