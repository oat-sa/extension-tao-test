<?php
require_once dirname(__FILE__) . '/../../tao/test/RestTestCase.php';

class RestTestsTest extends RestTestCase 
{
    public function serviceProvider(){
        return array(
            array('taoTests/RestTests')
        );
    }
}

?>