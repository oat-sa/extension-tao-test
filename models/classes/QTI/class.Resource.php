<?php

error_reporting(E_ALL);

/**
 * A resource respresent a QTI item from the point of view of the imsmanifest 
 * (ims_cp v1.1 : Content Packaging).
 * It provide the way to deploy the item in the file system.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026AE-includes begin
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026AE-includes end

/* user defined constants */
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026AE-constants begin
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026AE-constants end

/**
 * A resource respresent a QTI item from the point of view of the imsmanifest 
 * (ims_cp v1.1 : Content Packaging).
 * It provide the way to deploy the item in the file system.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_QTI
 */
class taoTests_models_classes_QTI_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute allowedTypes
     *
     * @access protected
     * @var array
     */
    protected static $allowedTypes = array('imsqti_item_xmlv2p0');

    /**
     * Short description of attribute identifier
     *
     * @access protected
     * @var string
     */
    protected $identifier = '';

    /**
     * Short description of attribute itemFile
     *
     * @access protected
     * @var string
     */
    protected $itemFile = '';

    /**
     * Short description of attribute auxiliaryFiles
     *
     * @access protected
     * @var array
     */
    protected $auxiliaryFiles = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @param  string type
     * @param  string file
     * @return mixed
     */
    public function __construct($id, $type, $file)
    {
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026D8 begin
        
    	if(!self::isAllowed($type)){
    		throw new Exception("Only the resources of the following type are supported : " . implode(',', self::$allowedTypes));
    	}
    	$this->identifier = $id;
    	$this->itemFile = $file;
    	
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026D8 end
    }

    /**
     * Short description of method isAllowed
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string type
     * @return boolean
     */
    public static function isAllowed($type)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--7b184bc:12bc4c3c18d:-8000:00000000000026D1 begin
        
        $returnValue = (!empty($type) && in_array($type, self::$allowedTypes));
        
        // section 127-0-1-1--7b184bc:12bc4c3c18d:-8000:00000000000026D1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemFile
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getItemFile()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026DD begin
        
        $returnValue = $this->itemFile;
        
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026DD end

        return (string) $returnValue;
    }

    /**
     * Short description of method addAuxiliaryFile
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return mixed
     */
    public function addAuxiliaryFile($file)
    {
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026DF begin
        
    	$this->auxiliaryFiles[] = $file;
    	
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026DF end
    }

    /**
     * Short description of method getAuxiliaryFiles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getAuxiliaryFiles()
    {
        $returnValue = array();

        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E2 begin
        
        $returnValue = $this->auxiliaryFiles;
        
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E2 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setAuxiliaryFiles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array files
     * @return mixed
     */
    public function setAuxiliaryFiles($files)
    {
        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026D4 begin
        
    	if(is_array($files)){
    		$this->auxiliaryFiles = $files;
    	}
    	
        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026D4 end
    }

} /* end of class taoTests_models_classes_QTI_Resource */

?>