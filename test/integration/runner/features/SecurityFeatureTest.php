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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoTests\test\integration\runner\features;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\taoTests\models\runner\plugins\PluginRegistry;
use oat\taoTests\models\runner\plugins\TestPluginService;
use oat\taoTests\models\runner\features\SecurityFeature;

class SecurityFeatureTest extends GenerisPhpUnitTestRunner
{

    public function testGetPluginsIds()
    {
        $serviceLocatorMock = $this->getServiceLocatorMock([
            TestPluginService::SERVICE_ID => $this->getTestPluginService()
        ]);

        $feature = new SecurityFeature();
        $feature->setServiceLocator($serviceLocatorMock);
        $plugins = $feature->getPluginsIds();
        sort($plugins);
        $this->assertEquals(['secure1', 'secure2'], $plugins);
    }

    //data to stub the registry content
    private static $pluginData =  [
        'taoQtiTest/runner/plugins/myPlugin' => [
            'id' => 'myPlugin',
            'module' => 'baz',
            'category' => 'test',
        ],
        'taoQtiTest/runner/plugins/controls/title/title' => [
            'id' => 'title',
            'module' => 'qux',
            'category' => 'controls',
            'active' => true
        ],
        'taoQtiTest/runner/plugins/controls/timer/timer' => [
            'id' => 'secure1',
            'module' => 'foo',
            'category' => 'security',
            'active' => true
        ],
        'taoQtiTest/runner/plugins/inactive' => [
            'id' => 'secure2',
            'module' => 'bar',
            'category' => 'security',
            'active' => true
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
}
