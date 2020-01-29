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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoTests\test\unit\runner\providers;

use oat\generis\test\TestCase;
use oat\taoTests\models\runner\providers\ProviderRegistry;

/**
 * Test the ProviderRegistry
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class ProviderRegistryTest extends TestCase
{

    private static $map = [
        [
            'id'          => 'qti',
            'name'        => 'QTI runner',
            'module'      => 'taoQtiTest/runner/provider/qti',
            'bundle'      => 'taoQtiTest/loader/qtiTestRunner.min',
            'description' => 'QTI implementation of the test runner',
            'category'    => 'runner',
            'active'      => true,
            'tags'        => [ 'core', 'qti', 'runner' ]
        ], [
            'id'          => 'qtiprint',
            'name'        => 'QTI runner for paper',
            'module'      => 'taoQtiPrint/runner/provider/qtiprint',
            'bundle'      => 'taoQtiPrint/loader/qtiTestRunner.min',
            'description' => 'QTI implementation of the test runner on paper',
            'category'    => 'runner',
            'active'      => true,
            'tags'        => [ 'core', 'qti', 'runner', 'print' ]
        ], [
            'id'       => 'request',
            'name'     => 'request communicator',
            'module'   => 'core/communicator/request',
            'bundle'   => 'loader/vendor.min',
            'category' => 'communicator',
            'active'   => true,
            'tags'     => [ ]
        ], [
            'id'       => 'sockets',
            'name'     => 'web sockets communicator',
            'module'   => 'core/communicator/ws',
            'bundle'   => 'loader/vendor.min',
            'category' => 'communicator',
            'active'   => true,
            'tags'     => [ ]
        ], [
            'id'       => 'poll',
            'name'     => 'poll communicator',
            'module'   => "core/communicator/poll",
            'bundle'   => 'loader/vendor.min',
            'category' => 'communicator',
            'active'   => true,
            'tags'     => [ ]
        ], [
            'id' => 'qtiServiceProxy',
            'module' => 'taoQtiTest/runner/proxy/qtiServiceProxy',
            'bundle' => 'taoQtiTest/loader/qtiTestRunner.min',
            'category' => 'proxy',
            'active'   => true,
            'tags'     => [ ]
        ]
    ];

    /**
     * Data provider
     * @return array the data
     */
    public function categoryFindingProvider()
    {
        return [
            [ null, [] ],
            [ '', [] ],
            [ 'foo', [] ],
            [ 'runner', [self::$map[0], self::$map[1]] ],
            [ 'communicator', [self::$map[2], self::$map[3],self::$map[4]]  ],
            [ 'proxy', [self::$map[5]] ],
        ];
    }

    /**
     * Test getting providers by category
     * @dataProvider categoryFindingProvider
     */
    public function testGetByCategory($category, $expectedProviders)
    {
        $registry = $this->getMockBuilder(ProviderRegistry::class)
                ->setMethods(['getMap', 'setConfig'])
                ->disableOriginalConstructor()
                ->getMock();
        $registry->method('getMap')->willReturn(self::$map);

        $providers = $registry->getByCategory($category);
        $this->assertEquals(array_values($providers), $expectedProviders);
    }

    /**
     * Test removing providers from a category
     */
    public function testRemoveByCategory()
    {
        $map = self::$map;
        $registry = $this->getMockBuilder(ProviderRegistry::class)
                ->setMethods(['getMap', 'setConfig', 'remove'])
                ->disableOriginalConstructor()
                ->getMock();

        $registry->method('getMap')->willReturn($map);

        $registry->expects($this->once())
             ->method('remove')
             ->with('taoQtiTest/runner/proxy/qtiServiceProxy');

        $registry->removeByCategory('proxy');
    }

    /**
     * Test removing providers from a wrong category
     */
    public function testRemoveByWrongCategory()
    {
        $map = self::$map;
        $registry = $this->getMockBuilder(ProviderRegistry::class)
                ->setMethods(['getMap', 'setConfig', 'remove'])
                ->disableOriginalConstructor()
                ->getMock();

        $registry->method('getMap')->willReturn($map);

        $registry->expects($this->never())
             ->method('remove')
             ->with($this->any());

        $registry->removeByCategory('foo');
    }
}
