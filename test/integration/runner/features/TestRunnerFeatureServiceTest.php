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
namespace oat\taoTests\test\integration\runner\features;

use common_exception_InconsistentData;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\test\unit\oatbox\log\TestLogger;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTests\models\runner\features\TestRunnerFeatureService;
use oat\taoTests\models\runner\plugins\PluginRegistry;
use oat\taoTests\models\runner\plugins\TestPluginService;
use oat\taoTests\test\integration\runner\features\samples\TestFeature;
use Prophecy\Prophet;
use Psr\Log\LogLevel;

/**
 * Test of TestRunnerFeatureServiceTest
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */
class TestRunnerFeatureServiceTest extends GenerisPhpUnitTestRunner
{
    //data to stub the registry content
    private static $pluginData =  [
        'taoQtiTest/runner/plugins/myPlugin' => [
            'id' => 'myPlugin',
            'module' => 'taoQtiTest/runner/plugins/myPlugin',
            'category' => 'test',
            'active' => true
        ],
        'taoQtiTest/runner/plugins/controls/title/title' => [
            'id' => 'title',
            'module' => 'taoQtiTest/runner/plugins/controls/title/title',
            'category' => 'controls',
            'active' => true
        ],
        'taoQtiTest/runner/plugins/controls/timer/timer' => [
            'id' => 'timer',
            'module' => 'taoQtiTest/runner/plugins/controls/timer/timer',
            'category' => 'controls',
            'active' => true
        ]
    ];

    /**
     * Get the service with the stubbed registry
     * @return TestPluginService
     */
    protected function getTestPluginService()
    {
        $testPluginService = new TestPluginService();

        $pluginRegistryProphecy = $this->prophesize(PluginRegistry::class);
        $pluginRegistryProphecy->getMap()->willReturn(self::$pluginData);

        $testPluginService->setRegistry($pluginRegistryProphecy->reveal());

        return $testPluginService;
    }

    public function testGetAll()
    {
        $feature1 = new TestFeature(
            'myId1',
            ['myPlugin'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );

        $feature2 = new TestFeature(
            'myId2',
            ['title', 'timer'],
            false,
            $this->getTestPluginService()->getAllPlugins()
        );

        $feature3 = new TestFeature(
            'myId3',
            ['title', 'timer'],
            false,
            $this->getTestPluginService()->getAllPlugins(),
            false
        );

        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->register($feature1);
        $testRunnerFeatureService->register($feature2);
        $testRunnerFeatureService->register($feature3);
        $testRunnerFeatureService->setServiceLocator($this->getServiceLocatorMock([]));

        $registeredFeatures = $testRunnerFeatureService->getAll();

        $this->assertEquals(2, count($registeredFeatures));
        $this->assertEquals('myId1', $registeredFeatures['myId1']->getId());
        $this->assertEquals('myId2', $registeredFeatures['myId2']->getId());

        $registeredFeatures = $testRunnerFeatureService->getAll(false);
        $this->assertEquals(3, count($registeredFeatures));
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testCannotRegisterTwoFeaturesWithTheSameId() {
        $feature1 = new TestFeature(
            'myId1',
            ['myPlugin'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );
        $feature2 = new TestFeature(
            'myId1',
            ['title', 'timer'],
            false,
            $this->getTestPluginService()->getAllPlugins()
        );
        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->register($feature1);
        $testRunnerFeatureService->register($feature2);
    }

    public function testUnregisterFeature() {
        // first we register 2 features
        $feature1 = new TestFeature(
            'myId1',
            ['myPlugin'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );

        $feature2 = new TestFeature(
            'myId2',
            ['title', 'timer'],
            false,
            $this->getTestPluginService()->getAllPlugins()
        );

        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->register($feature1);
        $testRunnerFeatureService->register($feature2);
        $testRunnerFeatureService->setServiceLocator($this->getServiceLocatorMock([]));

        $registeredFeatures = $testRunnerFeatureService->getAll();

        $this->assertEquals(2, count($registeredFeatures));
        $this->assertEquals('myId1', $registeredFeatures['myId1']->getId());
        $this->assertEquals('myId2', $registeredFeatures['myId2']->getId());

        // then we remove the first one
        $testRunnerFeatureService->unregister('myId1');

        $registeredFeatures = $testRunnerFeatureService->getAll();
        $this->assertEquals(1, count($registeredFeatures));

        // then the second one
        $testRunnerFeatureService->unregister('myId2');

        $registeredFeatures = $testRunnerFeatureService->getAll();
        $this->assertEquals(0, count($registeredFeatures));
    }

    public function testUnregisterBadId() {
        $testLogger = new TestLogger();

        $testRunnerFeatureService = new TestRunnerFeatureService();
        $testRunnerFeatureService->setLogger($testLogger);
        $testRunnerFeatureService->unregister('idontexist');

        $this->assertTrue($testLogger->has(LogLevel::WARNING, 'Cannot unregister inexistant feature idontexist'));
    }
}
