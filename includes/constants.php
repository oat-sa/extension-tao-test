<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

include_once ROOT_PATH . '/tao/includes/constants.php';

$todefine = array(
	'TEST_RELATED_ITEMS_PROP' 			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems',
	'TEST_TESTCONTENT_PROP' 			=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile',
	'TEST_CONTENT_REF_FILE'				=> BASE_PATH.'/data/test_content_ref.xml',
	'TEST_TESTPROCESS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#TestProcess'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>