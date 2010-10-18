<?php
require_once('tao/actions/Import.class.php');

/**
 * This controller provide the actions to import test 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @subpackage action
 *
 */
class TestImport extends Import {

	public function __construct(){
		parent::__construct();
		$this->formContainer = new taoTests_actions_form_Import();
	}
	
	/**
	 * action to perform on a posted QTI file
	 * @param array $formValues the posted data
	 */
	protected function importQTIFile($formValues){
		if(isset($formValues['source']) && $this->hasSessionAttribute('classUri')){
			
			//get the item parent class
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
			
			/// HERE ///
		}
	}
}
?>
