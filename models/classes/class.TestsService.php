<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoTests/models/classes/class.TestsService.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.10.2009, 09:53:58 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package taoTests
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-constants end

/**
 * Short description of class taoTests_models_classes_TestsService
 *
 * @access public
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package taoTests
 * @subpackage models_classes
 */
class taoTests_models_classes_TestsService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute testClass
     *
     * @access protected
     * @var Class
     */
    protected $testClass = null;

    /**
     * Short description of attribute itemClass
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;

    /**
     * Short description of attribute testsOntologies
     *
     * @access protected
     * @var array
     */
    protected $testsOntologies = array('http://www.tao.lu/Ontologies/TAOTest.rdf');

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return core_view_classes_
     */
    public function __construct()
    {
        $returnValue = null;

        // section 10-13-1-45-2836570e:123bd13e69b:-8000:0000000000001888 begin
		
		parent::__construct();
		$this->testClass = new core_kernel_classes_Class(TEST_CLASS);
		$this->loadOntologies($this->testsOntologies);
		
        // section 10-13-1-45-2836570e:123bd13e69b:-8000:0000000000001888 end

        return $returnValue;
    }

    /**
     * Short description of method getTests
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  array options
     * @return core_kernel_classes_ContainerCollection
     */
    public function getTests($options = array())
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:000000000000181B begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:000000000000181B end

        return $returnValue;
    }

    /**
     * Short description of method getTest
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string identifier usually the test label or the ressource URI
     * @return core_kernel_classes_Resource
     */
    public function getTest($identifier)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D8 begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D8 end

        return $returnValue;
    }

    /**
     * Short description of method bindTestContent
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  ContainerCollection items
     * @param  Resource test
     * @param  Resource testContent
     * @return core_kernel_classes_Resource
     */
    public function bindTestContent( core_kernel_classes_ContainerCollection $items,  core_kernel_classes_Resource $test,  core_kernel_classes_Resource $testContent)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DE begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DE end

        return $returnValue;
    }

    /**
     * Short description of method activateTest
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Resource test
     * @return boolean
     */
    public function activateTest( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E3 begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteTest
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Resource test
     * @return boolean
     */
    public function deleteTest( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E6 begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getTestClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getTestClass($uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001AF3 begin
		
		$returnValue = $this->testClass;
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001AF3 end

        return $returnValue;
    }

} /* end of class taoTests_models_classes_TestsService */

?>