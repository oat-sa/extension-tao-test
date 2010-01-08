<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoTests/actions/form/class.TestAuthoring.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.01.2010, 15:47:35 with ArgoUML PHP module 
 * (last revised $Date: 2009-04-11 21:57:46 +0200 (Sat, 11 Apr 2009) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_FormContainer
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DE7-includes begin
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DE7-includes end

/* user defined constants */
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DE7-constants begin
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DE7-constants end

/**
 * Short description of class taoTests_actions_form_TestAuthoring
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage actions_form
 */
class taoTests_actions_form_TestAuthoring
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DE9 begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('test_authoring');
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DE9 end
    }

    /**
     * Short description of method initElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DED begin
		
		
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DED end
    }

} /* end of class taoTests_actions_form_TestAuthoring */

?>