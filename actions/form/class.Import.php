<?php

error_reporting(E_ALL);

/**
 * TAO - taoTests/actions/form/class.Import.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.10.2010, 11:10:25 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoTests
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the import form.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('tao/actions/form/class.Import.php');

/* user defined includes */
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002659-includes begin
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002659-includes end

/* user defined constants */
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002659-constants begin
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002659-constants end

/**
 * Short description of class taoTests_actions_form_Import
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoTests
 * @subpackage actions_form
 */
class taoTests_actions_form_Import
    extends tao_actions_form_Import
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute formats
     *
     * @access protected
     * @var array
     */
    protected $formats = array('csv', 'qti');

    // --- OPERATIONS ---

    /**
     * Short description of method initQTIElements
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function initQTIElements()
    {
        // section 127-0-1-1--67198282:12bb0429ae8:-8000:0000000000002688 begin
        
    	$descElt = tao_helpers_form_FormFactory::getElement('qti_desc', 'Label');
		$descElt->setValue(__('A QTI-Package is a Zip archive containing a imsmanifest.xml file and the QTI resources to import'));
		$this->form->addElement($descElt);
    	
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add the source file"));
    	if(isset($_POST['import_sent_qti'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('application/zip', 'application/x-zip', 'application/x-zip-compressed', 'application/octet-stream'), 'extension' => array('zip'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000))
		));
    	
		$this->form->addElement($fileElt);
		$this->form->createGroup('file', __('Upload a QTI Package File'), array('qti_desc', 'source'));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    	
        // section 127-0-1-1--67198282:12bb0429ae8:-8000:0000000000002688 end
    }

} /* end of class taoTests_actions_form_Import */

?>