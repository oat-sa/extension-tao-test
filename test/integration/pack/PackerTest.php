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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoTests\test\integration\pack;

use common_Exception;
use taoTests_models_classes_TestsService;
use taoTests_models_classes_TestModel;
use core_kernel_classes_Resource;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\taoTests\models\pack\Packer;
use oat\taoTests\models\pack\Packable;
use oat\taoTests\models\pack\TestPack;

/**
 * Test the class {@link TestPack}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 */
class PackerTest extends GenerisPhpUnitTestRunner
{
    public function setUp(): void
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoTests');
    }

    /**
     * Test creating an TestPack
     */
    public function testConstructor()
    {
        $test = new core_kernel_classes_Resource('toto');
        $packer = new Packer($test);
        $this->assertInstanceOf(Packer::class, $packer);
    }

    /**
     * Test assigning assets to a pack
     */
    public function testPack()
    {
        $test = new core_kernel_classes_Resource('foo');
        $model = new core_kernel_classes_Resource('fooModel');

        $serviceMock = $this
                        ->getMockBuilder(taoTests_models_classes_TestsService::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $modelMock = $this
                        ->getMockBuilder(taoTests_models_classes_TestModel::class)
                        ->getMock();


        $packerMock = new PackerMock();

        $modelMock
            ->method('getPackerClass')
            ->will($this->returnValue(get_class($packerMock)));

        $serviceMock
            ->method('getTestModel')
            ->will($this->returnValue(new core_kernel_classes_Resource('fooModel')));

        $serviceMock
            ->method('getTestModelImplementation')
            ->with($this->equalTo($model))
            ->will($this->returnValue($modelMock));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));


        $packer = new Packer($test);

        $prop = new \ReflectionProperty(Packer::class, 'testService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);


        $result = $packer->pack();
        $this->assertInstanceOf(TestPack::class, $result);
        $this->assertEquals('qti', $result->getType());
        $this->assertEquals(['uri' => $test->getUri()], $result->getData());
    }

    /**
     * Test the exception chain when the test has no model
     */
    public function testNoTestModel()
    {
        $this->expectException(common_Exception::class);
        $test = new core_kernel_classes_Resource('foo');

        $serviceMock = $this
                        ->getMockBuilder(taoTests_models_classes_TestsService::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $serviceMock
            ->method('getTestModel')
            ->will($this->returnValue(null));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));


        $packer = new Packer($test);

        $prop = new \ReflectionProperty(Packer::class, 'testService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }

    /**
     * Test the exception chain when there is no implementations for a model
     *
     */
    public function testNoModelImplementation()
    {
        $this->expectException(common_Exception::class);
        $test = new core_kernel_classes_Resource('foo');
        $model = new core_kernel_classes_Resource('fooModel');

        $serviceMock = $this
                        ->getMockBuilder(taoTests_models_classes_TestsService::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $serviceMock
            ->method('getTestModel')
            ->will($this->returnValue($model));

        $serviceMock
            ->method('getTestModelImplementation')
            ->with($this->equalTo($model))
            ->will($this->returnValue(null));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));


        $packer = new Packer($test);

        $prop = new \ReflectionProperty(Packer::class, 'testService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }

    /**
     * Test the exception chain when the model does not return a correct packer class
     *
     */
    public function testNoPackerClass()
    {
        $this->expectException(common_Exception::class);
        $test = new core_kernel_classes_Resource('foo');

        $serviceMock = $this
                        ->getMockBuilder(taoTests_models_classes_TestsService::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $modelMock = $this
                        ->getMockBuilder(taoTests_models_classes_testModel::class)
                        ->getMock();


        $modelMock
            ->method('getPackerClass')
            ->will($this->returnValue(null));

        $serviceMock
            ->method('getTestModel')
            ->will($this->returnValue(new core_kernel_classes_Resource('fooModel')));

        $serviceMock
            ->method('getTestModelImplementation')
            ->will($this->returnValue($modelMock));

        $serviceMock
            ->method('singleton')
            ->will($this->returnSelf());


        $packer = new Packer($test);

        $prop = new \ReflectionProperty(Packer::class, 'testService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }

    /**
     * Test the exception chain when the model returns a wrong packer class
     *
     */
    public function testWrongPackerClass()
    {
        $this->expectException(common_Exception::class);

        $test = new core_kernel_classes_Resource('foo');

        $serviceMock = $this
                        ->getMockBuilder(taoTests_models_classes_TestsService::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $modelMock = $this
                        ->getMockBuilder(taoTests_models_classes_testModel::class)
                        ->getMock();

        $modelMock
            ->method('getPackerClass')
            ->will($this->returnValue("stdClass"));

        $serviceMock
            ->method('getTestModel')
            ->will($this->returnValue(new core_kernel_classes_Resource('fooModel')));

        $serviceMock
            ->method('getTestModelImplementation')
            ->will($this->returnValue($modelMock));

        $serviceMock
            ->method('singleton')
            ->will($this->returnSelf());


        $packer = new Packer($test);

        $prop = new \ReflectionProperty(Packer::class, 'testService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }
}

//use an old school mock as the Packer create it's own instance from the class
class PackerMock implements Packable
{
    public function packTest(core_kernel_classes_Resource $test)
    {
        return new TestPack('qti', ['uri' => $test->getUri()], []);
    }
}
