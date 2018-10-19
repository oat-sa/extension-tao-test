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
 */

namespace oat\taoTests\test\unit\runner\providers;

use common_exception_InconsistentData;
use oat\generis\test\TestCase;
use oat\taoTests\models\runner\providers\TestProvider;

/**
 * Test the TestProvider pojo
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class TestProviderTest extends TestCase
{

    /**
     * Data provider
     * @return array the data
     */
    public function accessorsProvider()
    {
        return [
            [
                [
                    'id' => 'foo',
                    'name' => 'Foo',
                    'module' => 'provider/foo',
                    'category' => 'dummy',
                    'description' => 'The best foo ever',
                    'active' => true,
                    'tags' => ['required']
                ], [
                'id' => 'foo',
                'name' => 'Foo',
                'module' => 'provider/foo',
                'category' => 'dummy',
                'description' => 'The best foo ever',
                'active' => true,
                'tags' => ['required']
            ]
            ], [
                [
                    'id' => '12',
                    'name' => 21,
                    'module' => 'provider/foo',
                    'category' => 'dummy',
                ], [
                    'id' => '12',
                    'name' => '21',
                    'module' => 'provider/foo',
                    'category' => 'dummy',
                    'description' => '',
                    'active' => true,
                    'tags' => []
                ]
            ]
        ];
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructBadId()
    {
        new TestProvider(12, 'foo', 'bar');
    }


    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructEmptyId()
    {
        new TestProvider('', 'foo', 'bar');
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructBadModule()
    {
        new TestProvider('foo', true, 'bar');
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructiEmptyModule()
    {
        new TestProvider('foo', '', 'bar');
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructBadCategory()
    {
        new TestProvider('foo', 'bar', []);
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testConstructNoCategory()
    {
        new TestProvider('foo', 'bar', null);
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testFromArrayNoRequiredData()
    {
        TestProvider::fromArray([]);
    }

    /**
     * Test contructor and getter
     * @dataProvider accessorsProvider
     */
    public function testConstruct($input, $output)
    {

        $testProvider = new TestProvider($input['id'], $input['module'], $input['category'], $input);

        $this->assertEquals($output['id'], $testProvider->getId());
        $this->assertEquals($output['name'], $testProvider->getName());
        $this->assertEquals($output['module'], $testProvider->getModule());
        $this->assertEquals($output['category'], $testProvider->getCategory());
        $this->assertEquals($output['description'], $testProvider->getDescription());
        $this->assertEquals($output['active'], $testProvider->isActive());
        $this->assertEquals($output['tags'], $testProvider->getTags());

        $testProvider->setActive(!$output['active']);
        $this->assertEquals(!$output['active'], $testProvider->isActive());
    }

    /**
     * Test from array and getters
     * @dataProvider accessorsProvider
     */
    public function testFromArray($input, $output)
    {

        $testProvider = TestProvider::fromArray($input);

        $this->assertEquals($output['id'], $testProvider->getId());
        $this->assertEquals($output['name'], $testProvider->getName());
        $this->assertEquals($output['module'], $testProvider->getModule());
        $this->assertEquals($output['category'], $testProvider->getCategory());
        $this->assertEquals($output['description'], $testProvider->getDescription());
        $this->assertEquals($output['active'], $testProvider->isActive());
        $this->assertEquals($output['tags'], $testProvider->getTags());

        $testProvider->setActive(!$output['active']);
        $this->assertEquals(!$output['active'], $testProvider->isActive());
    }

    /**
     * Test encoding the object to json
     */
    public function testJsonSerialize()
    {
        $expected = '{"id":"bar","module":"bar\/bar","bundle":"providers\/bundle.min","position":1,"name":"Bar","description":"The best bar ever","category":"dummy","active":false,"tags":["dummy","goofy"]}';

        $testProvider = new TestProvider('bar', 'bar/bar', 'dummy', [
            'name' => 'Bar',
            'description' => 'The best bar ever',
            'active' => false,
            'bundle' => 'providers/bundle.min',
            'tags' => ['dummy', 'goofy'],
            'position' => 1,
        ]);

        $serialized = json_encode($testProvider);

        $this->assertEquals($expected, $serialized);
    }
}
