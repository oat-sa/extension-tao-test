<?php
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';

//get the test into each extensions
$tests = TaoTestRunner::getTests(array('taoTests'));

//create the test sutie
$testSuite = new TestSuite('TAO Test module unit tests');
foreach($tests as $testCase){
	$testSuite->addFile($testCase);
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}
define("PHPCOVERAGE_HOME", INCLUDES_PATH. "/spikephpcoverage/src");
require_once  PHPCOVERAGE_HOME. "/CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";
//run the unit test suite
$includePaths = array(ROOT_PATH.'taoTests/models',ROOT_PATH.'taoTests/helpers');
$excludePaths = array();
$covReporter = new HtmlCoverageReporter("Code Coverage Report taoTests", "", PHPCOVERAGE_REPORTS."/taoTests");
$cov = new CoverageRecorder($includePaths, $excludePaths, $covReporter);
//run the unit test suite
$cov->startInstrumentation();
$testSuite->run($reporter);
$cov->stopInstrumentation();
$cov->generateReport();
$covReporter->printTextSummary(PHPCOVERAGE_REPORTS.'/taoTests_coverage.txt');
?>