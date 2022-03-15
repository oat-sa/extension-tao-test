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

namespace oat\taoTests\test\unit\Property;

use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\taoTests\models\Property\FeatureFlagExcludedPropertyMapper;
use PHPUnit\Framework\TestCase;

class QtiPackageStorageTest extends TestCase
{
    /** @var FeatureFlagExcludedPropertyMapper */
    private $sut;

    /** @var FeatureFlagChecker */
    private $featureFlagChecker;


    public function setUp(): void
    {
        $this->featureFlagChecker = new FeatureFlagChecker();
        $this->sut = new FeatureFlagExcludedPropertyMapper([], $this->featureFlagChecker);
    }

    /**
     * @dataProvider provideValues
     */
    public function testGetExcludedProperties(array $mapFlagsToProperty, array $envValues, array $expected): void
    {
        $this->sut->setPropertyIdFeatureFlagsMap($mapFlagsToProperty);
        $_ENV = array_merge($_ENV, $envValues);

        $actual = $this->sut->getExcludedProperties();

        $this->assertEquals($actual, $expected);
    }

    public function provideValues(): array
    {
        return [
            'allFlagsOn' => [
                'mapFlagsToProperty' => [
                    'property#1' => [
                        'FEATURE_FLAG_1',
                        'FEATURE_FLAG_2',
                    ],
                    'property#2' => [
                        'FEATURE_FLAG_1',
                        'FEATURE_FLAG_2',
                    ],
                ],
                'envValues' => [
                    'FEATURE_FLAG_1' => true,
                    'FEATURE_FLAG_2' => true,
                    'FEATURE_FLAG_3' => true,
                    'FEATURE_FLAG_4' => true,
                ],
                'expected' => []
            ],
            'excludedBothPropsByOneDisabledFlag' => [
                'mapFlagsToProperty' => [
                    'property#1' => [
                        'FEATURE_FLAG_1',
                        'FEATURE_FLAG_2',
                    ],
                    'property#2' => [
                        'FEATURE_FLAG_2',
                        'FEATURE_FLAG_4',
                    ],
                ],
                'envValues' => [
                    'FEATURE_FLAG_1' => true,
                    'FEATURE_FLAG_2' => false,
                    'FEATURE_FLAG_3' => true,
                    'FEATURE_FLAG_4' => true,
                ],
                'expected' => ['property#1', 'property#2']
            ],
            'excludedOneByOneDisabledFlag' => [
                'mapFlagsToProperty' => [
                    'property#1' => [
                        'FEATURE_FLAG_1',
                        'FEATURE_FLAG_2',
                    ],
                    'property#2' => [
                        'FEATURE_FLAG_3',
                        'FEATURE_FLAG_4',
                    ],
                ],
                'envValues' => [
                    'FEATURE_FLAG_1' => true,
                    'FEATURE_FLAG_2' => true,
                    'FEATURE_FLAG_3' => true,
                    'FEATURE_FLAG_4' => false,
                ],
                'expected' => ['property#2']
            ],
            'allFlagsOff' => [
                'mapFlagsToProperty' => [
                    'property#1' => [
                        'FEATURE_FLAG_1',
                        'FEATURE_FLAG_2',
                    ],
                    'property#2' => [
                        'FEATURE_FLAG_3',
                        'FEATURE_FLAG_4',
                    ],
                ],
                'envValues' => [
                    'FEATURE_FLAG_1' => false,
                    'FEATURE_FLAG_2' => false,
                    'FEATURE_FLAG_3' => false,
                    'FEATURE_FLAG_4' => false,
                ],
                'expected' => ['property#1', 'property#2']
            ],
        ];
    }
}
