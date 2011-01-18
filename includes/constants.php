<?php
/**
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
//include constants for the wfEngine:
include_once ROOT_PATH . '/wfEngine/includes/constants.php';

include_once ROOT_PATH . '/tao/includes/constants.php';

$todefine = array(
	'TEST_TESTCONTENT_PROP'	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent',
	
	'TEST_ACTIVE_PROP' 	=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#active',
	
	'TAO_TEST_AUTHORINGMODE_PROP' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#AuthoringMode',
	'TAO_TEST_SIMPLEMODE' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#i1268049036038811802',
	'TAO_TEST_ADVANCEDMODE' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#i1268049036038811803',
	
	'INSTANCE_FORMALPARAM_ITEMURI' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1288956259074862500',
	'INSTANCE_FORMALPARAM_TESTURI' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1288956260043375900',
	'INSTANCE_FORMALPARAM_DELIVERYURI' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1288956261000699400',
	'INSTANCE_PROCESSVARIABLE_DELIVERY' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1278922897063796600',
	'INSTANCE_SERVICEDEFINITION_ITEMRUNNER' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1288956262090045500'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>