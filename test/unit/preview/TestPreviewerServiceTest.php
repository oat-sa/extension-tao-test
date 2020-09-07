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

namespace oat\taoTests\test\unit\preview;

use Prophecy\Prophet;
use Prophecy\Argument;
use oat\generis\test\TestCase;
use oat\tao\model\modules\DynamicModule;
use oat\tao\model\ClientLibConfigRegistry;
use oat\oatbox\service\ConfigurableService;
use oat\taoTests\models\preview\TestPreviewerService;
use common_exception_InconsistentData as InconsistentDataException;

/**
 * Class TestPreviewerServiceTest
 *
 * @package oat\taoTests\test\unit\preview
 */
class TestPreviewerServiceTest extends TestCase
{
    private const ADAPTER_DATA = [
        'taoTests/previewer/factory' => [
            'previewers' => [
                'taoQtiTestPreviewer/previewer/adapter/test/qtiTest' => [
                    'id' => 'qtiTest',
                    'module' => 'taoQtiTestPreviewer/previewer/adapter/test/qtiTest',
                    'bundle' => 'taoQtiTestPreviewer/loader/qtiPreviewer.min',
                    'position' => null,
                    'name' => 'QTI Test Previewer',
                    'description' => 'QTI implementation of the test previewer',
                    'category' => 'previewer',
                    'active' => true,
                    'tags' => [
                        'core',
                        'qti',
                        'previewer',
                    ],
                ],
                'taoQtiTestPreviewer/previewer/adapter/item/qtiItem' => [
                    'id' => 'qtiItem',
                    'module' => 'taoQtiTestPreviewer/previewer/adapter/item/qtiItem',
                    'bundle' => 'taoQtiTestPreviewer/loader/qtiPreviewer.min',
                    'position' => null,
                    'name' => 'QTI Item Previewer',
                    'description' => 'QTI implementation of the item previewer',
                    'category' => 'previewer',
                    'active' => false,
                    'tags' => [
                        'core',
                        'qti',
                        'previewer',
                    ],
                ],
            ],
        ],
    ];

    /** @var TestPreviewerService */
    private $sut;

    /**
     * @before
     */
    public function init(): void
    {
        $this->sut = new TestPreviewerService();
        $this->sut->setRegistry($this->createAdapter());
    }

    public function testApi(): void
    {
        $this->assertInstanceOf(TestPreviewerService::class, $this->sut);
        $this->assertInstanceOf(ConfigurableService::class, $this->sut);
    }

    public function testGetAdapters(): void
    {
        $adapters = $this->sut->getAdapters();

        $this->assertCount(2, $adapters);

        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/test/qtiTest', $adapters);
        $secondAdapter = $adapters['taoQtiTestPreviewer/previewer/adapter/test/qtiTest'];
        $this->assertArrayHasKey('id', $secondAdapter);
        $this->assertArrayHasKey('module', $secondAdapter);
        $this->assertArrayHasKey('bundle', $secondAdapter);
        $this->assertArrayHasKey('category', $secondAdapter);
        $this->assertArrayHasKey('active', $secondAdapter);

        $this->assertEquals('qtiTest', $secondAdapter['id']);
        $this->assertEquals('taoQtiTestPreviewer/previewer/adapter/test/qtiTest', $secondAdapter['module']);
        $this->assertEquals('taoQtiTestPreviewer/loader/qtiPreviewer.min', $secondAdapter['bundle']);
        $this->assertEquals('previewer', $secondAdapter['category']);
        $this->assertEquals(true, $secondAdapter['active']);

        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/item/qtiItem', $adapters);
        $firstAdapter = $adapters['taoQtiTestPreviewer/previewer/adapter/item/qtiItem'];
        $this->assertArrayHasKey('id', $firstAdapter);
        $this->assertArrayHasKey('module', $firstAdapter);
        $this->assertArrayHasKey('bundle', $firstAdapter);
        $this->assertArrayHasKey('category', $firstAdapter);
        $this->assertArrayHasKey('active', $firstAdapter);

        $this->assertEquals('qtiItem', $firstAdapter['id']);
        $this->assertEquals('taoQtiTestPreviewer/previewer/adapter/item/qtiItem', $firstAdapter['module']);
        $this->assertEquals('taoQtiTestPreviewer/loader/qtiPreviewer.min', $firstAdapter['bundle']);
        $this->assertEquals('previewer', $firstAdapter['category']);
        $this->assertEquals(false, $firstAdapter['active']);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testRegisterAdapter(): void
    {
        $adapters = $this->sut->getAdapters();

        $this->assertCount(2, $adapters);

        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/test/qtiTest', $adapters);
        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/item/qtiItem', $adapters);
        $this->assertArrayNotHasKey('taoQtiTest/previewer/adapter/qtiMock', $adapters);

        $module = DynamicModule::fromArray([
            'id' => 'qtiMock',
            'name' => 'QTI Mock Previewer',
            'module' => 'taoQtiTest/previewer/adapter/qtiMock',
            'bundle' => 'taoQtiTestPreviewer/loader/qtiPreviewer.min',
            'description' => 'QTI implementation of the test previewer',
            'category' => 'previewer',
            'active' => true,
            'tags' => ['core', 'qti', 'previewer'],
        ]);
        $this->assertEquals(true, $this->sut->registerAdapter($module));

        $adapters = $this->sut->getAdapters();
        $this->assertCount(3, $adapters);
        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/test/qtiTest', $adapters);
        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/item/qtiItem', $adapters);
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiMock', $adapters);
    }

    public function testUnregisterAdapter(): void
    {
        $adapters = $this->sut->getAdapters();

        $this->assertCount(2, $adapters);

        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/test/qtiTest', $adapters);
        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/item/qtiItem', $adapters);

        $this->assertEquals(
            true,
            $this->sut->unregisterAdapter('taoQtiTestPreviewer/previewer/adapter/test/qtiTest')
        );

        $adapters = $this->sut->getAdapters();
        $this->assertCount(1, $adapters);
        $this->assertArrayNotHasKey('taoQtiTestPreviewer/previewer/adapter/test/qtiTest', $adapters);
        $this->assertArrayHasKey('taoQtiTestPreviewer/previewer/adapter/item/qtiItem', $adapters);

        $this->assertEquals(
            false,
            $this->sut->unregisterAdapter('taoQtiTestPreviewer/previewer/adapter/test/qtiTest')
        );
    }

    /**
     * @return ClientLibConfigRegistry
     */
    private function createAdapter(): ClientLibConfigRegistry
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize(ClientLibConfigRegistry::class);
        $data = self::ADAPTER_DATA;

        $prophecy
            ->isRegistered(Argument::type('string'))
            ->will(function ($args) use (&$data) {
                return isset($data[$args[0]]);
            });
        $prophecy
            ->get(Argument::type('string'))
            ->will(function ($args) use (&$data) {
                return $data[$args[0]];
            });
        $prophecy
            ->set(Argument::type('string'), Argument::type('array'))
            ->will(function ($args) use (&$data) {
                $data[$args[0]] = $args[1];
            });

        return $prophecy->reveal();
    }
}
