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
require_once('tao/models/classes/class.Service.php');

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
    extends tao_models_classes_Service
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

    /**
     * The ontologies to load
     *
     * @access protected
     * @var array
     */
    protected $testsOntologies = array('http://www.tao.lu/Ontologies/TAOTest.rdf', 'http://www.tao.lu/Ontologies/TAOItem.rdf');

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
		
		$this->testClass = new core_kernel_classes_Class(TEST_CLASS);
		$this->loadOntologies($this->testsOntologies);
		
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
     * Short description of method isTestClass
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
			
			$returnValue = $test->getPropertyValues(new core_kernel_classes_Property(TEST_RELATED_ITEMS_PROP));
			
			//order the item using the "/tao:TEST/tao:CITEM[Sequence]" attribute in the testcontent xml
			if($sequenced){
				try{
					$content = $this->getTestContent($test);
					if(!empty($content)){
						
						$sequencedItems = array();
						$unSequencedItems = array();
						
						$xml = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOERROR );
						if($xml instanceof SimpleXMLElement){
							$nodes = $xml->xpath('/tao:TEST/tao:CITEM');
							foreach($nodes as $node){
								$uri = (string)$node;
								if(!empty($uri)){
									if(in_array($uri, $returnValue)){
										$index = 0;
										if(isset($node['Sequence'])){
											$index = (int)$node['Sequence'];
										}
										if($index > 0){
											$sequencedItems[(int)$node['Sequence']] = $uri;
										}
										else{
											$unSequencedItems[] = $uri;
										}
									}
								}
							}
							ksort($sequencedItems);
							
							//push items without sequence at the end 
							if(count($unSequencedItems) > 0){
								foreach($unSequencedItems as $item){
									$sequencedItems[count($sequencedItems) + 1] = $item;
								}
							}
							$returnValue = $sequencedItems;
						}
					}
				}
				catch(Exception $e){ }
			}
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
     * Short description of method getTestContent
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
				echo $testContent;
				if($this->setTestContent($test, $testContent)){
					$returnValue = (string)$testContent;
				}
			}
			else if(count($testContents) == 1){	//get it
				$returnValue =  $testContents[0];
			}
			else{	//remove them 
				$test->removePropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			}
		}
		
        // section 127-0-1-1--645eb059:1260e94e6e6:-8000:0000000000001DEA end

        return (string) $returnValue;
    }

    /**
     * Short description of method setTestContent
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @param  string content
     * @return boolean
     */
    public function setTestContent( core_kernel_classes_Resource $test, $content = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--645eb059:1260e94e6e6:-8000:0000000000001DED begin
		
		if(!is_null($test)){
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
     * Short description of method initTestContent
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource test
     * @return string
     */
    public function initTestContent( core_kernel_classes_Resource $test)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--18790a60:12622d03866:-8000:0000000000001DFB begin
		
		$dom = new DOMDocument();
		$dom->load(TEST_CONTENT_REF_FILE);
		$root = $dom->documentElement;
		
		$root->setAttribute('rdf:ID', $test->uriResource);
		foreach($root->getElementsByTagNameNS('http://www.w3.org/TR/1999/PR-rdf-schema-19990303#','LABEL') as $labelNode){
			$labelNode->nodeValue = $test->getLabel();
		}
		foreach($root->getElementsByTagNameNS('http://www.w3.org/TR/1999/PR-rdf-schema-19990303#', 'COMMENT') as $commentNode){
			$commentNode->nodeValue = $test->comment;
		}
		$returnValue = $dom->saveXML();
		
        // section 127-0-1-1--18790a60:12622d03866:-8000:0000000000001DFB end

        return (string) $returnValue;
    }

} /* end of class taoTests_models_classes_TestsService */

?>