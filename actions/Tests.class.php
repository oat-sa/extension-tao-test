<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Tests Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage actions
 */
class Tests extends TaoModule {


	public function __construct(){
	
		parent::__construct();
	
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Tests');
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
	private function getCurrentTest(){
		
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
	
/*
 * controller actions
 */
	
	/**
	 * main action
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the tests tree 
	 * 'modelType' must be in request parameter
	 * @return void
	 */
	public function getTests(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$highlightUri = '';
		if($this->hasSessionAttribute("showNodeUri")){
			$highlightUri = $this->getSessionAttribute("showNodeUri");
			unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		} 
		echo json_encode( $this->service->toTree( $this->service->getTestClass(), true, true, $highlightUri));
	}
	
	/**
	 * Add an test instance
	 * @return void
	 */
	public function addTest(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$test = $this->service->createInstance($clazz);
		if(!is_null($test) && $test instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $test->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($test->uriResource)
			));
		}
	}
	
	/**
	 * edit a test instance
	 */
	public function editTest(){
		$clazz = $this->getCurrentClass();
		$test = $this->getCurrentTest();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $test);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$test = $this->service->bindProperties($test, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));
				$this->setData('message', 'Test saved');
				$this->setData('reload', true);
				$this->forward('Tests', 'index');
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($test->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', 'Edit test');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
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
		$myForm = $this->editClass($clazz, $this->service->getTestClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'class saved');
				$this->setData('reload', true);
				$this->forward('Tests', 'index');
			}
		}
		$this->setData('formTitle', 'Edit test class');
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
			$deleted = $this->service->deleteTest($this->getCurrentTest());
		}
		else{
			$deleted = $this->service->deleteTestClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
		
	/**
	 * duplicate a test instance by property copy
	 * @return void
	 */
	public function cloneTest(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$test = $this->getCurrentTest();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($test->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($test->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * get the list data: all taoObjects children except the TAO_TEST_CLASS
	 * @return void
	 */
	public function getLists(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		return json_encode(
			$this->getListData(array(
				TAO_TEST_CLASS
			))
		);
	}
	
	/**
	 * 
	 * @return 
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			$test = $this->getCurrentTest();
			$clazz = $this->getCurrentClass();
			
			$this->setData('authoringFile', BASE_URL.'/models/ext/testAuthoring/Testauthoring.php');
			$this->setData('dataPreview', urlencode(_url('getTestContent', 'Tests', array('uri' => $test->uriResource, 'classUri' => $clazz->uriResource))));
			$this->setData('instanceUri', tao_helpers_Uri::encode($test->uriResource, false));
		}
		catch(Exception $e){
			$this->setData('error', true);
		}
		$this->setView('authoring.tpl');
	}
	
	public function getTestContent(){
		header("Content-Type: text/xml; charset utf-8");
		
		try{
			$test = $this->getCurrentTest();
			$testContent = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			
			if($testContent instanceof core_kernel_classes_Literal){
				
				echo (string)$testContent;
			}
			else{
				throw Exception("{$test->uriResource} has no test content.");
			}
		}
		catch(Exception $e){
			error_log($e->getMessage());
			//print an empty response
			echo '<?xml version="1.0" encoding="utf-8" ?>';
		}
	}
	
	public function saveTestContent(){
		
		$message = __('An error occured while saving the test');
		
		if(isset($_SESSION['instance']) && isset($_SESSION['xml'])){
		
			$test = $this->service->getTest($_SESSION['instance']);
			if(!is_null($test)){
				$test = $this->service->bindProperties($test, array(TEST_TESTCONTENT_PROP => $_SESSION['xml']));
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->uriResource));
				$message = __('Test saved successfully');
			}
			unset($_SESSION['instance']);
			unset($_SESSION['xml']);
		}
		
		$this->redirect('/tao/Main/index?extension=taoTests&message='.urlencode($message));
	}
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getItems(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( $this->service->getTestClass(), true, true, ''));
	}
	
	public function saveItems(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		echo json_encode(array('saved'	=> $saved));
	}
	
	public function getMetaData(){
		throw new Exception("Not yet implemented");
	}
	
	public function saveComment(){
		throw new Exception("Not yet implemented");
	}
	
}
?>