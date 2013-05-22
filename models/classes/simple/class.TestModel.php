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
class taoTests_models_classes_simple_TestModel
	extends taoTests_models_classes_wfEngine_TestModel
	// implements taoTests_models_classes_TestModel
{
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring( core_kernel_classes_Resource $test) {
    	
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoTests');
    	$testService = taoTests_models_classes_TestsService::singleton();

    	$itemSequence = array();
		$itemUris = array();
		$i = 1;
		foreach($testService->getTestItems($test) as $item){
			$itemUris[] = $item->getUri();
			$itemSequence[$i] = array(
				'uri' 	=> tao_helpers_Uri::encode($item->getUri()),
				'label' => $item->getLabel()
			);
			$i++;
		}

		// data for item sequence, terrible solution
		// @todo implement an ajax request for labels or pass from tree to sequence
		$allItems = array();
		foreach($testService->getAllItems() as $itemUri => $itemLabel){
			$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
		}
		
    	$widget = new Renderer($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring'.DIRECTORY_SEPARATOR.'simple.tpl');
		$widget->setData('uri', $test->getUri());
    	$widget->setData('allItems', json_encode($allItems));
		$widget->setData('itemSequence', $itemSequence);
		
		// data for generis tree form
		$widget->setData('relatedItems', json_encode(tao_helpers_Uri::encodeArray($itemUris)));
		$openNodes = tao_models_classes_GenerisTreeFactory::getNodesToOpen($itemUris, new core_kernel_classes_Class(TAO_ITEM_CLASS));
		$widget->setData('itemRootNode', TAO_ITEM_CLASS);
		$widget->setData('itemOpenNodes', $openNodes);
    	return $widget->render();
    }

}

?>