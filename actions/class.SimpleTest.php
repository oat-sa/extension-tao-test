<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Controller for actions related to the simple test model
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoTests
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoTests_actions_SimpleTest extends tao_actions_SaSModule {

	/**
	 * (non-PHPdoc)
	 * @see tao_actions_SaSModule::getClassService()
	 */
	protected function getClassService() {
		return taoTests_models_classes_TestsService::singleton();
	}
	
	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct()
	{

		parent::__construct();

		//the service is initialized by default
		$this->service = taoTests_models_classes_TestsService::singleton();
	}

	/**
	 * save the related items from the checkbox tree or from the sequence box
	 * @return void
	 */
	public function saveItems()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;

		$items = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($value));
				$itemModel = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
				$supported = false;
				if (!is_null($itemModel)) {
					foreach ($itemModel->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODELTARGET_PROPERTY)) as $targeturi) {
						if ($targeturi == TAO_ITEM_ONLINE_TARGET) {
							$supported = true;
							break;
						}
					}
				}
				if ($supported) {
					array_push($items, $item);
				} else {
					throw new common_Exception($item->getLabel().' cannot be added to a test');
				}
			}
		}
		if($this->service->setTestItems($this->getCurrentInstance(), $items)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}

}
?>