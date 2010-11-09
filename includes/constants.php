<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

include_once ROOT_PATH . '/tao/includes/constants.php';

//include constants for the wfEngine:
include_once ROOT_PATH . '/wfEngine/includes/constants.php';

$todefine = array(
	'TEST_RELATED_ITEMS_PROP' 			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile',
	'TEST_CONTENT_REF_FILE'				=> BASE_PATH.'/data/test_content_ref.xml',
	'TEST_TESTPROCESS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#TestProcess',
	'TAO_TEST_AUTHORINGMODE_PROP' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode',
	'TAO_TEST_SIMPLEMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811802',
	'TAO_TEST_ADVANCEDMODE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811803'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>