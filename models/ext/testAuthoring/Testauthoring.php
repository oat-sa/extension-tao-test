<?php
require('../../../includes/common.php');


require('TAOAuthoringTGUI.php');
$TAOAuthoringTGUI = new TAOAuthoringTGUI($_GET['xml'], $_GET['instance']);
echo $TAOAuthoringTGUI->getOutput();
?>