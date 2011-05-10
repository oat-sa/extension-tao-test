<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017DB-constants end

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoTests
 * @subpackage models_classes
 */
class taoTests_models_classes_TestsService
    extends tao_models_classes_GenerisService
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
     * Short description of method getTest
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string identifier usually the test label or the ressource URI
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getTest($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D8 begin
		
		if(is_null($clazz) && $mode == 'uri'){
			try{
				$resource = new core_kernel_classes_Resource($identifier);
				foreach($resource->getType() as $type){
					$clazz = $type;
					break;
				}
			}
			catch(Exception $e){}
		}
		if(is_null($clazz)){
			$clazz = $this->testClass;
		}
		if($this->isTestClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017D8 end

        return $returnValue;
    }

    /**
     * delete a test instance
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
			$processAuthoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
			$processAuthoringService->deleteProcess($process);
			
			$returnValue = $test->delete();
		}
		
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017E6 end

        return (bool) $returnValue;
    }

    /**
     * get a test subclass by uri. 
     * If the uri is not set, it returns the test class (the top level class.
     * If the uri don't reference a test  subclass, it returns null
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getTestClass($uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001AF3 begin
		
		if(empty($uri) && !is_null($this->testClass)){
			$returnValue = $this->testClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isTestClass($clazz)){
				$returnValue = $clazz;
			}
		}
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001AF3 end

        return $returnValue;
    }

    /**
     * Short description of method createTestClass
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
		
			$testClass = $this->createSubClass($clazz, $label);
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $subjectClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' test property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
				
				//@todo implement check if there is a widget key and/or a range key
			}
			$returnValue = $testClass;
		}
		
        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C2B end

        return $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of Test
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Class clazz
     * @return boolean
     */
    public function isTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C3C begin
		
		if($clazz->uriResource == $this->testClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->testClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--728644f3:12512379b22:-8000:0000000000001C40 begin
		
		if(!is_null($clazz)){
			if($this->isTestClass($clazz) && $clazz->uriResource != $this->testClass->uriResource){
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
			 	$authoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
			 	$process = $test->getUniquePropertyValue(
					new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)
				);
				if(!is_null($process)){
					$activities = $authoringService->getActivitiesByProcess($process);
				
					foreach($activities as $activity){
						$item = $authoringService->getItemByActivity($activity);
						if(!is_null($item) && $item instanceof core_kernel_classes_Resource){
							$returnValue[$item->uriResource] = $item;
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return array
     */
    public function getAllItems()
    {
        $returnValue = array();

        // section 127-0-1-1-a1589c9:1262c43ae7a:-8000:0000000000001DFE begin
		
		$itemClazz = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		foreach($itemClazz->getInstances(true) as $instance){
			$returnValue[$instance->uriResource] = $instance->getLabel();
		}
		
        // section 127-0-1-1-a1589c9:1262c43ae7a:-8000:0000000000001DFE end

        return (array) $returnValue;
    }

    /**
     * Short description of method updateProcessLabel
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BF1 begin
		
		//call the parent create instance to prevent useless process test to be created:
		$clone = parent::createInstance($clazz, $instance->getLabel()." bis");
		
		if(!is_null($clone)){
			$noCloningProperties = array(
				TEST_TESTCONTENT_PROP,
				RDF_TYPE
			);
		
			foreach($clazz->getProperties(true) as $property){
			
				if(!in_array($property->uriResource, $noCloningProperties)){
					//allow clone of every property value but the deliverycontent, which is a process:
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
			}
			
			//clone the process:
			$propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
			try{
				$process = $instance->getUniquePropertyValue($propInstanceContent);
			}catch(Exception $e){}
			if(!is_null($process)){
				$processCloner = new wfEngine_models_classes_ProcessCloner();
				$processClone = $processCloner->cloneProcess($process);
				$clone->editPropertyValues($propInstanceContent, $processClone->uriResource);
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
		
		$test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $processInstance->uriResource);
		$this->updateProcessLabel($test);
		
		//set the the default authoring mode to the 'simple mode':
		$test->setPropertyValue(new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP), TAO_TEST_SIMPLEMODE);
		
		$returnValue = $test;		
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002BFA end

        return $returnValue;
    }

    /**
     * Short description of method linearizeTestProcess
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource test
     * @return boolean
     */
    public function linearizeTestProcess( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C01 begin
		
		//get list of all items in the test, without order:
		$items = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
		
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
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource test
     * @return array
     */
    public function getTestItems( core_kernel_classes_Resource $test)
    {
        $returnValue = array();

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C05 begin
		$items = array();
		$authoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
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
				if($isIntial->uriResource == GENERIS_TRUE){
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
			$connectorCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES, $currentActivity->uriResource);
			$nextActivity = null;
			foreach($connectorCollection->getIterator() as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
					break;
				}
			}
			if(!is_null($nextActivity)){
				$currentActivity = $nextActivity;
			}else{
				if($i == $totalNumber-1){
					//it is normal, since it is the last activity and item
				}else{
					throw new Exception('the next activity of the connector is not found');
				}	
			}
		}
		
		if(count($items) > 0){
			
			ksort($items);
			
			$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
			$itemSubClasses = array();
			foreach($itemClass->getSubClasses(true) as $itemSubClass){
				$itemSubClasses[] = $itemSubClass->uriResource;
			}
			
			foreach($items as $item){
				$clazz = $this->getClass($item);
				if(in_array($clazz->uriResource, $itemSubClasses)){
					$returnValue[] = $clazz;
				}
				$returnValue[] = $item;
			}
		}
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C05 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setTestItems
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource test
     * @param  array items
     * @return boolean
     */
    public function setTestItems( core_kernel_classes_Resource $test, $items)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C08 begin
		$authoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
		
		// get the current process:
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		
		$var_delivery = new core_kernel_classes_Resource(INSTANCE_PROCESSVARIABLE_DELIVERY);
		
		if(!wfEngine_helpers_ProcessUtil::checkType($var_delivery, new core_kernel_classes_Class(CLASS_PROCESSVARIABLES))){
			throw new Exception('the required process variable "delivery" is missing, reinstalling tao is required');
		}
		
		//get formal param associated to the 3 required service input parameters:
		$itemUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI);
		$testUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_TESTURI);
		$deliveryUriParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_DELIVERYURI);
		
		//delete all related activities:
		$activities = $authoringService->getActivitiesByProcess($process);
		foreach($activities as $activity){
			if(!$authoringService->deleteActivity($activity)){
				return $returnValue;
			}
		}
		
		//create the list of activities and interactive services and items plus their appropriate property values:
		$totalNumber = count($items);//0...n
		$previousConnector = null; 
		for($i=0;$i<$totalNumber;$i++){
			$item = $items[$i];
			if(!($item instanceof core_kernel_classes_Resource)){
				throw new Exception("the array element n$i is not a Resource");
			}
			
			//create an activity
			$activity = null;
			$activity = $authoringService->createActivity($process, "item: {$item->getLabel()}");
			if($i==0){
				//set the property value as initial
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}
			
			//set property value visible to true
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);
			
			//set ACL mode to role user restricted with role=subject
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE),  INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY);
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
			
			
			//get the item runner service definition: must exists!
			$itemRunnerServiceDefinition = new core_kernel_classes_Resource(INSTANCE_SERVICEDEFINITION_ITEMRUNNER);
			if(!wfEngine_helpers_ProcessUtil::checkType($itemRunnerServiceDefinition, new core_kernel_classes_Class(CLASS_SUPPORTSERVICES))){
				throw new Exception('required  service definition item runner does not exists, reinstall tao is required');
			}
			
			//create a call of service and associate the service definition to it:
			$interactiveService = $authoringService->createInteractiveService($activity);
			$interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $itemRunnerServiceDefinition->uriResource);
			
			$authoringService->setActualParameter($interactiveService, $itemUriParam, $item->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);//constant: we know it!
			$authoringService->setActualParameter($interactiveService, $testUriParam, $test->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);//constant: we know it!
			$authoringService->setActualParameter($interactiveService, $deliveryUriParam, $var_delivery->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);//don't know yet so process var!
			
			if($totalNumber == 1){
				if(!is_null($interactiveService) && $interactiveService instanceof core_kernel_classes_Resource){
					return true;
				}
			}
			if($i<$totalNumber-1){
				//get the connector created as the same time as the activity and set the type to "sequential" and the next activity as the selected service definition:
				$connector = $authoringService->createConnector($activity);
				if(!($connector instanceof core_kernel_classes_Resource) || is_null($connector)){
					throw new Exception("the created connector is not a resource");
					return $returnValue;
				}
			
				$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
				
				if(!is_null($previousConnector)){
					$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				}
				$previousConnector = $connector;//set the current connector as "the previous one" for the next loop	
			}
			else{
				//if it is the last test of the array, no need to add a connector: just connect the previous connector to the last activity
				$previousConnector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $activity->uriResource);
				//every action is performed:
				$returnValue = true;
			}
		}
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C08 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setAuthoringMode
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource test
     * @param  string mode
     * @return boolean
     */
    public function setAuthoringMode( core_kernel_classes_Resource $test, $mode = '')
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C0C begin
		$property = new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP);
		switch(strtolower($mode)){
			case 'simple':{
				$test->editPropertyValues($property, TAO_TEST_SIMPLEMODE);
				//linearization required:
				$returnValue = $this->linearizeTestProcess($test);
				break;
			}
			case 'advanced':{
				$returnValue = $test->editPropertyValues($property, TAO_TEST_ADVANCEDMODE);
				break;
			}
			default:{
				$returnValue = false;
			}
		}
        // section 10-13-1-39-7cf56b28:12c53e4afe8:-8000:0000000000002C0C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isTestActive
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource test
     * @return boolean
     */
    public function isTestActive( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;

        // section 10-11-2-16--f6d941a:12d7a53887b:-8000:0000000000002F4A begin
		$active = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_ACTIVE_PROP));
		if(is_null($active)){
			if ($active->uriResource == GENERIS_TRUE){
				$returnValue = true;
			}
		}
        // section 10-11-2-16--f6d941a:12d7a53887b:-8000:0000000000002F4A end

        return (bool) $returnValue;
    }

} /* end of class taoTests_models_classes_TestsService */

?>