<?php

/**
 * Tests Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
class taoTests_actions_Tests extends tao_actions_TaoModule {

	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct(){
	
		parent::__construct();
	
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestsService');
		$this->defaultData();
	}
	
	
	/**
	 * Override auth method
	 * @see TaoModule::_isAllowed
	 * @return boolean
	 */	
	protected function _isAllowed(){
		$context = Context::getInstance();
		if($context->getActionName() != 'getTestContent'){
			return parent::_isAllowed();
		}
		return true;
	}
	
	
/*
 * conveniance methods
 */
	
	/**
	 * get the instancee of the current test regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the test instance
	 */
	protected function getCurrentInstance(){
		
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		
		$test = $this->service->getTest($uri, 'uri', $clazz);
		if(is_null($test)){
			throw new Exception("No test found for the uri {$uri}");
		}
		
		return $test;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getTestClass();
	}
	
	
/*
 * controller actions
 */
	
	
	/**
	 * edit a test instance
	 * @return void
	 */
	public function editTest(){
		$clazz = $this->getCurrentClass();
		$test = $this->getCurrentInstance();
		
		$formContainer = new tao_actions_form_Instance($clazz, $test);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$propertyValues = $myForm->getValues();
				
				//check if the authoring mode has changed: if advanced->simple, modify the related process to make it compatible
				if(array_key_exists(TAO_TEST_AUTHORINGMODE_PROP, $propertyValues)){
					if($propertyValues[TAO_TEST_AUTHORINGMODE_PROP] == TAO_TEST_SIMPLEMODE){
						if($test->getUniquePropertyValue(new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP))->uriResource == TAO_TEST_ADVANCEDMODE){
							//get all tests from the process, then save them:
							$this->service->linearizeTestProcess($test);
						}
					}
				}
				
				//then save the property values as usual
				$test = $this->service->bindProperties($test, $propertyValues);
				
				//edit process label:
				$this->service->updateProcessLabel($test);
				
				$this->setData('message', __('Test saved'));
				$this->setData('reload', true);
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));
		
		//test authoring mode:
		$this->setData('authoringMode', 'simple');
		$authoringMode = $test->getUniquePropertyValue(new core_kernel_classes_Property(TAO_TEST_AUTHORINGMODE_PROP));
		$myForm->removeElement(tao_helpers_Uri::encode(TAO_TEST_AUTHORINGMODE_PROP));
		
		if($authoringMode->uriResource == TAO_TEST_ADVANCEDMODE){
			$this->setData('authoringMode', 'advanced');
		}else{
			//remove the authoring button
			$myForm->removeElement(tao_helpers_Uri::encode(TEST_TESTCONTENT_PROP));
			
			//the default option is the simple mode:
			$allItems = array();
			foreach($this->service->getAllItems() as $itemUri => $itemLabel){
				$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
			}
			$this->setData('allItems', json_encode($allItems));
			
			$relatedItems = array();
			$itemSequence = array();
			$i = 1;
			foreach($this->service->getTestItems($test) as $item){
				
				$relatedItems[] = tao_helpers_Uri::encode($item->uriResource);
				if(!$item->isClass()){
					$itemSequence[$i] = array(
						'uri' 	=> tao_helpers_Uri::encode($item->uriResource),
						'label' => $item->getLabel()
					);
					$i++;
				}
			}
			
			$this->setData('itemSequence', $itemSequence);
			$this->setData('relatedItems', json_encode($relatedItems));
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($test->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', __('Test properties'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_test.tpl');
	}
	
	/**
	 * add a test (subclass Test)
	 * @return void
	 */
	public function addTestClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createTestClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
	}
	
	/**
	 * Edit a test model (edit a class)
	 * @return void
	 */
	public function editTestClass(){
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getTestClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit test class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * delete a test or a test class
	 * called via ajax
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteTest($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteTestClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
		
	
	
	/**
	 * display the authoring  template
	 * @return void
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			
			//get process instance to be authored
			 $test = $this->getCurrentInstance();
			 $processDefinition = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			$this->setData('processUri', tao_helpers_Uri::encode($processDefinition->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('authoring/process_authoring_tool.tpl');
	}
	
	/**
		deprecated
	 * display the item sequence tempalte and initialize the grid component
	 * @return void 
	 */
	public function itemSequence(){
		$test = $this->getCurrentInstance();
		$clazz =  $this->getCurrentClass();
		
		$this->setData('uri', tao_helpers_Uri::encode($test->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setView('itemsequence.tpl');
	}
	
	/**
	 * provide the user list data via json
	 * @return void
	 */
	public function itemSequenceData(){
		$page = $this->getRequestParameter('page'); 
		$limit = $this->getRequestParameter('rows'); 
		$sidx = $this->getRequestParameter('sidx');  
		$sord = $this->getRequestParameter('sord'); 
		$start = $limit * $page - $limit; 
		
		if(!$sidx) $sidx =1; // connect to the database 
		
		$test = $this->getCurrentInstance();
		$items = $this->service->getItemSequence($test, array(
			'order' 	=> $sidx,
			'orderDir'	=> $sord,
			'start'		=> $start,
			'end'		=> $limit
		));
		
		$count = count($items); 
		if( $count >0 ) { 
			$total_pages = ceil($count/$limit); 
		} 
		else { 
			$total_pages = 0; 
		} 
		if ($page > $total_pages){
			$page = $total_pages; 
		}
		
		$response = new stdClass();
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		$index = 0;
		foreach($items as $i => $item) { 
			$response->rows[$index]['id']= (string)$item['sequence']; 
			$response->rows[$index]['cell']= array(
				(string)$item['sequence'], 
				tao_helpers_Uri::encode($item['uri']), 
				$item['label'], 
				$item['weight'], 
				$item['difficulty'], 
				$item['discrimination'],
				$item['guessing'],
				$item['model']
			);
			$index++;
		} 
		echo json_encode($response); 
	}
	
	/**
	 * save the sequence
	 * @return void
	 */
	public function saveItemSequence(){
		
		$response = array('saved' => false);
		
		$test = $this->getCurrentInstance();
		$clazz =  $this->getCurrentClass();
		
		$sequence = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^item_/", $key)){
				$key = str_replace("item_", '', $key);
				$index = substr($key, 0, strpos($key, '_'));
				$key = substr($key, strpos($key, '_') + 1);
				if($key == 'uri'){
					$value = tao_helpers_Uri::decode($value);
				}
				$sequence[$index][$key] = $value;
			}
		}
		if($this->service->saveItemSequence($test, $sequence)){
			$response['saved'] = true;
		}
		
		echo json_encode($response);
	}
	
	/**
	 should be useless now
	 * get the xml content of a test over an http request 
	 * @return void 
	 */
	public function getTestContent(){
		header("Content-Type: text/xml; charset utf-8");
		try{
			print $this->service->getTestContent($this->getCurrentInstance());
			return;
		}
		catch(Exception $e){}
		print '<?xml version="1.0" encoding="utf-8" ?>';
	}
	
	/**
		deprecated?
	 * save the xml content of a test
	 * @return void
	 */
	public function saveTestContent(){
		
		$message = __('An error occured while saving the test');
		
		if(isset($_SESSION['instance']) && isset($_SESSION['xml'])){
		
			$test = $this->service->getTest($_SESSION['instance']);
			if($this->service->setTestContent($test, $_SESSION['xml'])){
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));
				$message = __('Test saved successfully');
			}
			unset($_SESSION['instance']);
			unset($_SESSION['xml']);
		}
		
		$this->redirect(_url('index', 'Main', 'tao', array('extension' => 'taoTests', 'message' => urlencode($message))));
	}
	
	/**
	 * Test preview 
	 * @return void
	 */
	public function preview(){
		//logout from TAO, login to the wf user "previewer" and open the process window, with the back button to TAO:
		throw new Exception("the preview for a test is no longer available");
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setView('preview.tpl');
	}
	
	/**
	 * get the list of items to populate the checkbox tree of related items
	 * @return void
	 */
	public function getItems(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array('chunk' => false);
		if($this->hasRequestParameter('classUri')) {
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		}
		if($this->hasRequestParameter('selected')){
			$selected = $this->getRequestParameter('selected');
			if(!is_array($selected)){
				$selected = array($selected);
			}
			$options['browse'] = $selected;
		}
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	/**
	 * save the related items from the checkbox tree
	 * @return void
	 */
	public function saveItems(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$items = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($items, new core_kernel_classes_Resource(tao_helpers_Uri::decode($value)));
			}
		}
		if($this->service->setTestItems($this->getCurrentInstance(), $items)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	public function advancedMode(){
		$this->setAuthoringMode('advanced');
	}
	
	public function simpleMode(){
		$this->setAuthoringMode('simple');
	}
	
	private function setAuthoringMode($mode){
		$mode = strtolower($mode);
		if($mode != 'simple' && $mode != 'advanced'){
			throw new Exception('invalid mode');
		}
		
		$test = $this->getCurrentInstance();
		$clazz = $this->getCurrentClass();
		
		$this->service->setAuthoringMode($test, $mode);
		
		$param = array(
			'uri' => tao_helpers_Uri::encode($test->uriResource),
			'classUri' => tao_helpers_Uri::encode($clazz->uriResource)
		);
		
		//reload the form, thus let the advanced authoring tab be available
		$this->redirect(tao_helpers_Uri::url('editTest', 'Tests', null, $param));
	}
}
?>