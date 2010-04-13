<?php
/**
 * SaSTests Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSTests extends Tests {

    
    
    /**
     * @see Tests::__construct()
     */
    public function __construct() {
        $this->setSessionAttribute('currentExtension', 'taoTests');
		tao_helpers_form_GenerisFormFactory::setMode(tao_helpers_form_GenerisFormFactory::MODE_STANDALONE);
		parent::__construct();
    }
    
 	/**
     * Give the auth to the workflow engine
     * @return boolean
     */
    protected function _isAllowed(){
    	return isset($_SESSION['taoqual.authenticated']);
    }
    	
	
	/**
     * @see TaoModule::setView()
     */
    public function setView($identifier, $useMetaExtensionView = false) {
		if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', BASE_PATH . '/' . DIR_VIEWS . $GLOBALS['dir_theme'] . $identifier);
		}
		parent::setView('sas.tpl', true);
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
		
		$relatedItems = $this->service->getRelatedItems($test, true);
		$this->setData('relatedItems', json_encode(array_map("tao_helpers_Uri::encode", $relatedItems)));
		
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