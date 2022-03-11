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

namespace oat\taoTests\models\classes\services;

use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;

class FeatureFlagFormTestPropertyMapper
{
    /** @var array */
    private $params;
    /** @var FeatureFlagCheckerInterface  */
    private $featureFlagChecker;

    public function __construct(array $params, FeatureFlagCheckerInterface $featureFlagChecker)
    {
        $this->params = $params;
        $this->featureFlagChecker = $featureFlagChecker;
    }

    public function getExcludedProperties(): array
    {
        $excludedProperties = [];

        foreach ($this->params as $field => $featureFlags) {
            foreach ($featureFlags as $featureFlag) {
                if (!$this->featureFlagChecker->isEnabled($featureFlag)) {
                    $excludedProperties[] = $field;
                }
            }
        }

        return array_unique($excludedProperties);
    }
}
