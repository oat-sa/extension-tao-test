<?php

use oat\tao\test\RestTestCase;

class RestTestsTest extends RestTestCase
{
    public function serviceProvider(){
        return array(
            array('taoTests/RestTests')
        );
    }
}