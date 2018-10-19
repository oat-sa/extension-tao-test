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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoTests\test\unit\runner\providers;

use oat\generis\test\TestCase;
use oat\oatbox\service\ConfigurableService;
use oat\taoTests\models\runner\providers\ProviderRegistry;
use oat\taoTests\models\runner\providers\TestProvider;
use oat\taoTests\models\runner\providers\TestProviderService;
use Prophecy\Prophet;

/**
 * Test the TestProviderService
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class TestProviderServiceTest extends TestCase
{

    //data to stub the regiitry content
    private static $providerData = [
        'taoQtiTest/runner/providers/qtiRunner' => [
            'id' => 'qtiRunner',
            'module' => 'taoQtiTest/runner/providers/qtiRunner',
            'bundle' => 'providers/bundle.min',
            'name' => 'QTI runner',
            'description' => 'QTI implementation of the test runner',
            'category' => 'runner',
            'active' => true,
            'tags' => ['core', 'qti', 'runner']
        ],
        'taoQtiTest/runner/providers/qtiPreviewer' => [
            'id' => 'qtiPreviewer',
            'module' => 'taoQtiTest/runner/providers/qtiPreviewer',
            'bundle' => 'providers/bundle.min',
            'name' => 'QTI previewer',
            'description' => 'QTI implementation of the test/item previewer',
            'category' => 'previewer',
            'active' => true,
            'tags' => ['core', 'qti', 'previewer']
        ]
    ];

    /**
     * Get the service with the stubbed registry
     * @return TestProviderService
     */
    protected function getTestProviderService()
    {
        $testProviderService = new TestProviderService();

        $prophet = new Prophet();
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend(ProviderRegistry::class);
        $prophecy->getMap()->willReturn(self::$providerData);

        $testProviderService->setRegistry($prophecy->reveal());

        return $testProviderService;
    }

    /**
     * Check the service is a service
     */
    public function testApi()
    {
        $testProviderService = $this->getTestProviderService();
        $this->assertInstanceOf(TestProviderService::class, $testProviderService);
        $this->assertInstanceOf(ConfigurableService::class, $testProviderService);
    }

    /**
     * Test the method TestProviderService::getAllProviders
     */
    public function testGetAllProviders()
    {
        $testProviderService = $this->getTestProviderService();

        $providers = $testProviderService->getAllProviders();

        $this->assertEquals(2, count($providers));

        $provider0 = $providers['taoQtiTest/runner/providers/qtiRunner'];
        $provider1 = $providers['taoQtiTest/runner/providers/qtiPreviewer'];

        $this->assertInstanceOf(TestProvider::class, $provider0);
        $this->assertInstanceOf(TestProvider::class, $provider1);

        $this->assertEquals('qtiRunner', $provider0->getId());
        $this->assertEquals('qtiPreviewer', $provider1->getId());

        $this->assertEquals('QTI runner', $provider0->getName());
        $this->assertEquals('QTI previewer', $provider1->getName());

        $this->assertTrue($provider0->isActive());
        $this->assertTrue($provider1->isActive());
    }

    /**
     * Test the method TestProviderService::getProvider
     */
    public function testGetOneProvider()
    {
        $testProviderService = $this->getTestProviderService();

        $provider = $testProviderService->getProvider('qtiRunner');

        $this->assertInstanceOf(TestProvider::class, $provider);
        $this->assertEquals('qtiRunner', $provider->getId());
        $this->assertEquals('QTI runner', $provider->getName());
        $this->assertEquals('taoQtiTest/runner/providers/qtiRunner', $provider->getModule());
        $this->assertEquals('runner', $provider->getCategory());

        $this->assertTrue($provider->isActive());
    }

}
