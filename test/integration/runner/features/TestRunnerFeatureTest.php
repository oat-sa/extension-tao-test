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
use oat\generis\test\TestCase;
use oat\generis\test\unit\oatbox\log\TestLogger;
use oat\oatbox\log\LoggerService;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTests\models\runner\plugins\PluginRegistry;
use oat\taoTests\models\runner\plugins\TestPlugin;
use oat\taoTests\models\runner\plugins\TestPluginService;
use oat\taoTests\test\integration\runner\features\samples\TestFeature;
use oat\taoTests\test\integration\runner\features\samples\TestFeatureEmptyDescription;
use oat\taoTests\test\integration\runner\features\samples\TestFeatureEmptyLabel;
use Prophecy\Prophet;
use Psr\Log\LogLevel;

/**
 * Test of TestRunnerFeatureTest abstract class. Test implementations are in samples folder.
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */
class TestRunnerFeatureTest extends TestCase
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
        ],
        'taoQtiTest/runner/plugins/inactive' => [
            'id' => 'inactive',
            'module' => 'taoQtiTest/runner/plugins/inactive',
            'category' => 'test',
            'active' => false
        ]
    ];

    /**
     * Get the service with the stubbed registry
     * @return TestPluginService
     */
    protected function getTestPluginService()
    {
        $prophecy = $this->prophesize(PluginRegistry::class);
        $prophecy->getMap()->willReturn(self::$pluginData);

        $testPluginService = new TestPluginService();
        $testPluginService->setRegistry($prophecy->reveal());

        return $testPluginService;
    }

    /**
     *
     */
    public function provideBadConstructorParameters() {
        return [
            // bad id
            [ '',     ['myPlugin'], true,   $this->getTestPluginService()->getAllPlugins() ],
            [ 69,     ['myPlugin'], true,   $this->getTestPluginService()->getAllPlugins() ],

            // bad pluginId
            [ 'myId', '',           true,   $this->getTestPluginService()->getAllPlugins() ],
            [ 'myId', [],           true,   $this->getTestPluginService()->getAllPlugins() ],
            [ 'myId', [69],         true,   $this->getTestPluginService()->getAllPlugins() ],

            // bad isEnabledByDefault
            [ 'myId', ['myPlugin'], null,   $this->getTestPluginService()->getAllPlugins() ],
            [ 'myId', ['myPlugin'], 'true', $this->getTestPluginService()->getAllPlugins() ],

            // bad allPlugins
            [ 'myId', ['myPlugin'], true,   ''],
            [ 'myId', ['myPlugin'], true,   []],
            [ 'myId', ['myPlugin'], true,   ['myPlugin']],
        ];
    }

    /**
     * @param string        $id
     * @param string[]      $pluginsIds
     * @param bool          $isEnabledByDefault
     * @param TestPlugin[]  $allPlugins
     *
     * @dataProvider provideBadConstructorParameters
     * @expectedException common_exception_InconsistentData
     */
    public function testBadConstructorParameters($id, $pluginsIds, $isEnabledByDefault, $allPlugins) {
        new TestFeature(
            $id,
            $pluginsIds,
            $isEnabledByDefault,
            $allPlugins
        );
    }

    public function testConstructPluginsIdNotInRegistry()
    {
        $feature = new TestFeature(
            'myId',
            ['iDontExist'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );

        $testLogger = new TestLogger();
        $serviceLocatorMock = $this->getServiceLocatorMock([
            LoggerService::SERVICE_ID => $testLogger
        ]);
        $feature->setServiceLocator($serviceLocatorMock);

        $feature->getPluginsIds();
        $this->assertTrue($testLogger->has(LogLevel::WARNING, 'Invalid plugin Id iDontExist for test runner feature myId'));
    }

    public function testConstructPluginsInactive()
    {
        $feature = new TestFeature(
            'myId',
            ['inactive'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );

        $testLogger = new TestLogger();
        $serviceLocatorMock = $this->getServiceLocatorMock([
            LoggerService::SERVICE_ID => $testLogger
        ]);
        $feature->setServiceLocator($serviceLocatorMock);

        $feature->getPluginsIds();
        $this->assertTrue($testLogger->has(LogLevel::WARNING, 'Cannot include inactive plugin inactive in test runner feature myId'));
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructEmptyLabel()
    {
        new TestFeatureEmptyLabel(
            'myId',
            ['myPlugin'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructEmptyDescription()
    {
        new TestFeatureEmptyDescription(
            'myId',
            ['myPlugin'],
            true,
            $this->getTestPluginService()->getAllPlugins()
        );
    }

    private static $featureData = [
        'id'                 => 'myId',
        'pluginsIds'         => ['myPlugin', 'timer', 'title'],
        'isEnabledByDefault' => false
    ];

    public function getTestFeature() {
        return new TestFeature(
            self::$featureData['id'],
            self::$featureData['pluginsIds'],
            self::$featureData['isEnabledByDefault'],
            $this->getTestPluginService()->getAllPlugins()
        );
    }

    public function testConstruct()
    {
        $feature = $this->getTestFeature();

        $this->assertEquals(self::$featureData['id'],                 $feature->getId());
        $this->assertEquals(self::$featureData['pluginsIds'],         $feature->getPluginsIds());
        $this->assertEquals(self::$featureData['isEnabledByDefault'], $feature->isEnabledByDefault());
        $this->assertEquals('testFeature',                            $feature->getLabel());
        $this->assertEquals('A simple feature used for unit testing', $feature->getDescription());
    }

    public function testPhpSerialize() {
        $feature = $this->getTestFeature();

        $this->assertEquals(
            'new oat\taoTests\test\integration\runner\features\samples\TestFeature()',
            $feature->__toPhpCode()
        );
    }
}
