<?php
/**
 * SaSTests Controller provide process services on tests
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoTests_actions_SaSTests extends taoTests_actions_Tests {

    
    
    /**
     * @see Tests::__construct()
     */
    public function __construct() {
    	tao_helpers_Context::load('STANDALONE_MODE');
        $this->setSessionAttribute('currentExtension', 'taoTests');
		parent::__construct();
    }
    
	/**
	 * @see TaoModule::setView()
	 * @param string $identifier the view name
	 * @param boolean $useMetaExtensionView use a view from the parent extention
	 * @return mixed 
	 */
    public function setView($identifier, $useMetaExtensionView = false) {
		if(tao_helpers_Request::isAjax()){
			return parent::setView($identifier, $useMetaExtensionView);
		}
    	if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', DIR_VIEWS . 'templates/' . $identifier);
		}
		return parent::setView('sas.tpl', true);
    }
	
	/**
     * overrided to prevent exception: 
     * if no class is selected, the root class is returned 
     * @see TaoModule::getCurrentClass()
     * @return core_kernel_class_Class
     */
    protected function getCurrentClass() {
        if($this->hasRequestParameter('classUri')){
        	return parent::getCurrentClass();
        }
		return $this->getRootClass();
    }
	
	/**
	 * Render the tree and the list to select and order the test related items 
	 * @return void
	 */
	public function selectItems(){
		
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setData('classUri', $this->getRequestParameter('classUri'));
		
		$test = $this->getCurrentInstance();
		
		$allItems = array();
		foreach($this->service->getAllItems() as $itemUri => $itemLabel){
			$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
		}
		$this->setData('allItems', json_encode($allItems));
		
		$relatedItems = tao_helpers_Uri::encodeArray($this->service->getRelatedItems($test, true), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('relatedItems', json_encode($relatedItems));
		
		$itemSequence = array();
		foreach($relatedItems as $index => $itemUri){
			$item = new core_kernel_classes_Resource($itemUri);
			$itemSequence[$index] = array(
				'uri' 	=> tao_helpers_Uri::encode($itemUri),
				'label' => $item->getLabel()
			);
		}
		$this->setData('itemSequence', $itemSequence);
		$this->setView('items.tpl');
	}
	
}
?>