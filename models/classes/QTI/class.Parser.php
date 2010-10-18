<?php

error_reporting(E_ALL);

/**
 * TAO - taoTests/models/classes/QTI/class.Parser.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.10.2010, 11:58:49 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Parser enables you to load, parse and validate xml content from an xml
 * Usually used for to load and validate the itemContent  property.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Parser.php');

/* user defined includes */
// section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026A7-includes begin
// section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026A7-includes end

/* user defined constants */
// section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026A7-constants begin
// section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026A7-constants end

/**
 * Short description of class taoTests_models_classes_QTI_Parser
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_QTI
 */
class taoTests_models_classes_QTI_Parser
    extends tao_models_classes_Parser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method validate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function validate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026A8 begin
        
        $returnValue = parent::validate(dirname(__FILE__).'/data/imscp_v1p1.xsd');
        
        // section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026A8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function load()
    {
        $returnValue = array();

        // section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026AA begin
        // section 127-0-1-1-4e1fc318:12bbead867c:-8000:00000000000026AA end

        return (array) $returnValue;
    }

} /* end of class taoTests_models_classes_QTI_Parser */

?>