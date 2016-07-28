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
namespace oat\taoTests\test\runner\plugins;

use Prophecy\Argument;
use Prophecy\Prophet;
use oat\taoTests\models\runner\plugins\TestPlugin;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPluginTest extends TaoPhpUnitTestRunner
{

    public function accessorsProvider()
    {
        return [
            [
                [
                    'id'          => 'foo',
                    'name'        => 'Foo',
                    'description' => 'The best foo ever',
                    'active'      => true,
                    'tags'        => ['required']
                ], [
                    'id'          => 'foo',
                    'name'        => 'Foo',
                    'description' => 'The best foo ever',
                    'active'      => true,
                    'tags'        => ['required']
                ]
            ], [
                [
                    'id'          => 12,
                    'name'        => 21,
                ], [
                    'id'          => '12',
                    'name'        => '21',
                    'description' => '',
                    'active'      => true,
                    'tags'        => []
                ]
            ], [
                [
                    'id'          => null,
                    'name'        => null,
                    'description' => null,
                    'active'      => null,
                    'tags'        => null
                ], [
                    'id'          => '',
                    'name'        => '',
                    'description' => '',
                    'active'      => true,
                    'tags'        => []
                ]
            ]
        ];
    }

    /**
     *
     * @dataProvider accessorsProvider
     */
    public function testAccessors($input, $output)
    {

        if(isset($input['description'])){
            $testPlugin = new TestPlugin($input['id'], $input['name'], $input['description'], $input['active'], $input['tags']);
        } else {
            $testPlugin = new TestPlugin($input['id'], $input['name']);
        }

        $this->assertEquals($output['id'], $testPlugin->getId());
        $this->assertEquals($output['name'], $testPlugin->getName());
        $this->assertEquals($output['description'], $testPlugin->getDescription());
        $this->assertEquals($output['active'], $testPlugin->isActive());
        $this->assertEquals($output['tags'], $testPlugin->getTags());

        $testPlugin->setActive(!$output['active']);
        $this->assertEquals(!$output['active'], $testPlugin->isActive());
    }


    public function testJsonSerialize()
    {
        $expected = '{"id":"bar","name":"Bar","description":"The best bar ever","active":false,"tags":["dummy","goofy"]}';

        $testPlugin = new TestPlugin('bar', 'Bar', 'The best bar ever', false, ['dummy', 'goofy']);

        $serialized = json_encode($testPlugin);

        $this->assertEquals($expected, $serialized);
    }
}
