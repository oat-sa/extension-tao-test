<?php
/*
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoTests\test\unit\pack;

use oat\generis\test\TestCase;
use oat\taoTests\models\pack\TestPack;
use InvalidArgumentException;

/**
 * Test the class {@link TestPack}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 */
class TestPackTest extends TestCase
{

    /**
     * Test creating an TestPack
     */
    public function testConstructor(){
        $type = 'qti';
        $data = array('foo' => 'bar');
        $items= array();

        $pack = new TestPack($type, $data, $items);
        $this->assertInstanceOf(TestPack::class, $pack);
        $this->assertEquals($type, $pack->getType());
        $this->assertEquals($data, $pack->getData());
        $this->assertEquals($items, $pack->getItems());
    }


    /**
     * Test the constructor with an empty type
     */
    public function testWrongTypeConstructor(){
        $this->expectException(InvalidArgumentException::class);

        new TestPack(null, array(), array());
    }

    /**
     * Test the constructor with invalid data
     */
    public function testWrongDataConstructor(){
        $this->expectException(InvalidArgumentException::class);

        new TestPack('qti', '{"foo":"bar"}', array());
    }

    /**
     * Test the constructor with invalid data
     */
    public function testWrongItemsConstructor(){
        $this->expectException(InvalidArgumentException::class);

        new TestPack('qti', array(), 'foo');
    }

    /**
     * Provides data to test the bundle
     * @return array() the data
     */
    public function jsonSerializableProvider(){

        $data = array();



        return $data;
    }

    /**
     * Test the testPack serializaion
     */
    public function testSerialization(){

       $testPack = new TestPack('qti', array('foo' => 'bar'), array('foo', 'bar'));

       $expected = '{"type":"qti","data":{"foo":"bar"},"items":["foo","bar"]}';

       $this->assertInstanceOf(TestPack::class, $testPack);
       $this->assertTrue(is_string($expected));
       $this->assertEquals($expected, json_encode($testPack));
    }

}
