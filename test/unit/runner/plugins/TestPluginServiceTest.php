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
namespace oat\taoTests\test\unit\runner\plugins;

use oat\generis\test\TestCase;
use oat\taoTests\models\runner\plugins\PluginRegistry;
use oat\taoTests\models\runner\plugins\TestPlugin;
use oat\taoTests\models\runner\plugins\TestPluginService;
use Prophecy\Prophet;
use oat\oatbox\service\ConfigurableService;

/**
 * Test the TestPluginService
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPluginServiceTest extends TestCase
{

    //data to stub the regsitry content
    private static $pluginData =  [
        'taoQtiTest/runner/plugins/controls/title/title' => [
                'id' => 'title',
                'module' => 'taoQtiTest/runner/plugins/controls/title/title',
                'bundle' => 'plugins/bundle.min',
                'position' => 1,
                'name' => 'Title indicator',
                'description' => 'Display the title of current test element',
                'category' => 'controls',
                'active' => true,
                'tags' => ['core', 'qti' ]
            ],
            'taoQtiTest/runner/plugins/controls/timer/timer' => [
                'id' => 'timer',
                'module' => 'taoQtiTest/runner/plugins/controls/timer/timer',
                'bundle' => 'plugins/bundle.min',
                'position' => 2,
                'name' => 'Timer indicator',
                'description' => 'Add countdown when remaining time',
                'category' => 'controls',
                'active' => true,
                'tags' => ['core' ]
            ]
        ];

    /**
     * Get the service with the stubbed registry
     * @return TestPluginService
     */
    protected function getTestPluginService()
    {
        $testPluginService = new TestPluginService();

        $prophet = new Prophet();
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend(PluginRegistry::class);
        $prophecy->getMap()->willReturn(self::$pluginData);

        $testPluginService->setRegistry($prophecy->reveal());

        return $testPluginService;
    }

    /**
     * Check the service is a service
     */
    public function testApi()
    {
        $testPluginService = $this->getTestPluginService();
        $this->assertInstanceOf(TestPluginService::class, $testPluginService);
        $this->assertInstanceOf(ConfigurableService::class, $testPluginService);
    }

    /**
     * Test the method TestPluginService::getAllPlugins
     */
    public function testGetAllPlugins()
    {
        $testPluginService = $this->getTestPluginService();

        $plugins = $testPluginService->getAllPlugins();

        $this->assertEquals(2, count($plugins));

        $plugin0 = $plugins['taoQtiTest/runner/plugins/controls/title/title'];
        $plugin1 = $plugins['taoQtiTest/runner/plugins/controls/timer/timer'];

        $this->assertInstanceOf(TestPlugin::class, $plugin0);
        $this->assertInstanceOf(TestPlugin::class, $plugin1);

        $this->assertEquals('title', $plugin0->getId());
        $this->assertEquals('timer', $plugin1->getId());

        $this->assertEquals('Title indicator', $plugin0->getName());
        $this->assertEquals('Timer indicator', $plugin1->getName());

        $this->assertEquals(1, $plugin0->getPosition());
        $this->assertEquals(2, $plugin1->getPosition());

        $this->assertTrue($plugin0->isActive());
        $this->assertTrue($plugin1->isActive());
    }

    /**
     * Test the method TestPluginService::getPlugin
     */
    public function testGetOnePlugin()
    {
        $testPluginService = $this->getTestPluginService();

        $plugin = $testPluginService->getPlugin('timer');

        $this->assertInstanceOf(TestPlugin::class, $plugin);
        $this->assertEquals('timer', $plugin->getId());
        $this->assertEquals('Timer indicator', $plugin->getName());
        $this->assertEquals(2, $plugin->getPosition());
        $this->assertEquals('taoQtiTest/runner/plugins/controls/timer/timer', $plugin->getModule());
        $this->assertEquals('controls', $plugin->getCategory());

        $this->assertTrue($plugin->isActive());
    }

}
