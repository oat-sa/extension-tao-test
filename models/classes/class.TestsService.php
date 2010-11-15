<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
				$type = $resource->getUniquePropertyValue(new core_kernel_classes_Property( RDF_TYPE ));
				$clazz = new core_kernel_classes_Class($type->uriResource);
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
     * create a delivery instance, and at the same time the process instance associated to it
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Class clazz
	 * @param  string label
     * @return core_kernel_classes_Resource
     */
	public function createInstance(core_kernel_classes_Class $clazz, $label = ''){
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
		
		return $test;		
	}
	
	/**
     * Make sure that the test and the associated process have the same label
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return void
     */
	public function updateProcessLabel(core_kernel_classes_Resource $test){
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		$process->setLabel("Process ".$test->getLabel());
	}
	
	public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;
        
   		$returnValue = parent::cloneInstance($instance, $clazz);
		//clone process or not?
		

        return $returnValue;
    }
	
    /**
     * delete a test instance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * define the list of items composing a test
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @param  array items
     * @param  boolean sequenced
     * @return boolean
     */
    public function setRelatedItems( core_kernel_classes_Resource $test, $items = array(), $sequenced = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5f8e44a2:1258d8ab867:-8000:0000000000001D2A begin
		
		if(!is_null($test)){
			
			$relatedItemProp = new core_kernel_classes_Property(TEST_RELATED_ITEMS_PROP);
			
			$test->removePropertyValues($relatedItemProp);
			$done = 0;
			foreach($items as $item){
				if($test->setPropertyValue($relatedItemProp, $item)){
					$done++;
				}
			}
			if($done == count($items)){
				$returnValue = true;
			}
			
			if($sequenced){
				try{
					$content = $this->getTestContent($test);
					
					$dom = new DOMDocument();
					$dom->loadXML($content);
					$root = $dom->documentElement;
					
					$oldItemSequence = array();
					
					$xpath = new DomXpath($dom);
					$result = $xpath->query("//tao:CITEM");
					for($i = 0; $i < $result->length; $i++){
						$node = $result->item($i);
						$oldItemSequence[] = $node->parentNode->removeChild($node);
					}
					
					foreach($items as $index => $itemUri){
						$item = new core_kernel_classes_Resource($itemUri);
						try{
							
							$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
							if($itemModel instanceof core_kernel_classes_Resource){
								$runtime = basename($itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY)));
							}
						}
						catch(Exception $exp){
							$runtime = '';
						}
						$weight = "1";
						
						foreach($oldItemSequence as $oldItem){
							if($oldItem->nodeValue == $itemUri){
								if($oldItem->hasAttribute('weight')){
									$weight = $oldItem->getAttribute('weight');
								}
								if($oldItem->hasAttribute('itemModel') && $runtime == ''){
									$runtime = $oldItem->getAttribute('itemModel');
								}
								break;
							}
						}
						
						$itemNode = $dom->createElement('tao:CITEM', $item->uriResource);
						$itemNode->setAttribute('weight', '1');
						$itemNode->setAttribute('Sequence', $index);
						$itemNode->setAttribute('itemModel', $runtime);
						$root->appendChild($itemNode);
					}
					
					$returnValue = $this->setTestContent($test, $dom->saveXML());
					
				}
				catch(DOMException $domExp){
					$returnValue = false;
				}
			}
		}
		
        // section 127-0-1-1--5f8e44a2:1258d8ab867:-8000:0000000000001D2A end

        return (bool) $returnValue;
    }

    /**
     * Get all available items
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * get the content of a test
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @return string
     */
    public function getTestContent( core_kernel_classes_Resource $test)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--645eb059:1260e94e6e6:-8000:0000000000001DEA begin
		
		if(!is_null($test)){
			$testContents = $test->getPropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			
			if(count($testContents) == 0){	//lazy init
				$testContent = $this->initTestContent($test);
				if($this->setTestContent($test, $testContent)){
					$returnValue = (string)$testContent;
				}
			}
			else if(count($testContents) == 1){	//get it
				
				if(strlen(trim($testContents[0])) == 0){
					$testContent = $this->initTestContent($test);	//lazy init in case of empty prop
					if($this->setTestContent($test, $testContent)){
						$returnValue = (string)$testContent;
					}
				}
				else{
					$returnValue = (string)$testContents[0];
				}
			}
			else if(count($testContents) > 1){	
				$testContent =  $testContents[0];
				//if there is more than one test content, it should not happend but to prevent errors:
				$test->removePropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));	//remove them 
				if($this->setTestContent($test, $testContent)){
					$returnValue = (string)$testContent;
				}
			}
		}
		
        // section 127-0-1-1--645eb059:1260e94e6e6:-8000:0000000000001DEA end

        return (string) $returnValue;
    }

    /**
     * set the content of a test
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test a valid xml string
     * @param  string content
     * @return boolean
     */
    public function setTestContent( core_kernel_classes_Resource $test, $content = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--645eb059:1260e94e6e6:-8000:0000000000001DED begin
		
		if(!is_null($test)){
			
			//initialize the content
			try{
				$dom = new DOMDocument();
				$dom->loadXML($content);
				$content = $this->initTestContent($test, $dom);
			}
			catch(DOMException $domEx){}
			
			$test = $this->bindProperties($test, array(TEST_TESTCONTENT_PROP => $content));
			
			try{
				if($this->getTestContent($test) == $content){
					$returnValue = true;
				}
			}
			catch(Exception $e){}
		}
		
        // section 127-0-1-1--645eb059:1260e94e6e6:-8000:0000000000001DED end

        return (bool) $returnValue;
    }

    /**
     * initialize the default content of a test (copy the content of
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @param  DomDocument dom
     * @return string
     */
    public function initTestContent( core_kernel_classes_Resource $test,  DomDocument $dom = null)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--18790a60:12622d03866:-8000:0000000000001DFB begin
		
        if(is_null($dom)){
			$dom = new DOMDocument();
			$dom->load(TEST_CONTENT_REF_FILE);
        }
		$root = $dom->documentElement;
		if(!is_null($root)){
			$currentLang = strtoupper(core_kernel_classes_Session::singleton()->getLg());
			
			$root->setAttribute('rdf:ID', $test->uriResource);
			foreach($root->getElementsByTagNameNS('http://www.w3.org/TR/1999/PR-rdf-schema-19990303#','LABEL') as $labelNode){
				$labelNode->nodeValue = $test->getLabel();
				$labelNode->setAttribute('lang', $currentLang);
			}
			foreach($root->getElementsByTagNameNS('http://www.w3.org/TR/1999/PR-rdf-schema-19990303#', 'COMMENT') as $commentNode){
				$commentNode->nodeValue = $test->getComment();
				$commentNode->setAttribute('lang', $currentLang);
			}
			$returnValue = $dom->saveXML();
		}
        // section 127-0-1-1--18790a60:12622d03866:-8000:0000000000001DFB end

        return (string) $returnValue;
    }

    /**
     * get the ordered sequence of items linked to the test in parameter
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @param  array options
     * @return array
     */
    public function getItemSequence( core_kernel_classes_Resource $test, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1-16627393:126328f62c2:-8000:0000000000001E00 begin
		
		try{
			$content = $this->getTestContent($test);
			
			$dom = new DOMDocument();
			$dom->loadXML($content);
			$root = $dom->documentElement;
			
			$xpath = new DomXpath($dom);
			$result = $xpath->query("//tao:CITEM");
			
			$i = 0;
			$length = $result->length;
			if(isset($options['start'])){
				if($options['start'] > 0 && $options['start'] < $length){
					$i = $options['start'];
				}
			}
			if(isset($options['end'])){
				if($options['end'] > $i && $options['end'] <= $length){
					$length = $options['end'];
				}
			}
			while($i < $length){
				$node = $result->item($i);
				if($node->hasAttribute('Sequence')){
					$index = (int)$node->getAttribute('Sequence');
				}
				else{
					$index = count($returnValue);
				}
				$itemResource = new core_kernel_classes_Resource($node->nodeValue);
				$item = array(
					'sequence'			=>  $index,
					'label' 			=>  $itemResource->getLabel(),
					'uri' 				=>  $itemResource->uriResource,
					'weight'			=> ($node->hasAttribute('weight')) 			? $node->getAttribute('weight') 	: '',
					'difficulty'		=> ($node->hasAttribute('DIFFICULTY')) 		? $node->getAttribute('DIFFICULTY') : '',
					'discrimination'	=> ($node->hasAttribute('DISCRIMINATION')) 	? $node->getAttribute('DISCRIMINATION') : '',
					'guessing'			=> ($node->hasAttribute('GUESSING')) 		? $node->getAttribute('GUESSING') 	: '',
					'model'				=> ($node->hasAttribute('model')) 			? $node->getAttribute('model') 		: '',
					'itemModel'			=> ($node->hasAttribute('itemModel')) 		? $node->getAttribute('itemModel') 	: ''
				);
				
				$returnValue[$index] = $item;
				
				$i++;
			}
			if(isset($options['order'])){
				$desc = false;
				if((isset($options['orderDir']))){
					if(strtolower($options['orderDir']) != 'asc'){
						$desc = true;
					}
				}
				$returnValue = tao_helpers_Array::sortByField($returnValue, $options['order'], $desc);
			}
		}
		catch(DOMException $domExp){ }
		
        // section 127-0-1-1-16627393:126328f62c2:-8000:0000000000001E00 end

        return (array) $returnValue;
    }

    /**
     * save the ordered item sequence of a test
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @param  array sequence
     * @return boolean
     */
    public function saveItemSequence( core_kernel_classes_Resource $test, $sequence)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-30d8730d:1264b634d6b:-8000:0000000000001E1D begin
		
		try{
			$content = $this->getTestContent($test);
			
			$dom = new DOMDocument();
			$dom->loadXML($content);
			$root = $dom->documentElement;
			
			//remove all tao:CITEM
			$xpath = new DomXpath($dom);
			$result = $xpath->query("//tao:CITEM");
			for($i = 0; $i < $result->length; $i++){
				$node = $result->item($i);
				$node->parentNode->removeChild($node);
			}
			
			//create them again
			foreach($sequence as $index => $itemData){
				$item = new core_kernel_classes_Resource($itemData['uri']);
				try{
					$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
					if($itemModel instanceof core_kernel_classes_Resource){
						$runtime = basename($itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY)));
					}
				}
				catch(Exception $exp){
					$runtime = '';
				}
				
				$itemNode = $dom->createElement('tao:CITEM', $item->uriResource);
				$itemNode->setAttribute('Sequence', $index);
				$itemNode->setAttribute('itemModel', $runtime);
				
				if(isset($itemData['weight'])){
					$itemNode->setAttribute('weight', $itemData['weight']);
				}
				if(isset($itemData['difficulty'])){
					$itemNode->setAttribute('DIFFICULTY', $itemData['difficulty']);
				}
				if(isset($itemData['discrimination'])){
					$itemNode->setAttribute('DISCRIMINATION', $itemData['discrimination']);
				}
				if(isset($itemData['guessing'])){
					$itemNode->setAttribute('GUESSING', $itemData['guessing']);
				}
				if(isset($itemData['model'])){
					$itemNode->setAttribute('model', $itemData['model']);
				}
				
				$root->appendChild($itemNode);
			}
			$returnValue = $this->setTestContent($test, $dom->saveXML());
		}
		catch(DOMException $domExp){ }
		
		
        // section 127-0-1-1-30d8730d:1264b634d6b:-8000:0000000000001E1D end

        return (bool) $returnValue;
    }

    /**
     * get the list of test parameters
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @return array
     */
    public function getTestParameters( core_kernel_classes_Resource $test)
    {
        $returnValue = array();

        // section 127-0-1-1-6730a7c:126559edebd:-8000:0000000000001E21 begin
		
		try{
			$content = $this->getTestContent($test);
			
			$dom = new DOMDocument();
			$dom->loadXML($content);
			$root = $dom->documentElement;
			
			
			$xpath = new DomXpath($dom);
			
			//duration
			$result = $xpath->query("//tao:DURATION");
			if($result->item(0)){
				$returnValue['duration'] = $result->item(0)->nodeValue;
			}
			
			//password
			$result = $xpath->query("//tao:PASSWORD");
			if($result->item(0)){
				$returnValue['password'] = $result->item(0)->nodeValue;
			}
			
			//sequence mode and delay
			$result = $xpath->query("//tao:HASSEQUENCEMODE");
			if($result->item(0)){
				$node = $result->item(0);
				$returnValue['hassequencemode'] = $node->nodeValue;
				if(empty($returnValue['hassequencemode'])){
					$returnValue['hassequencemode'] = 'SEQUENCIAL';
				}
				if($node->hasAttribute('DELAY')){
					$returnValue['delay'] = $node->getAttribute('DELAY');
				}
			}
			
			//scoring method
			$result = $xpath->query("//tao:HASSCORINGMETHOD");
			if($result->item(0)){
				$node = $result->item(0);
				$returnValue['hasscoringmethod'] = $node->nodeValue;
				if(empty($returnValue['hasscoringmethod'])){
					$returnValue['hasscoringmethod'] = 'CLASSICAL RATIO';
				}
				if($node->hasAttribute('Qmin')){
					$returnValue['QMIN'] = $node->getAttribute('Qmin');
				}
				if($node->hasAttribute('Qmax')){
					$returnValue['QMAX'] = $node->getAttribute('Qmax');
				}
				if($node->hasAttribute('Qiter')){
					$returnValue['QITER'] = $node->getAttribute('Qiter');
				}
			}
			
			//cumul model
			$result = $xpath->query("//tao:CUMULMODEL");
			if($result->item(0)){
				$returnValue['cumulmodel'] = $result->item(0)->nodeValue;
				if(empty($returnValue['cumulmodel'])){
					$returnValue['cumulmodel'] = 'CLASSICAL';
				}
			}
			
			//halt criteria
			$result = $xpath->query("//tao:HALTCRITERIA");
			if($result->item(0)){
				$node = $result->item(0);
				$returnValue['haltcriteria'] = $node->nodeValue;
				if($node->hasAttribute('MAX')){
					$returnValue['max'] = $node->getAttribute('MAX');
				}
			}
			
			//threshold
			$result = $xpath->query("//cll:threshold");
			foreach($result as $i => $node){
				$returnValue['thresh'.($i+1)] = $node->nodeValue;
			}
			
			//display
			
			$returnValue['display'] = array();
			$result = $xpath->query("//button[@id='prevItem_button']");
			if($result->item(0)){
				$node = $result->item(0);
				if($node->hasAttribute('url')){
					$returnValue['urlleft'] = $node->getAttribute('url');
				}
				if($node->hasAttribute('left')){
					$returnValue['navleft'] = $node->getAttribute('left');
				}
				if($node->hasAttribute('top')){
					$returnValue['navtop'] = $node->getAttribute('top');
				}
			}
			else{
				$returnValue['display'][] = 'deactivateback';
			}
			$result = $xpath->query("//button[@id='nextItem_button']");
			if($result->item(0)){
				$node = $result->item(0);
				if($node->hasAttribute('url')){
					$returnValue['urlright'] = $node->getAttribute('url');
				}
				if($node->hasAttribute('left')){
					$returnValue['navleft'] = $node->getAttribute('left');
				}
				if($node->hasAttribute('top')){
					$returnValue['navtop'] = $node->getAttribute('top');
				}
			}
			$result = $xpath->query("//box[@id='itemContainer_box']");
			if($result->item(0)){
				$node = $result->item(0);
				if($node->hasAttribute('left')){
					$returnValue['itemleft'] = $node->getAttribute('left');
				}
				if($node->hasAttribute('top')){
					$returnValue['itemtop'] = $node->getAttribute('top');
				}
			}
			
			$result = $xpath->query("//progressmeter");
			if($result->item(0)){
				$node = $result->item(0);
				if($node->hasAttribute('left')){
					$returnValue['progressbarleft'] = $node->getAttribute('left');
				}
				if($node->hasAttribute('top')){
					$returnValue['progressbartop'] = $node->getAttribute('top');
				}
			}
			
			
			$result = $xpath->query("//progressmeter[@id='test_progressmeter']");
			if($result->item(0)){
				$returnValue['display'][] = 'showprogessbar';
			}
			
			$result = $xpath->query("//listbox[@id='testItems_listbox']");
			if($result->item(0)){
				$returnValue['display'][] = 'showlistbox';
			}
			
			$result = $xpath->query("//label[@id='testLabel_label']");
			if($result->item(0)){
				$returnValue['display'][] = 'showLabel';
			}
			
			$result = $xpath->query("//label[@id='testComment_label']");
			if($result->item(0)){
				$returnValue['display'][] = 'showComment';
			}
		}
		catch(DOMException $domExp){ }
		
		
		
        // section 127-0-1-1-6730a7c:126559edebd:-8000:0000000000001E21 end

        return (array) $returnValue;
    }

    /**
     * save the test parameters
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @param  array parameters
     * @return boolean
     */
    public function saveTestParameters( core_kernel_classes_Resource $test, $parameters = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6730a7c:126559edebd:-8000:0000000000001E24 begin
		
		try{
			$content = $this->getTestContent($test);
			
			$dom = new DOMDocument();
			$dom->loadXML($content);
			$root = $dom->documentElement;
			
			$xpath = new DomXpath($dom);
			
			//duration
			if(isset($parameters['duration'])){
				$result = $xpath->query("//tao:DURATION");
				if($result->item(0)){
					$result->item(0)->nodeValue = $parameters['duration'];
				}
				else{
					$root->appendChild(
						$dom->createElement('tao:DURATION', $parameters['duration'])
					);
				}
			}
			
			//password
			if(isset($parameters['password'])){
				$result = $xpath->query("//tao:PASSWORD");
				if($result->item(0)){
					$result->item(0)->nodeValue = $parameters['password'];
				}
				else{
					$root->appendChild(
						$dom->createElement('tao:PASSWORD', $parameters['password'])
					);
				}
			}
			
			//sequence mode
			if(isset($parameters['hassequencemode'])){
				$result = $xpath->query("//tao:HASSEQUENCEMODE");
				if($result->item(0)){
					$node = $result->item(0);
					$node->nodeValue = $parameters['hassequencemode'];
				}
				else{
					$root->appendChild(
						$dom->createElement('tao:HASSEQUENCEMODE', $parameters['hassequencemode'])
					);
				}
			}
			
			//sequence mode delay
			if(isset($parameters['delay'])){
				$result = $xpath->query("//tao:HASSEQUENCEMODE");
				if($result->item(0)){
					$node = $result->item(0);
					$node->setAttribute('DELAY', $parameters['delay']);
				}
				else{
					$node = $dom->createElement('tao:HASSEQUENCEMODE', 'SEQUENCIAL');
					$node->setAttribute('DELAY', $parameters['delay']);
					$root->appendChild($node);
				}
			}
			
			//scoring method
			if(isset($parameters['hasscoringmethod'])){
				$result = $xpath->query("//tao:HASSCORINGMETHOD");
				if($result->item(0)){
					$node = $result->item(0);
					$node->nodeValue = $parameters['hasscoringmethod'];
				}
				else{
					$root->appendChild(
						$dom->createElement('tao:HASSCORINGMETHOD', $parameters['hasscoringmethod'])
					);
				}
			}
			
			//scoring method Qmin
			if(isset($parameters['QMIN'])){
				$result = $xpath->query("//tao:HASSCORINGMETHOD");
				if($result->item(0)){
					$node = $result->item(0);
					$node->setAttribute('Qmin', $parameters['QMIN']);
				}
				else{
					$node = $dom->createElement('tao:HASSCORINGMETHOD', 'CLASSICAL RATIO');
					$node->setAttribute('Qmin', $parameters['QMIN']);
					$root->appendChild($node);
				}
			}
			
			//scoring method Qmax
			if(isset($parameters['QMAX'])){
				$result = $xpath->query("//tao:HASSCORINGMETHOD");
				if($result->item(0)){
					$node = $result->item(0);
					$node->setAttribute('Qmax', $parameters['QMAX']);
				}
				else{
					$node = $dom->createElement('tao:HASSCORINGMETHOD', 'CLASSICAL RATIO');
					$node->setAttribute('Qmax', $parameters['QMAX']);
					$root->appendChild($node);
				}
			}
			
			//scoring method Qiter
			if(isset($parameters['QITER'])){
				$result = $xpath->query("//tao:HASSCORINGMETHOD");
				if($result->item(0)){
					$node = $result->item(0);
					$node->setAttribute('Qiter', $parameters['QITER']);
				}
				else{
					$node = $dom->createElement('tao:HASSCORINGMETHOD', 'CLASSICAL RATIO');
					$node->setAttribute('Qiter', $parameters['QITER']);
					$root->appendChild($node);
				}
			}
			
			//cumul model
			if(isset($parameters['cumulmodel'])){
				$result = $xpath->query("//tao:CUMULMODEL");
				if($result->item(0)){
					$node = $result->item(0);
					$node->nodeValue = $parameters['cumulmodel'];
				}
				else{
					$root->appendChild(
						$dom->createElement('tao:CUMULMODEL', $parameters['cumulmodel'])
					);
				}
			}
			
			//halt criteria
			if(isset($parameters['haltcriteria'])){
				$result = $xpath->query("//tao:HALTCRITERIA");
				if($result->item(0)){
					$node = $result->item(0);
					$node->nodeValue = $parameters['haltcriteria'];
				}
				else{
					$root->appendChild(
						$dom->createElement('tao:HALTCRITERIA', $parameters['haltcriteria'])
					);
				}
			}
			
			//scoring method MAX
			if(isset($parameters['max'])){
				$result = $xpath->query("//tao:HALTCRITERIA");
				if($result->item(0)){
					$node = $result->item(0);
					$node->setAttribute('MAX', $parameters['max']);
				}
				else{
					$node = $dom->createElement('tao:HALTCRITERIA');
					$node->setAttribute('MAX', $parameters['max']);
					$root->appendChild($node);
				}
			}
			
			
			//threshold1
			
			if(isset($parameters['thresh1']) || isset($parameters['thresh2']) || isset($parameters['thresh3'])){
				$result = $xpath->query("//tao:LAUNCH[@plugin='CLLPlugin']");
				if(!$result->item(0)){
					$node = $dom->createElement('tao:LAUNCH');
					$node->setAttribute('plugin', 'CLLPlugin');
					
					$node->appendChild($dom->createElement('cll:threshold'));
					$node->appendChild($dom->createElement('cll:threshold'));
					$node->appendChild($dom->createElement('cll:threshold'));
					
					$root->appendChild($node);
				}
			}
			
			if(isset($parameters['thresh1'])){
				$result = $xpath->query("//cll:threshold");
				if($result->item(0)){
					$result->item(0)->nodeValue = $parameters['thresh1'];
				}
			}
			
			if(isset($parameters['thresh2'])){
				$result = $xpath->query("//cll:threshold");
				if($result->item(1)){
					$result->item(1)->nodeValue = $parameters['thresh2'];
				}
			}
			
			if(isset($parameters['thresh3'])){
				$result = $xpath->query("//cll:threshold");
				if($result->item(2)){
					$result->item(2)->nodeValue = $parameters['thresh3'];
				}
			}
			
			$result = $xpath->query("//box[@id='itemContainer_box']");
			if($result->item(0)){
				$node = $result->item(0);
				if(isset($parameters['itemleft'])){
					$node->setAttribute('left', $parameters['itemleft']);
				}
				if(isset($parameters['itemtop'])){
					$node->setAttribute('top', $parameters['itemtop']);
				}
			}
			else{
				$result = $xpath->query("//testContainer_box");
				if($result->item(0)){
					$parentNode = $result->item(0);
					$node = $dom->createElement('button');
					$node->setAttribute('id', 'itemContainer_box');
					if(isset($parameters['itemleft'])){
						$node->setAttribute('left', $parameters['itemleft']);
					}
					if(isset($parameters['itemtop'])){
						$node->setAttribute('top', $parameters['itemtop']);
					}
					$parentNode->appendChild($node);
				}
			}
			
			//display
			if(isset($parameters['display'])){
				
				if(in_array('deactivateback', $parameters['display'])){
					$result = $xpath->query("//button[@id='prevItem_button']");
					if($result->item(0)){
						$node = $result->item(0);
						$node->parentNode->removeChild($node);
					}
				}
				else{
					$result = $xpath->query("//button[@id='prevItem_button']");
					if($result->item(0)){
						$node = $result->item(0);
						if(isset($parameters['urlleft'])){
							$node->setAttribute('url', $parameters['urlleft']);
						}
						if(isset($parameters['navleft'])){
							$node->setAttribute('left', $parameters['navleft']);
						}
						if(isset($parameters['navtop'])){
							$node->setAttribute('top', $parameters['navtop']);
						}
					}
					else{
						$result = $xpath->query("//box[@id='testContainer_box']");
						if($result->item(0)){
							$parentNode = $result->item(0);
							$node = $dom->createElement('button');
							$node->setAttribute('id', 'prevItem_button');
							$node->setAttribute('label', 'Back');
							$node->setAttribute('image', 'item_previous.jpg'); 
							$node->setAttribute('disabled', 'true');
							$node->setAttribute('oncommand', 'tao_test.prevItem');
							if(isset($parameters['urlleft'])){
								$node->setAttribute('url', $parameters['urlleft']);
							}
							if(isset($parameters['navleft'])){
								$node->setAttribute('left', $parameters['navleft']);
							}
							if(isset($parameters['navtop'])){
								$node->setAttribute('top', $parameters['navtop']);
							}
							$parentNode->appendChild($node);
						}
					}
				}
				
				$result = $xpath->query("//button[@id='nextItem_button']");
				if($result->item(0)){
					$node = $result->item(0);
					if(isset($parameters['urlright'])){
						$node->setAttribute('url', $parameters['urlright']);
					}
					if(isset($parameters['navleft'])){
						$node->setAttribute('left', $parameters['navleft']);
					}
					if(isset($parameters['navtop'])){
						$node->setAttribute('top', $parameters['navtop']);
					}
				}
				else{
					$result = $xpath->query("//box[@id='testContainer_box']");
					if($result->item(0)){
						$parentNode = $result->item(0);
						$node = $dom->createElement('button');
						$node->setAttribute('id', 'nextItem_button');
						$node->setAttribute('label', 'Next');
						$node->setAttribute('image', 'item_next.jpg'); 
						$node->setAttribute('disabled', 'true');
						$node->setAttribute('oncommand', 'tao_test.nextItem');
						if(isset($parameters['urlright'])){
							$node->setAttribute('url', $parameters['urlright']);
						}
						if(isset($parameters['navleft'])){
							$node->setAttribute('left', $parameters['navleft']);
						}
						if(isset($parameters['navtop'])){
							$node->setAttribute('top', $parameters['navtop']);
						}
						$parentNode->appendChild($node);
					}
				}
				
				if(!in_array('showprogessbar', $parameters['display'])){
					$result = $xpath->query("//progressmeter[@id='test_progressmeter']");
					if($result->item(0)){
						$node = $result->item(0);
						$node->parentNode->removeChild($node);
					}
				}
				else{
					$result = $xpath->query("//progressmeter[@id='test_progressmeter']");
					if(!$result->item(0)){
						$result = $xpath->query("//box[@id='testContainer_box']");
						if($result->item(0)){
							$parentNode = $result->item(0);
							$node = $dom->createElement('progressmeter');
							$node->setAttribute('id', 'test_progressmeter');
							$parentNode->appendChild($node);
						}
					}
				}
				$result = $xpath->query("//progressmeter[@id='test_progressmeter']");
				if($result->item(0)){
					$node = $result->item(0);
					if(isset($parameters['progressbarleft'])){
						$node->setAttribute('left', $parameters['progressbarleft']);
					}
					if(isset($parameters['progressbartop'])){
						$node->setAttribute('top', $parameters['progressbartop']);
					}
				}
				
				if(!in_array('showlistbox', $parameters['display'])){
					$result = $xpath->query("//listbox[@id='testItems_listbox']");
					if($result->item(0)){
						$node = $result->item(0);
						$node->parentNode->removeChild($node);
					}
				}
				else{
					$result = $xpath->query("//listbox[@id='testItems_listbox']");
					if(!$result->item(0)){
						$result = $xpath->query("//box[@id='testContainer_box']");
						if($result->item(0)){
							$parentNode = $result->item(0);
							$node = $dom->createElement('listbox');
							$node->setAttribute('id', 'testItems_listbox');
							$parentNode->appendChild($node);
						}
					}
				}
				
				if(!in_array('showLabel', $parameters['display'])){
					$result = $xpath->query("//label[@id='testLabel_label']");
					if($result->item(0)){
						$node = $result->item(0);
						$node->parentNode->removeChild($node);
					}
				}
				else{
					$result = $xpath->query("//listbox[@id='testItems_listbox']");
					if(!$result->item(0)){
						$result = $xpath->query("//label[@id='testLabel_label']");
						if($result->item(0)){
							$parentNode = $result->item(0);
							$node = $dom->createElement('label');
							$node->setAttribute('id', 'testLabel_label');
							$parentNode->appendChild($node);
						}
					}
				}
				if(!in_array('showComment', $parameters['display'])){
					$result = $xpath->query("//label[@id='testComment_label']");
					if($result->item(0)){
						$node = $result->item(0);
						$node->parentNode->removeChild($node);
					}
				}
				else{
					$result = $xpath->query("//listbox[@id='testItems_listbox']");
					if(!$result->item(0)){
						$result = $xpath->query("//label[@id='testComment_label']");
						if($result->item(0)){
							$parentNode = $result->item(0);
							$node = $dom->createElement('label');
							$node->setAttribute('id', 'testComment_label');
							$parentNode->appendChild($node);
						}
					}
				}
			}
			
			$returnValue = $this->setTestContent($test, $dom->saveXML());
		}
		catch(DOMException $domExp){ }
		
        // section 127-0-1-1-6730a7c:126559edebd:-8000:0000000000001E24 end

        return (bool) $returnValue;
    }
	
	/**
     * Build a sequential process for the item from an array of tests 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource test
	 * @param  array items
     * @return boolean
     */
	public function setTestItems(core_kernel_classes_Resource $test, $items){
		
		$returnValue = false;
		
		$authoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
		
		// get the current process:
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		
		$var_delivery = $authoringService->getProcessVariable("delivery");
		if(is_null($var_delivery)){
			$var_delivery = $authoringService->createProcessVariable("delivery", "delivery");
		}
		
		if(is_null($var_delivery)){
			throw new Exception('the required process variable "delivery" is missing: ');
		}
		
		//create formal param associated to the 3 required service parameter:
		$itemUriParam = $authoringService->getFormalParameter('itemUri');//it is alright if the default value (i.e. proc var has been changed)
		if(is_null($itemUriParam)){
			$itemUriParam = $authoringService->createFormalParameter('itemUri', 'constant', '', 'item uri (exe)');
		}
		
		$testUriParam = $authoringService->getFormalParameter('testUri');
		if(is_null($testUriParam)){
			$testUriParam = $authoringService->createFormalParameter('testUri', 'constant', '', 'test uri(exe)');
		}
		
		$deliveryUriParam = $authoringService->getFormalParameter('deliveryUri');
		if(is_null($deliveryUriParam)){
			$deliveryUriParam = $authoringService->createFormalParameter('deliveryUri', 'processvariable', $var_delivery->uriResource, 'delivery uri (exe)');
		}
		
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
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE), INSTANCE_ACL_ROLE);//should be eventually INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE), CLASS_ROLE_SUBJECT);
			
			
			//get the item runner service definition, if does not exist, create one:
			$itemRunnerServiceUrl = $authoringService->getItemRunnerUrl();
			$serviceDefinition = wfEngine_helpers_ProcessUtil::getServiceDefinition($itemRunnerServiceUrl);
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
			
			//create a call of service and associate the service definition to it:
			$interactiveService = $authoringService->createInteractiveService($activity);
			$interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
			$authoringService->setActualParameter($interactiveService, $itemUriParam, $item->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN);//constant: we know it!
			$authoringService->setActualParameter($interactiveService, $testUriParam, $test->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN);//constant: we know it!
			$authoringService->setActualParameter($interactiveService, $deliveryUriParam, $var_delivery->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMIN, PROPERTY_ACTUALPARAM_PROCESSVARIABLE);//don't know yet so process var!
			
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
		
		return $returnValue;
	}
	
	/**
     * Get an ordered array of items that make up a sequential test process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource delivery
     * @return array
     */
	public function getTestItems(core_kernel_classes_Resource $test){
		
		$returnValue = array();
		
		$items = array();
		// $authoringService = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
		$authoringService = new taoTests_models_classes_TestAuthoringService();
		
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
		// var_dump($activities, $currentActivity->getLabel());
		//start the loop:
		for($i=0;$i<$totalNumber;$i++){
			$item = $authoringService->getItemByActivity($currentActivity);
			if(!is_null($item)){
				$items[$i] = $item;
			}
			
			//get its connector (check the type is "sequential) if ok, get the next activity
			$connectorCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $currentActivity->uriResource);
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
		
		return $returnValue;
	}
	
	public function linearizeTestProcess(core_kernel_classes_Resource $test){
	
		$returnValue = false;
		
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
		
		return $returnValue;
	}
	
	public function setAuthoringMode(core_kernel_classes_Resource $test, $mode){
	
		$property = new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP);
		switch(strtolower($mode)){
			case 'simple':{
				$test->editPropertyValues($property, TAO_TEST_SIMPLEMODE);
				//linearization required:
				$this->linearizeTestProcess($test);
				break;
			}
			case 'advanced':{
				$test->editPropertyValues($property, TAO_TEST_ADVANCEDMODE);
				break;
			}
			default:{
				return false;
			}
		}
		
		
	}
} /* end of class taoTests_models_classes_TestsService */

?>