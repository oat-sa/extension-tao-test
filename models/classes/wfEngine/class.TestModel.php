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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * the wfEngine TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_wfEngine
 */
class taoTests_models_classes_wfEngine_TestModel
	implements taoTests_models_classes_TestModel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * default constructor to ensure the implementation
     * can be instanciated
     */
    public function __construct() {
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
    	$processInstance = wfEngine_models_classes_ProcessDefinitionService::singleton()->createInstance(new core_kernel_classes_Class(CLASS_PROCESS),'process generated with testsService');

		//set ACL right to delivery process initialization:
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
		$processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);

		$test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $processInstance->getUri());
		$processInstance->setLabel("Process ".$test->getLabel());
		taoTests_models_classes_TestsService::singleton()->setTestItems($test, $items);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
    	$content = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
    	if (!is_null($content)) {
	    	$content->delete();
	    	$test->removePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $content);
    	}
    }
    
    public function getItems( core_kernel_classes_Resource $test) {
		$items = array();
		$authoringService = taoTests_models_classes_TestAuthoringService::singleton();

		//get the associated process:
		$process = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));

		if (!is_null($process)) {
			//get list of all activities:
			$activities = $authoringService->getActivitiesByProcess($process);
	
			foreach($activities as $activity){
				$item = $authoringService->getItemByActivity($activity);
				if(!is_null($item)){
					$items[] = $item;
				}
			}
		}

    	return $items;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring( core_kernel_classes_Resource $test) {
    	$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
 		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoTests');
    	$widget = new Renderer($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring'.DIRECTORY_SEPARATOR.'workflow.tpl');
		$widget->setData('processUri', $process->getUri());
		$widget->setData('label', __('Authoring %s', $test->getLabel()));
    	return $widget->render();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
	public function onChangeTestLabel( core_kernel_classes_Resource $test) {
		$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		$process->setLabel("Process ".$test->getLabel());
	}
    
}

?>