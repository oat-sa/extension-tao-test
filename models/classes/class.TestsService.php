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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTests
 * @subpackage models_classes
 */
class taoTests_models_classes_TestsService
    extends tao_models_classes_ClassService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level test class
     *
     * @access protected
     * @var Class
     */
    protected $testClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_view_classes_
     */
    public function __construct()
    {
        $returnValue = null;

        // section 10-13-1-45-2836570e:123bd13e69b:-8000:0000000000001888 begin

		parent::__construct();

		$this->testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);

        // section 10-13-1-45-2836570e:123bd13e69b:-8000:0000000000001888 end

        return $returnValue;
    }

    /**
     * delete a test instance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return boolean
     */
    public function deleteTest( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E6 begin

		if(!is_null($test)){
			//delete the associated process:
			$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			$processAuthoringService = taoTests_models_classes_TestAuthoringService::singleton();
			$processAuthoringService->deleteProcess($process);

			$returnValue = $test->delete();
		}


        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E6 end

        return (bool) $returnValue;
    }

    /**
     * get the test class
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     */
    public function getRootclass()
    {
		return $this->testClass;
    }

    /**
     * Short description of method createTestClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createTestClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C2B begin

		if(is_null($clazz)){
			$clazz = $this->testClass;
		}

		if($this->isTestClass($clazz)){
            return $this->createSubClass($clazz, $label);
		}
        else{
            throw new common_exception_InconsistentData($clazz . ' should be a Class Test ');
        }
        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C2B end

        return $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of Test
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C3C begin

		if($clazz->getUri() == $this->testClass->getUri()){
			$returnValue = true;
		}
		else{
			foreach($this->testClass->getSubClasses(true) as $subclass){
				if($clazz->getUri() == $subclass->getUri()){
					$returnValue = true;
					break;
				}
			}
		}

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C3C end

        return (bool) $returnValue;
    }

    /**
     * delete a test class or sublcass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C40 begin

		if(!is_null($clazz)){
			if($this->isTestClass($clazz) && $clazz->getUri() != $this->testClass->getUri()){
				$returnValue = $clazz->delete();
			}
		}

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C40 end

        return (bool) $returnValue;
    }

    /**
     * get the list of items in the test in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @param  boolean sequenced
     * @return array
     */
    public function getRelatedItems( core_kernel_classes_Resource $test, $sequenced = false)
    {
        $returnValue = array();

        // section 127-0-1-1--5f8e44a2:1258d8ab867:-8000:0000000000001D27 begin
		if(!is_null($test)){

			try{
			 	$authoringService = taoTests_models_classes_TestAuthoringService::singleton();
			 	$process = $test->getUniquePropertyValue(
					new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)
				);
				if(!is_null($process)){
					$activities = $authoringService->getActivitiesByProcess($process);

					foreach($activities as $activity){
						$item = $authoringService->getItemByActivity($activity);
						if(!is_null($item) && $item instanceof core_kernel_classes_Resource){
							$returnValue[$item->getUri()] = $item;
						}
					}
				}

			}
			catch(Exception $e){}

		}
        // section 127-0-1-1--5f8e44a2:1258d8ab867:-8000:0000000000001D27 end

        return (array) $returnValue;
    }

    /**
     * Get all available items
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAllItems()
    {
        $returnValue = array();

        // section 127-0-1-1-a1589c9:1262c43ae7a:-8000:0000000000001DFE begin

		$itemClazz = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		foreach($itemClazz->getInstances(true) as $instance){
			$returnValue[$instance->getUri()] = $instance->getLabel();
		}

        // section 127-0-1-1-a1589c9:1262c43ae7a:-8000:0000000000001DFE end

        return (array) $returnValue;
    }

    /**
     * Short description of method updateProcessLabel
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return boolean
     */
    public function updateProcessLabel( core_kernel_classes_Resource $test = null)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BEA begin
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		$returnValue = $process->setLabel("Process ".$test->getLabel());
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BEA end

        return (bool) $returnValue;
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BF1 begin

		//call the parent create instance to prevent useless process test to be created:
		$label = $instance->getLabel();
		$cloneLabel = "$label bis";
		$clone = parent::createInstance($clazz, $cloneLabel);

		if(!is_null($clone)){
			$noCloningProperties = array(
				TEST_TESTCONTENT_PROP,
				RDF_TYPE
			);

			foreach($clazz->getProperties(true) as $property){

				if(!in_array($property->getUri(), $noCloningProperties)){
					//allow clone of every property value but the deliverycontent, which is a process:
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
			}
			//Fix label
			if(preg_match("/bis/", $label)) {
				$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
				$cloneNumber++;
				$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)."bis $cloneNumber" ;
			}
			$clone->setLabel($cloneLabel);

			//clone the process:
			$propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
			try{
				$process = $instance->getUniquePropertyValue($propInstanceContent);
			}catch(Exception $e){}
			if(!is_null($process)){
				$processCloner = new wfAuthoring_models_classes_ProcessCloner();
				$processClone = $processCloner->cloneProcess($process);
				$clone->editPropertyValues($propInstanceContent, $processClone->getUri());
			}else{
				throw new Exception("the test process cannot be found");
			}

			$this->updateProcessLabel($clone);
			$returnValue = $clone;
		}

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BF1 end

        return $returnValue;
    }

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Class $clazz, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BFA begin
		$test = parent::createInstance($clazz, $label);

		//create a process instance at the same time:
		$processInstance = parent::createInstance(new core_kernel_classes_Class(CLASS_PROCESS),'process generated with testsService');

		//set ACL right to delivery process initialization:
		$extensionsManager = common_ext_ExtensionsManager::singleton();
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);

		$test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $processInstance->getUri());
		$this->updateProcessLabel($test);

		//set the the default state to 'activ':
		$test->setPropertyValue(new core_kernel_classes_Property(TEST_ACTIVE_PROP), GENERIS_TRUE);

		$returnValue = $test;
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BFA end

        return $returnValue;
    }

    /**
     * Short description of method linearizeTestProcess
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return boolean
     */
    public function linearizeTestProcess( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C01 begin

		//get list of all items in the test, without order:
		$items = array();
		$authoringService = taoTests_models_classes_TestAuthoringService::singleton();

		//get the associated process:
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));

		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);

		foreach($activities as $activity){
			$item = $authoringService->getItemByActivity($activity);
			if(!is_null($item)){
				$items[] = $item;
			}
		}

		$returnValue = $this->setTestItems($test, $items);
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C01 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getTestItems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return array
     */
    public function getTestItems( core_kernel_classes_Resource $test)
    {
        $returnValue = array();

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C05 begin
		$items = array();
		$authoringService = taoTests_models_classes_TestAuthoringService::singleton();
		// $authoringService = new taoTests_models_classes_TestAuthoringService();

		//get the associated process, set in the test content property
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));

		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		$totalNumber = count($activities);

		//find the first one: property isinitial == true (must be only one, if not error) and set as the currentActivity:
		$currentActivity = null;
		foreach($activities as $activity){

			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->getUri() == GENERIS_TRUE){
					$currentActivity = $activity;
					break;
				}
			}
		}

		if(is_null($currentActivity)){
			return $items;
		}

		//start the loop:
		for($i=0;$i<$totalNumber;$i++){
			$item = $authoringService->getItemByActivity($currentActivity);
			if(!is_null($item)){
				$items[$i] = $item;
			}

			//get its connector (check the type is "sequential) if ok, get the next activity
			$connector = $currentActivity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
			$nextActivity = null;
			if (!is_null($connector)) {
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
				}
			}
			
			if(!is_null($nextActivity)){
				$currentActivity = $nextActivity;
			}else{
				if($i == $totalNumber-1){
					//it is normal, since it is the last activity and item
				}else{
					throw new common_Exception('the next activity of the connector is not found');
				}
			}
		}

		$returnValue = $items;
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C05 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setTestItems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @param  array items
     * @return boolean
     */
    public function setTestItems( core_kernel_classes_Resource $test, $items)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C08 begin
		$authoringService = taoTests_models_classes_TestAuthoringService::singleton();

		// get the current process:
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));

		$var_delivery = new core_kernel_classes_Resource(INSTANCE_PROCESSVARIABLE_DELIVERY);

		if(!$var_delivery->hasType(new core_kernel_classes_Class(CLASS_PROCESSVARIABLES))){
			throw new Exception('The required process variable "delivery" is missing. Reinstall TAO is required.');
		}

		//get formal param associated to the 3 required service input parameters:
		$itemUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI);
		$testUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_TESTURI);
		$deliveryUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_DELIVERYURI);

		//delete all related activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		foreach($activities as $activity){
			if(!$authoringService->deleteActivity($activity)){
				throw new common_exception_Error('Unable to delete Activity '.$activity->getUri());
			}
		}

		//create the list of activities and interactive services and items plus their appropriate property values:
		$previousActivity = null;
		$connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		
		foreach ($items as $item) {
			if(!($item instanceof core_kernel_classes_Resource)){
				throw new common_Exception("An item provided to ".__FUNCTION__." is not a resource but ".gettype($item));
			}

			//create an activity
			$activity = null;
			$activity = $authoringService->createActivity($process, "item: {$item->getLabel()}");

			//set property value visible to true
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);

			//set ACL mode to role user restricted with role=subject
			$extManager = common_ext_ExtensionsManager::singleton();
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE),  INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY);
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);


			//get the item runner service definition: must exists!
			$itemRunnerServiceDefinition = new core_kernel_classes_Resource(INSTANCE_SERVICEDEFINITION_ITEMRUNNER);
			if(!$itemRunnerServiceDefinition->hasType(new core_kernel_classes_Class(CLASS_SUPPORTSERVICES))){
				throw new Exception('required  service definition item runner does not exists, reinstall tao is required');
			}

			//create a call of service and associate the service definition to it:
			$interactiveService = $authoringService->createInteractiveService($activity);
			$interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $itemRunnerServiceDefinition->getUri());

			$authoringService->setActualParameter($interactiveService, $itemUriParam, $item->getUri(), PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);//constant: we know it!
			$authoringService->setActualParameter($interactiveService, $testUriParam, $test->getUri(), PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);//constant: we know it!
			$authoringService->setActualParameter($interactiveService, $deliveryUriParam, $var_delivery->getUri(), PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);//don't know yet so process var!

			if(!is_null($previousActivity)) {
				$connectorService->createSequential($previousActivity, $activity);
			} else {
				//set the property value as initial
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}
			$previousActivity = $activity;
		}
		$returnValue = true;
		// section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C08 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isTestActive
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return boolean
     */
    public function isTestActive( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-11-2-16--f6d941a:12d7a53887b:-8000:0000000000002F4A begin
		$active = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_ACTIVE_PROP));
		if(is_null($active)){
			if ($active->getUri() == GENERIS_TRUE){
				$returnValue = true;
			}
		}
        // section 10-11-2-16--f6d941a:12d7a53887b:-8000:0000000000002F4A end

        return (bool) $returnValue;
    }
    
    public function getTestModel(core_kernel_classes_Resource $test) {
		return $test->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TEST_TESTMODEL));
    }

    /**
     * Returns the implementation of an items test model
     * 
     * @param core_kernel_classes_Resource $test
     * @return taoTests_models_classes_TestModel
     */
    public function getTestModelImplementation(core_kernel_classes_Resource $testModel) {
    	$returnValue = null;
		if (!empty($testModel)) {
			$classname = (string)$testModel->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TESTMODEL_IMPLEMENTATION));
			if (!empty($classname)) {
				if (class_exists($classname) && in_array('taoTests_models_classes_TestModel', class_implements($classname))) {
					$returnValue = new $classname();
				} else {
					throw new common_exception_Error('Test model service '.$classname.' not found, or not compatible for test model '.$testModel->getLabel());
				}
			}
		}
		return $returnValue;
    }
    
} /* end of class taoTests_models_classes_TestsService */

?>