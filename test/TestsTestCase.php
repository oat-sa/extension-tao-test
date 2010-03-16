<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

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
	
}
?>