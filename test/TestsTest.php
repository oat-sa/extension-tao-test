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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
// require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
// include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 */
class TestsTestCase extends TaoPhpUnitTestRunner {

	/**
	 * @var taoTests_models_classes_TestsService
	 */
	protected $testsService = null;

	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
		$this->testsService = taoTests_models_classes_TestsService::singleton();
	}

	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoTests_models_classes_TestsService::__construct
	 */
	public function testService(){
		$this->assertIsA($this->testsService , 'tao_models_classes_Service');
		$this->assertIsA($this->testsService , 'taoTests_models_classes_TestsService');
	}

    /**
     * @return \core_kernel_classes_Class|null
     */
    public function testTests() {
        $this->assertTrue(defined('TAO_TEST_CLASS'));
        $tests = $this->testsService->getRootclass();
        $this->assertIsA($tests, 'core_kernel_classes_Class');
        $this->assertEquals(TAO_TEST_CLASS, $tests->getUri());

        return $tests;
    }

    /**
     * @depends testTests
     * @param $tests
     * @return \core_kernel_classes_Class
     */
    public function testSubTest($tests) {
		$subTestClassLabel = 'subTest class';
		$subTest = $this->testsService->createSubClass($tests, $subTestClassLabel);
		$this->assertIsA($subTest, 'core_kernel_classes_Class');
		$this->assertEquals($subTestClassLabel, $subTest->getLabel());
		$this->assertTrue($this->testsService->isTestClass($subTest));

        return $subTest;
    }

    /**
     * @depends testTests
     * @param $tests
     * @return \core_kernel_classes_Resource
     */
    public function testTestInstance($tests) {
		$testInstanceLabel = 'test instance';
		$testInstance = $this->testsService->createInstance($tests, $testInstanceLabel);
		$this->assertIsA($testInstance, 'core_kernel_classes_Resource');
		$this->assertEquals($testInstanceLabel, $testInstance->getLabel());

        return $testInstance;
    }

    /**
     * @depends testSubTest
     * @param $subTest
     * @return \core_kernel_classes_Resource
     */
    public function testSubTestInstance($subTest) {
		$subTestInstanceLabel = 'subTest instance';
		$subTestInstance = $this->testsService->createInstance($subTest);
		$this->assertTrue(defined('RDFS_LABEL'));
		$subTestInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subTestInstance->setLabel($subTestInstanceLabel);
		$this->assertIsA($subTestInstance, 'core_kernel_classes_Resource');
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

}