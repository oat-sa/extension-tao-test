<?php
require('../../../../generis/common/inc.extension.php');
require('../../../includes/common.php');

$userService = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
$currentUser = $userService->getCurrentUser(Session::getAttribute(tao_models_classes_UserService::LOGIN_KEY));
if(isset($currentUser['login'])){
	$_SESSION["datalg"] = $userService->getUserLanguage($currentUser['login']);
}

require('TAOAuthoringTGUI.php');
$TAOAuthoringTGUI = new TAOAuthoringTGUI($_GET['xml'], $_GET['instance']);
echo $TAOAuthoringTGUI->getOutput();
?>