<?php

error_reporting(E_ALL);

/**
 * The ParserFactory class provides methods to build Business resources from
 * data source
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E7-includes begin
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E7-includes end

/* user defined constants */
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E7-constants begin
// section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E7-constants end

/**
 * The ParserFactory class provides methods to build Business resources from
 * data source
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoTests
 * @subpackage models_classes_QTI
 */
class taoTests_models_classes_QTI_ParserFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Build the QTI_Resource elements contained in the IMSManifest given by the
     * source
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement source
     * @return array
     */
    public static function getResourcesFromManifest( SimpleXMLElement $source)
    {
        $returnValue = array();

        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E8 begin
        
        //check of the root tag
    	if($source->getName() != 'manifest'){
	       	throw new Exception("incorrect manifest root tag");
	    }
	    
	    $resourceNodes = $source->xpath("//*[name(.)='resource']");
	    foreach($resourceNodes as $resourceNode){
	    	$type = (string)$resourceNode['type'];
	    	if(taoTests_models_classes_QTI_Resource::isAllowed($type)){
	    		
	    		$id = (string)$resourceNode['identifier'];
	    		(isset($resourceNode['href'])) ? $href = (string)$resourceNode['href'] : $href = '';
	    		
	    		$auxFiles = array();
	    		$xmlFiles = array();
	    		foreach($resourceNode->file as $fileNode){
	    			$fileHref = (string)$fileNode['href'];
	    			if(preg_match("/\.xml$/", $fileHref)){
		    			if(empty($href)){
		    				$xmlFiles[] = $fileHref;
		    			}
	    			}
	    			else{
	    				$auxFiles[] = $fileHref;
	    			}
	    		}
	    		
	    		if(count($xmlFiles) == 1 && empty($href)){
	    			$href = $xmlFiles[0];
	    		}
	    		$resource = new taoTests_models_classes_QTI_Resource($id, $type, $href);
	    		$resource->setAuxiliaryFiles($auxFiles);
	    		
	    		$returnValue[] = $resource;
	    	}
	    }
        
        // section 127-0-1-1--24e482dc:12bc4041ca7:-8000:00000000000026E8 end

        return (array) $returnValue;
    }

} /* end of class taoTests_models_classes_QTI_ParserFactory */

?>