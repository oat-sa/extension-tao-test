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

use common_exception_InconsistentData;
use oat\generis\test\TestCase;
use oat\taoTests\models\runner\plugins\TestPlugin;

/**
 * Test the TestPlugin pojo
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPluginTest extends TestCase
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
                    'id'          => 'foo',
                    'name'        => 'Foo',
                    'module'      => 'plugin/foo',
                    'category'    => 'dummy',
                    'description' => 'The best foo ever',
                    'active'      => true,
                    'tags'        => ['required']
                ], [
                    'id'          => 'foo',
                    'name'        => 'Foo',
                    'module'      => 'plugin/foo',
                    'category'    => 'dummy',
                    'description' => 'The best foo ever',
                    'active'      => true,
                    'tags'        => ['required']
                ]
            ], [
                [
                    'id'          => '12',
                    'name'        => 21,
                    'module'      => 'plugin/foo',
                    'category'    => 'dummy',
                ], [
                    'id'          => '12',
                    'name'        => '21',
                    'module'      => 'plugin/foo',
                    'category'    => 'dummy',
                    'description' => '',
                    'active'      => true,
                    'tags'        => []
                ]
            ]
        ];
    }

    public function testConstructBadId()
    {
        $this->expectException(common_exception_InconsistentData::class);
        new TestPlugin(12, 'foo', 'bar');
    }


    public function testConstructEmptyId()
    {
        $this->expectException(common_exception_InconsistentData::class);
        new TestPlugin('', 'foo', 'bar');
    }

    public function testConstructBadModule()
    {
        $this->expectException(common_exception_InconsistentData::class);
        new TestPlugin('foo', true, 'bar');
    }

    public function testConstructiEmptyModule()
    {
        $this->expectException(common_exception_InconsistentData::class);
        new TestPlugin('foo', '', 'bar');
    }

    public function testConstructBadCategory()
    {
        $this->expectException(common_exception_InconsistentData::class);
        new TestPlugin('foo', 'bar', []);
    }

    public function testConstructNoCategory()
    {
        $this->expectException(common_exception_InconsistentData::class);
        new TestPlugin('foo', 'bar', null);
    }

    public function testFromArrayNoRequiredData()
    {
        $this->expectException(common_exception_InconsistentData::class);
        TestPlugin::fromArray([]);
    }

    /**
     * Test contructor and getter
     * @dataProvider accessorsProvider
     */
    public function testConstruct($input, $output)
    {

        $testPlugin = new TestPlugin($input['id'], $input['module'], $input['category'], $input);

        $this->assertEquals($output['id'], $testPlugin->getId());
        $this->assertEquals($output['name'], $testPlugin->getName());
        $this->assertEquals($output['module'], $testPlugin->getModule());
        $this->assertEquals($output['category'], $testPlugin->getCategory());
        $this->assertEquals($output['description'], $testPlugin->getDescription());
        $this->assertEquals($output['active'], $testPlugin->isActive());
        $this->assertEquals($output['tags'], $testPlugin->getTags());

        $testPlugin->setActive(!$output['active']);
        $this->assertEquals(!$output['active'], $testPlugin->isActive());
    }

    /**
     * Test from array and getters
     * @dataProvider accessorsProvider
     */
    public function testFromArray($input, $output)
    {

        $testPlugin = TestPlugin::fromArray($input);

        $this->assertEquals($output['id'], $testPlugin->getId());
        $this->assertEquals($output['name'], $testPlugin->getName());
        $this->assertEquals($output['module'], $testPlugin->getModule());
        $this->assertEquals($output['category'], $testPlugin->getCategory());
        $this->assertEquals($output['description'], $testPlugin->getDescription());
        $this->assertEquals($output['active'], $testPlugin->isActive());
        $this->assertEquals($output['tags'], $testPlugin->getTags());

        $testPlugin->setActive(!$output['active']);
        $this->assertEquals(!$output['active'], $testPlugin->isActive());
    }

    /**
     * Test encoding the object to json
     */
    public function testJsonSerialize()
    {
        $expected = '{"id":"bar","module":"bar\/bar","bundle":"plugins\/bundle.min","position":12,"name":"Bar",'
            . '"description":"The best bar ever","category":"dummy","active":false,"tags":["dummy","goofy"]}';

        $testPlugin = new TestPlugin('bar', 'bar/bar', 'dummy', [
            'name' => 'Bar',
            'description' => 'The best bar ever',
            'active' =>  false,
            'position' => 12,
            'bundle' => 'plugins/bundle.min',
            'tags' => ['dummy', 'goofy']
        ]);

        $serialized = json_encode($testPlugin);

        $this->assertEquals($expected, $serialized);
    }
}
