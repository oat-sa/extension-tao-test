<?php

error_reporting(E_ALL);

/**
 * TAO - taoTests\models\classes\class.TestAuthoringService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.01.2011, 15:50:15 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoTests
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfEngine_models_classes_ProcessAuthoringService
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('wfEngine/models/classes/class.ProcessAuthoringService.php');

/* user defined includes */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-includes begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-includes end

/* user defined constants */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-constants begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-constants end

/**
 * Short description of class taoTests_models_classes_TestAuthoringService
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoTests
 * @subpackage models_classes
 */
class taoTests_models_classes_TestAuthoringService
    extends wfEngine_models_classes_ProcessAuthoringService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the url of the TAO item runner service
     *
     * @access protected
     * @var string
     */
    protected $itemRunnerUrl = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getItemByActivity
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getItemByActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E43 begin
		if(!empty($activity)){
			
			//check all interactive services:
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
					
					$serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
					
					//if service definition has the url of the service item runner
					$itemRunnerServiceDefinition = wfEngine_helpers_ProcessUtil::getServiceDefinition($this->itemRunnerUrl);
					
					if(!is_null($itemRunnerServiceDefinition)){
						if($serviceDefinition->uriResource == $itemRunnerServiceDefinition->uriResource){
					
							foreach($iService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN))->getIterator() as $actualParam){
								
								$formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
								if($formalParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)) == 'itemUri'){
									$item = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
									if(!is_null($item)){
										$returnValue = $item;
										break(2);
									}
								}
							}
							
						}
					}
						
				}
				
			}
			
		}
		
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E43 end

        return $returnValue;
    }

    /**
     * Short description of method getItemRunnerUrl
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return string
     */
    public function getItemRunnerUrl()
    {
        $returnValue = (string) '';

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E46 begin
		$returnValue = $this->itemRunnerUrl;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E46 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getTestFromProcess
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource process
     * @return core_kernel_classes_Resource
     */
    public function getTestFromProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E48 begin
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$tests = $testClass->searchInstances(array(TEST_TESTCONTENT_PROP => $process->uriResource), array('like'=>false));
		if(!empty($tests)){
			$returnValue = $tests[0];
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E48 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function __construct()
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FA9 begin
		parent::__construct();
		$this->itemRunnerUrl = '/taoDelivery/ItemDelivery/runner?itemUri=^itemUri&testUri=^testUri&deliveryUri=^deliveryUri&';
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FA9 end
    }

} /* end of class taoTests_models_classes_TestAuthoringService */

?>