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
use oat\taoTests\models\runner\plugins\PluginRegistry;
use oat\taoTests\test\runner\features\samples\TestFeature;
use oat\taoTests\test\runner\features\samples\TestFeatureEmptyDescription;
use oat\taoTests\test\runner\features\samples\TestFeatureEmptyLabel;
use Prophecy\Prophet;

/**
 * Test of TestRunnerFeatureTest abstract class. Test implementations are in samples folder.
 *
 * @author Christophe Noël <christophe@taotesting.com>
 */
class TestRunnerFeatureTest extends TaoPhpUnitTestRunner
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

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructEmptyId()
    {
        new TestFeature(
            '',
            ['myPlugin'],
            true,
            $this->getTestPluginRegistry()
        );
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructIdNotString()
    {
        new TestFeature(
            69,
            ['myPlugin'],
            true,
            $this->getTestPluginRegistry()
        );
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructEmptyPluginsIds()
    {
        new TestFeature(
            'myId',
            '',
            true,
            $this->getTestPluginRegistry()
        );
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructPluginsIdNotArray()
    {
        new TestFeature(
            'myId',
            'myPlugin',
            true,
            $this->getTestPluginRegistry()
        );
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructPluginsIdNotInRegistry()
    {
        new TestFeature(
            'myId',
            ['iDontExist'],
            true,
            $this->getTestPluginRegistry()
        );
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructPluginsInactive()
    {
        new TestFeature(
            'myId',
            ['inactive'],
            true,
            $this->getTestPluginRegistry()
        );
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
            $this->getTestPluginRegistry()
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
            $this->getTestPluginRegistry()
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
            $this->getTestPluginRegistry()
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
            'new oat\taoTests\test\runner\features\samples\TestFeature()',
            $feature->__toPhpCode()
        );
    }
}

