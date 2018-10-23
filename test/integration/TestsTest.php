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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2014 (update and modification) Open Assessment Technologies SA
 *               
 */
namespace oat\taoTests\test\integration;

use tao_models_classes_Service;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\tao\model\TaoOntology;
use Prophecy\Prediction\CallTimesPrediction;
use taoTests_models_classes_TestsService;
use taoTests_models_classes_TestCompiler;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;
use taoTests_models_classes_TestsService as TestService;

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 
 */
class TestsTest extends GenerisPhpUnitTestRunner {

	/**
	 * 
	 * @var taoTests_models_classes_TestsService
	 */
	protected $testsService = null;

	/**
	 * tests initialization
	 */
	public function setUp(){
	    parent::setUp();
		$this->testsService = taoTests_models_classes_TestsService::singleton();
	}

	/**
	 * Test the user service implementation
	 * @see \tao_models_classes_ServiceFactory::get
	 * @see \taoTests_models_classes_TestsService::__construct
	 */
	public function testService(){
		
		$this->assertIsA($this->testsService , tao_models_classes_Service::class);
		$this->assertIsA($this->testsService , taoTests_models_classes_TestsService::class);
	}

    /**
     * @return \core_kernel_classes_Class|null
     */
    public function testTests() {
        $tests = $this->testsService->getRootclass();
        $this->assertIsA($tests, core_kernel_classes_Class::class);
        $this->assertEquals(TaoOntology::TEST_CLASS_URI , $tests->getUri());

        return $tests;
    }


    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return array
     */
    public function getTestModels(){
        $testModelClass = new core_kernel_classes_Class(TestService::CLASS_TEST_MODEL);

        return $testModelClass->getInstances();
    }


    /**
     * @throws \oat\taoTests\models\MissingTestmodelException
     */
    public function testSetTestModel() {
        $test = $this->testsService->getRootclass();
        $models = $this->getTestModels();
        foreach ($models as $uri => $model){
            $this->testsService->setTestModel($test, $model);
            $this->assertEquals($this->testsService->getTestModel($test)->getUri(),$uri);
        }
    }

    public function testGetTestModelImplementationBackwardCompatibleFakeClass(){
        $this->expectException(\common_exception_Error::class);

        $testModelProphecy = $this->prophesize(core_kernel_classes_Resource::class);
        $testModelProphecy->getOnePropertyValue(new core_kernel_classes_Property(TestService::PROPERTY_TEST_MODEL_IMPLEMENTATION))->willReturn('FakeTestModelClass');

        $this->testsService->getTestModelImplementation($testModelProphecy->reveal());
    }

    public function testGetTestModelImplementationBackwardCompatible(){
        $testModelProphecy = $this->prophesize(core_kernel_classes_Resource::class);
        $testModelProphecy->getOnePropertyValue(new core_kernel_classes_Property(TestService::PROPERTY_TEST_MODEL_IMPLEMENTATION))->willReturn(TestModelUnit::class);

        $testModelImp = $this->testsService->getTestModelImplementation($testModelProphecy->reveal());

        $this->assertInstanceOf(TestModelUnit::class, $testModelImp);
    }

    public function testGetTestModelImplementationNullTestModelImplementation(){
        $this->expectException(\common_exception_NoImplementation::class);

        $testModelProphecy = $this->prophesize(core_kernel_classes_Resource::class);
        $testModelProphecy->getOnePropertyValue(new core_kernel_classes_Property(TestService::PROPERTY_TEST_MODEL_IMPLEMENTATION))->willReturn('');
        $testModelProphecy->getUri()->willReturn('testModelUri');
        $testModelProphecy->getUri()->should(new CallTimesPrediction(1));

        $this->testsService->getTestModelImplementation($testModelProphecy->reveal());
    }

    public function testGetTestModelImplementationService(){
        $serviceLocatorMock = $this->getServiceLocatorMock([
            'testModelServiceId' => new TestModelUnit()
        ]);

        $testServiceMock = $this->getMockBuilder(taoTests_models_classes_TestsService::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getServiceManager'))
            ->getMock();

        $testServiceMock->expects($this->once())
            ->method('getServiceManager')
            ->willReturn($serviceLocatorMock);
        $testModelProphecy = $this->prophesize(core_kernel_classes_Resource::class);
        $testModelProphecy->getOnePropertyValue(new core_kernel_classes_Property(TestService::PROPERTY_TEST_MODEL_IMPLEMENTATION))
            ->willReturn('testModelServiceId');

        $testModelImp = $testServiceMock->getTestModelImplementation($testModelProphecy->reveal());

        $this->assertInstanceOf(TestModelUnit::class, $testModelImp);

    }

    /**
     * @throws \ReflectionException
     */
    public function testGetCompilerClass() {
        $test = $this->testsService->getRootclass();

        $models = $this->getTestModels();
        foreach ($models as $uri => $model){
            $this->testsService->setTestModel($test, $model);
            $compilerName = $this->testsService->getCompilerClass($test);
            $compilerClass = new \ReflectionClass($compilerName);
            $this->assertTrue($compilerClass->isSubclassOf(taoTests_models_classes_TestCompiler::class));
        }
    }

    
    
    /**
     * @depends testTests
     * @param $test
     * @return void
     */
    public function testGetTestItems($test) {
        $result = $this->testsService->getTestItems($test);
		$this->assertInternalType('array', $result);
    }

    /**
     * @depends testTests
     * @param $tests
     * @return \core_kernel_classes_Class
     */
    public function testSubTest($tests) {
		$subTestClassLabel = 'subTest class';
		$subTest = $this->testsService->createSubClass($tests, $subTestClassLabel);
		$this->assertIsA($subTest, core_kernel_classes_Class::class);
		$this->assertEquals($subTestClassLabel, $subTest->getLabel());
		$this->assertTrue($this->testsService->isTestClass($subTest));
		$this->assertTrue($this->testsService->isTestClass($tests));
        return $subTest;
    }

    /**
     * @depends testTests
     * @param $tests
     * @return \core_kernel_classes_Resource
     */
    public function testTestInstance($tests) {
		$testInstanceLabel = 'test instance bis';
		$testInstance = $this->testsService->createInstance($tests, $testInstanceLabel);
		$this->assertIsA($testInstance, core_kernel_classes_Resource::class);
		$this->assertEquals($testInstanceLabel, $testInstance->getLabel());

        return $testInstance;
    }

	/**
	 * Test the cloning
     * @depends testSubTest
     * @param $testClass
	 * @return \core_kernel_classes_Class
	 */
    public function testCloneClass($testClass) {
        $clone = $this->testsService->cloneClazz($testClass, $this->testsService->getRootclass());
		$this->assertNotNull($clone);

        return $clone;
    }

	/**
	 * Test the deletion
     * @depends testCloneClass
     * @param $testClass
	 * @return void
	 */
    public function testDeleteTestClass($testClass) {
        $deleted = $this->testsService->deleteClass($testClass);
		$this->assertTrue($deleted);
    }

	/**
	 * Test getAllItems
	 * @return void
	 */
    public function testGetAllItems() {
        $allItems = $this->testsService->getAllItems();
		$this->assertInternalType('array', $allItems);
    }

	/**
	 * Test cloneInstance
     * @depends testTestInstance
     * @param $testInstance
	 * @return \core_kernel_classes_Resource
	 */
    public function testCloneInstance($testInstance) {
		$clone = $this->testsService->cloneInstance($testInstance, $this->testsService->getRootclass());
		$this->assertNotNull($clone);

        return $clone;
    }

    /**
     * @depends testSubTest
     * @param $subTest
     * @return \core_kernel_classes_Resource
     */
    public function testSubTestInstance($subTest) {
		$subTestInstanceLabel = 'subTest instance';
		$subTestInstance = $this->testsService->createInstance($subTest);
		$subTestInstance->removePropertyValues(new core_kernel_classes_Property(OntologyRdfs::RDFS_LABEL));
		$subTestInstance->setLabel($subTestInstanceLabel);
		$this->assertIsA($subTestInstance, core_kernel_classes_Resource::class);
		$this->assertEquals($subTestInstanceLabel, $subTestInstance->getLabel());

        return $subTestInstance;
    }

    /**
     * @depends testSubTestInstance
     * @param $subTestInstance
     */
    public function testSubTestInstanceChangeLabel($subTestInstance) {
		$subTestInstanceLabel = 'my sub test instance';
		$subTestInstance->setLabel($subTestInstanceLabel);
		$this->assertEquals($subTestInstanceLabel, $subTestInstance->getLabel());
    }

    /**
     * @depends testTestInstance
     * @param $testInstance
     */
    public function testDeleteTestInstance($testInstance) {
		$this->assertTrue($testInstance->delete());
    }

    /**
     * @depends testSubTestInstance
     * @param $subTestInstance
     */
    public function testDeleteSubTestInstance($subTestInstance) {
		$this->assertTrue($subTestInstance->delete());
    }

    /**
     * @depends testSubTestInstance
     * @param $subTestInstance
     */
    public function testVerifySubTestInstanceDeletion($subTestInstance) {
		$this->assertFalse($subTestInstance->exists());
    }

    /**
     * @depends testSubTest
     * @param $subTest
     */
    public function testDeleteSubTest($subTest) {
		$this->assertTrue($subTest->delete());
    }

	/**
	 * Test the deletion
     * @depends testTests
     * @param $tests
	 * @return void
	 */
    public function testDeleteTest($tests) {
		$testInstance = $this->testsService->createInstance($tests);
		$this->assertTrue($this->testsService->deleteTest($testInstance));
    }

}


class TestModelUnit implements \taoTests_models_classes_TestModel{
    /**
     * @inheritDoc
     */
    public function prepareContent(core_kernel_classes_Resource $test, $items = array())
    {
        // TODO: Implement prepareContent() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteContent(core_kernel_classes_Resource $test)
    {
        // TODO: Implement deleteContent() method.
    }

    /**
     * @inheritDoc
     */
    public function getItems(core_kernel_classes_Resource $test)
    {
        // TODO: Implement getItems() method.
    }

    /**
     * @inheritDoc
     */
    public function getAuthoringUrl(core_kernel_classes_Resource $test)
    {
        // TODO: Implement getAuthoringUrl() method.
    }

    /**
     * @inheritDoc
     */
    public function cloneContent(core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination)
    {
        // TODO: Implement cloneContent() method.
    }

    /**
     * @inheritDoc
     */
    public function getCompilerClass()
    {
        // TODO: Implement getCompilerClass() method.
    }

    /**
     * @inheritDoc
     */
    public function getPackerClass()
    {
        // TODO: Implement getPackerClass() method.
    }

}
