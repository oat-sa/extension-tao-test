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
					$itemRunnerServiceDefinition = $this->getItemRunnerServiceDefinition();
					
					if(is_null($itemRunnerServiceDefinition)){
						throw new Exception('the item runner service definition does not exist');
					}
					
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
	
	public function getItemRunnerServiceDefinition(){
		
		$serviceDefinition = null;
		
		//get the item runner service definition, if does not exist, create one:
		$itemRunnerServiceUrl = '/taoDelivery/ItemDelivery/runner?itemUri=^itemUri&testUri=^testUri&deliveryUri=^deliveryUri&';
		
		$serviceDefinitionCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_SUPPORTSERVICES_URL, $itemRunnerServiceUrl);
		if(!$serviceDefinitionCollection->isEmpty()){
			if($serviceDefinitionCollection->get(0) instanceof core_kernel_classes_Resource){
				$serviceDefinition = $serviceDefinitionCollection->get(0);
			}
		}
		if(is_null($serviceDefinition)){
			//if no corresponding service def found, create a service definition:
			$serviceDefinitionClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
			$serviceDefinition = $serviceDefinitionClass->createInstance($item->getLabel(), 'created by test service');
			
			//set service definition (the test) and parameters:
			$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL), $itemRunnerServiceUrl);
			$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $itemUriParam->uriResource);
			$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $testUriParam->uriResource);
			$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $deliveryUriParam->uriResource);
		}
		
		return $serviceDefinition;
	}
	

} /* end of class taoTests_models_classes_TestAuthoringService */

?>