<?php
$saved = false;
if(isset($_POST['testcontent'])){
	require('../../../../generis/common/inc.extension.php');
	require('../../../includes/common.php');
	require_once('TAOTsaveContent.php');
	
	$TAOTsaveContent = new TAOTsaveContent();
	$xml = $TAOTsaveContent->getOutput($_POST['testcontent']);
	
	$testService = tao_models_classes_ServiceFactory::get('Tests');
	$test = $testService->getTest($_POST['instance']);
	if(!is_null($test) && !empty($xml)){
		$test = $testService->bindProperties($test, array(TEST_TESTCONTENT_PROP => $xml));
		$saved = true;
		
	}
}
echo json_encode(array('saved' => $saved));
?>
