<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The taoTests_models_classes_TestAuthoringService class provides methods to connect to several ontologies and interact with them.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoTests_models_classes_TestAuthoringService
    extends wfEngine_models_classes_ProcessAuthoringService
{
   
	protected $itemRunnerUrl = '';
	
	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		parent::__construct();
		$this->itemRunnerUrl = '/taoDelivery/ItemDelivery/runner?itemUri=^itemUri&testUri=^testUri&deliveryUri=^deliveryUri&';
    }
		
	public function getItemRunnerUrl(){
		return $this->itemRunnerUrl;
	}
	/**
     * Used in delivery compilation: get the test included in an activity
	 * if found, it returns the delivery resource and null otherwise
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource activity
     * @return core_kernel_classes_Resource or null
     */	
	public function getItemByActivity(core_kernel_classes_Resource $activity){
		$returnValue = null;
		
		if(!empty($activity)){
			
			//check all interactive services:
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
					
					$serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
					
					//if service definition has the url of the service item runner
					$itemRunnerServiceDefinition = wfEngine_helpers_ProcessUtil::getServiceDefinition($this->itemRunnerUrl);
					
					if(!is_null($itemRunnerServiceDefinition)){
						if($serviceDefinition->uriResource == $itemRunnerServiceDefinition->uriResource){
					
							foreach($iService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMIN))->getIterator() as $actualParam){
								
								$formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAMETER));
								if($formalParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)) == 'itemUri'){
									$item = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_CONSTANTVALUE));
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
		
		return $returnValue;
	}
	
	/**
     * Get the delivery associated to a process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource process
     * @return core_kernel_classes_Resource or null
     */	
	public function getTestFromProcess(core_kernel_classes_Resource $process){
		
		$test = null;
		
		$testCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(TEST_TESTCONTENT_PROP,  $process->uriResource);
		if(!$testCollection->isEmpty()){
			$test = $testCollection->get(0);
		}
		
		return $test;
	}
	
} /* end of class taoTests_models_classes_TestAuthoringService */

?>