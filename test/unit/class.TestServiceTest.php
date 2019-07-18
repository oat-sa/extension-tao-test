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
 *
 */

use oat\generis\test\TestCase;
use \taoTests_models_classes_TestsService;
use \oat\taoTests\models\runner\features\TestRunnerFeatureService;

class TestServiceTest extends TestCase
{
    private function getServiceTestMock()
    {
        return $this->getMockBuilder(taoTests_models_classes_TestsService::class)
            ->disableOriginalConstructor()
            ->setMethods(['isTestClass', 'getUri'])
            ->getMock();
    }

    public function testDeleteTestClass()
    {
        $testServiceMock = $this->getServiceTestMock();
        $testServiceMock->method('isTestClass')->willReturn(true);

        $coreClassMock = $this->getMockBuilder(core_kernel_classes_Class::class)
            ->setConstructorArgs(['http://www.tao.lu/Ontologies/TAOTest.rdf#Test'])
            ->setMethods(['delete'])
            ->getMock();
        $coreClassMock->method('delete')->willReturn(true);

        $coreClassMock1 = $this->getMockBuilder(core_kernel_classes_Class::class)
            ->setConstructorArgs(['true'])
            ->setMethods(['delete'])
            ->getMock();
        $coreClassMock1->method('delete')->willReturn(true);

        $this->assertFalse($testServiceMock->deleteTestClass($coreClassMock));
        $this->assertTrue($testServiceMock->deleteTestClass($coreClassMock1));
    }

    public function testGetPropertyByUri()
    {
        $service = new taoTests_models_classes_TestsService();
        $this->assertInstanceOf(
            core_kernel_classes_Property::class,
            $service->getPropertyByUri(taoTests_models_classes_TestsService::PROPERTY_TEST_TESTMODEL)
        );
    }

    public function testGetTestModel()
    {
        $service = new taoTests_models_classes_TestsService();
        $resourceMock = $this->getMockBuilder(core_kernel_classes_Resource::class)
            ->setConstructorArgs(['true'])
            ->getMock();
        
        try {
            $this->assertInstanceOf(
                core_kernel_classes_Container::class,
                $service->getTestModel($resourceMock)
            );
        } catch (\Exception $e) {
            $this->assertEquals('Undefined testmodel for test ', $e->getMessage());
        }
    }
}
