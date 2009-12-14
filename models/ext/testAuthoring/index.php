<?php
if(isset($_POST['testcontent'])){
	require('../../../../generis/common/inc.extension.php');
	require('../../../includes/common.php');
	require_once('TAOTsaveContent.php');
	
	$_SESSION['instance'] = $_POST['instance'];
	
	$TAOTsaveContent = new TAOTsaveContent();
	$_SESSION['xml'] = $TAOTsaveContent->getOutput($_POST['testcontent']);
}
?>
<script type="text/javascript">
	window.top.location = '/taoTests/Tests/saveTestContent';
</script>
