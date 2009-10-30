<?php
class Tests extends Module {

	protected $service = null;

	public function __construct(){
		$this->service = tao_models_classes_ServiceFactory::get('Tests');
	}
	
	/**
	 * main action
	 * @return void
	 */
	public function index(){
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
}
?>