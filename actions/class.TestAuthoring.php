<?php
/**
 * DeliveryAuthoring Controller provide actions to edit a delivery
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class taoTests_actions_TestAuthoring extends wfAuthoring_actions_ProcessAuthoring {
	
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoTests_models_classes_TestAuthoringService::singleton();
		$this->defaultData();
	}
}	