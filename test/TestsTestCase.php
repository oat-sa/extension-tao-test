<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/constants.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage test
 */
class TestsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoTests_models_classes_TestsService
	 */
	protected $testsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoTests_models_classes_TestsService::__construct
	 */
	public function testService(){
		
		$testsService = tao_models_classes_ServiceFactory::get('Tests');
		$this->assertIsA($testsService, 'tao_models_classes_Service');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		$this->testsService = $testsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the test class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_TEST_CLASS'));
		$testClass = $this->testsService->getTestClass();
		$this->assertIsA($testClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_TEST_CLASS, $testClass->uriResource);
		
		//create a subclass
		$subTestClassLabel = 'subTest class';
		$subTestClass = $this->testsService->createSubClass($testClass, $subTestClassLabel);
		$this->assertIsA($subTestClass, 'core_kernel_classes_Class');
		$this->assertEqual($subTestClassLabel, $subTestClass->getLabel());
		$this->assertTrue($this->testsService->isTestClass($subTestClass));
		
		//create instance of Test
		$testInstanceLabel = 'test instance';
		$testInstance = $this->testsService->createInstance($testClass, $testInstanceLabel);
		$this->assertIsA($testInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($testInstanceLabel, $testInstance->getLabel());
		
		//create instance of subTest
		$subTestInstanceLabel = 'subTest instance';
		$subTestInstance = $this->testsService->createInstance($subTestClass);
		$this->assertTrue(defined('RDFS_LABEL'));
		$subTestInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subTestInstance->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $subTestInstanceLabel);
		$this->assertIsA($subTestInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subTestInstanceLabel, $subTestInstance->getLabel());
		
		$subTestInstanceLabel2 = 'my sub test instance';
		$subTestInstance->setLabel($subTestInstanceLabel2);
		$this->assertEqual($subTestInstanceLabel2, $subTestInstance->getLabel());
		
		
		//delete test instance
		$this->assertTrue($testInstance->delete());
		
		//delete subclass and check if the instance is deleted
		$subTestInstanceUri = $subTestInstance->uriResource;
		$this->assertNotNull($this->testsService->getTest($subTestInstanceUri));
		$this->assertTrue($subTestInstance->delete());
		$this->assertNull($this->testsService->getTest($subTestInstanceUri));
		
		$this->assertTrue($subTestClass->delete());
	}
	
	
}
?>