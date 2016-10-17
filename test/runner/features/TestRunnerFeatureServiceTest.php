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
 */
namespace oat\taoTests\test\runner\features;

use common_exception_InconsistentData;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTests\models\runner\features\TestRunnerFeatureService;
use oat\taoTests\models\runner\plugins\PluginRegistry;
use oat\taoTests\test\runner\features\samples\TestFeature;
use Prophecy\Prophet;

/**
 * Test of TestRunnerFeatureServiceTest
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */
class TestRunnerFeatureServiceTest extends TaoPhpUnitTestRunner
{
    //data to stub the registry content
    private static $pluginData =  [
        'taoQtiTest/runner/plugins/myPlugin' => [
            'id' => 'myPlugin',
            'module' => 'taoQtiTest/runner/plugins/myPlugin',
            'category' => 'test',
        ],
        'taoQtiTest/runner/plugins/controls/title/title' => [
            'id' => 'title',
            'module' => 'taoQtiTest/runner/plugins/controls/title/title',
            'category' => 'controls',
        ],
        'taoQtiTest/runner/plugins/controls/timer/timer' => [
            'id' => 'timer',
            'module' => 'taoQtiTest/runner/plugins/controls/timer/timer',
            'category' => 'controls',
        ]
    ];

    /**
     * Get the service with the stubbed registry
     * @return PluginRegistry
     */
    protected function getTestPluginRegistry()
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend(PluginRegistry::class);
        $prophecy->getMap()->willReturn(self::$pluginData);

        return $prophecy->reveal();
    }

    public function testGetAll()
    {
        $feature1 = new TestFeature(
            'myId1',
            ['myPlugin'],
            true,
            $this->getTestPluginRegistry()
        );

        $feature2 = new TestFeature(
            'myId2',
            ['title', 'timer'],
            false,
            $this->getTestPluginRegistry()
        );

        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->register($feature1);
        $testRunnerFeatureService->register($feature2);

        $registeredFeatures = $testRunnerFeatureService->getAll();

        $this->assertEquals(2, count($registeredFeatures));
        $this->assertEquals('myId1', $registeredFeatures['myId1']->getId());
        $this->assertEquals('myId2', $registeredFeatures['myId2']->getId());
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testCannotRegisterTwoFeaturesWithTheSameId() {
        $feature1 = new TestFeature(
            'myId1',
            ['myPlugin'],
            true,
            $this->getTestPluginRegistry()
        );
        $feature2 = new TestFeature(
            'myId1',
            ['title', 'timer'],
            false,
            $this->getTestPluginRegistry()
        );
        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->register($feature1);
        $testRunnerFeatureService->register($feature2);
    }

}

