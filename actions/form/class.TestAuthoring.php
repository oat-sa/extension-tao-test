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
		$this->form->setActions(tao_helpers_form_FormFactory::getCommonActions('top'), 'top');
		
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
		
		$this->form->addElement(tao_helpers_form_FormFactory::getElement('uri', 'Hidden'));
		$this->form->addElement(tao_helpers_form_FormFactory::getElement('classUri', 'Hidden'));
		
		//duration 
		$durationElt = tao_helpers_form_FormFactory::getElement('duration', 'Textbox');
		$durationElt->setDescription(__('Maximum time allowed'));
		$this->form->addElement($durationElt);
		
		//password
		$passElt = tao_helpers_form_FormFactory::getElement('password', 'Hiddenbox');
		$passElt->setDescription(__('Password'));
		$this->form->addElement($passElt);
		
		$this->form->createGroup("g1", __("g1"), array('duration', 'password'));
		
		//sequence mode
		$sequenceElt = tao_helpers_form_FormFactory::getElement('hassequencemode', 'Combobox');
		$sequenceElt->setDescription(__('Sequence mode'));
		$sequenceElt->setOptions(array(
			'SEQUENCIAL'	=> __('Sequencial'),
			'RANDOM'		=> __('Random'),
			'MAXFISHER'		=> __('Maxfisher')
		));
		$this->form->addElement($sequenceElt);
		
		//delay
		$delayElt = tao_helpers_form_FormFactory::getElement('delay', 'Textbox');
		$delayElt->setDescription(__('Delay'));
		$delayElt->setAttributes(array('size' => 4));
		$this->form->addElement($delayElt);
		
		$this->form->createGroup("g2", __("g2"), array('hassequencemode', 'delay'));
		
		//scoring method
		$scoringElt = tao_helpers_form_FormFactory::getElement('hasscoringmethod', 'Combobox');
		$scoringElt->setDescription(__('Scoring method'));
		$scoringElt->setOptions(array(
			'CLASSICALRATIO'		=> __('Classical ratio'),
			'MAXIMUMLIKELIHOOD'		=> __('Maximum likelihood'),
			'MAXIMUMAPOSTERIORI'	=> __('Maximum a posteriori'),
			'EXPECTEDAPOSTERIORI'	=> __('Expected a posteriori')
		));
		$this->form->addElement($scoringElt);
		
		//QMIN
		$qminElt = tao_helpers_form_FormFactory::getElement('qmin', 'Textbox');
		$qminElt->setDescription(__('QMIN'));
		$qminElt->setAttributes(array('size' => 4));
		$this->form->addElement($qminElt);
		
		//QMAX
		$qmaxElt = tao_helpers_form_FormFactory::getElement('qmax', 'Textbox');
		$qmaxElt->setDescription(__('QMAX'));
		$qmaxElt->setAttributes(array('size' => 4));
		$this->form->addElement($qmaxElt);
		
		//QITER
		$qiterElt = tao_helpers_form_FormFactory::getElement('qiter', 'Textbox');
		$qiterElt->setDescription(__('QITER'));
		$qiterElt->setAttributes(array('size' => 4));
		$this->form->addElement($qiterElt);
		
		$this->form->createGroup("g3", __("g3"), array('hasscoringmethod', 'qmin', 'qmax', 'qiter'));
		
		//cumul model
		$cumulElt = tao_helpers_form_FormFactory::getElement('cumulmodel', 'Combobox');
		$cumulElt->setDescription(__('Cumul model'));
		$cumulElt->setOptions(array(
			'CLASSICAL'		=> __('Classical'),
			'LIKELIHOOD'		=> __('Likelihood'),
			'LOG-LIKELIHOOD'		=> __('Log-likelihood')
		));
		$this->form->addElement($cumulElt);
		
		$this->form->createGroup("g4", __("g4"), array('cumulmodel'));
		
		//Halt criteria
		$haltElt = tao_helpers_form_FormFactory::getElement('haltcriteria', 'Combobox');
		$haltElt->setDescription(__('Halt criteria'));
		$haltElt->setOptions(array(
			''		=> '',
			'DELTASCORE'	=> __('DELTASCORE'),
			'DELTASE'		=> __('DELTASE')
		));
		$this->form->addElement($haltElt);
		
		//threshold
		$thresholdElt = tao_helpers_form_FormFactory::getElement('deltascorethreshold', 'Textbox');
		$thresholdElt->setDescription(__('Threshold'));
		$thresholdElt->setAttributes(array('size' => 4));
		$this->form->addElement($thresholdElt);
		
		//max
		$maxElt = tao_helpers_form_FormFactory::getElement('max', 'Textbox');
		$maxElt->setDescription(__('Max'));
		$thresholdElt->setAttributes(array('size' => 4));
		$this->form->addElement($maxElt);
		
		$this->form->createGroup("g5", __("g5"), array('haltcriteria', 'deltascorethreshold', 'max'));
		
		
		//Threshold 1
		$thresh1Elt = tao_helpers_form_FormFactory::getElement('thresh1', 'Textbox');
		$thresh1Elt->setDescription(__('Threshold 1'));
		$this->form->addElement($thresh1Elt);
		
		//Threshold 2
		$thresh2Elt = tao_helpers_form_FormFactory::getElement('thresh2', 'Textbox');
		$thresh2Elt->setDescription(__('Threshold 2'));
		$this->form->addElement($thresh2Elt);
		
		//Threshold 3
		$thresh3Elt = tao_helpers_form_FormFactory::getElement('thresh3', 'Textbox');
		$thresh3Elt->setDescription(__('Threshold 3'));
		$this->form->addElement($thresh3Elt);
		
		$this->form->createGroup("g6", __("g6"), array( 'thresh1', 'thresh2','thresh3'));
		
		//display params
		$displayElt = tao_helpers_form_FormFactory::getElement('display', 'Checkbox');
		$displayElt->setDescription(__('Display'));
		$displayElt->setOptions(array(
			'showlistbox' => __('Display items list during the test'),
			'showprogessbar' => __('Show a progress bar during the test'),
			'showLabel' => __('Display label'),
			'showComment' => __('Display comments'),
			'deactivateback' => __('Deactivate back button')
		));
		$this->form->addElement($displayElt);
		
		//item top
		$itemTopElt = tao_helpers_form_FormFactory::getElement('itemtop', 'Textbox');
		$itemTopElt->setDescription(__('Test content (top position)'));
		$itemTopElt->setAttributes(array('size' => 4));
		$this->form->addElement($itemTopElt);
		
		//item left
		$itemLeftElt = tao_helpers_form_FormFactory::getElement('itemleft', 'Textbox');
		$itemLeftElt->setDescription(__('Test content (left position)'));
		$itemLeftElt->setAttributes(array('size' => 4));
		$this->form->addElement($itemLeftElt);
		
		//nav top
		$navTopElt = tao_helpers_form_FormFactory::getElement('navtop', 'Textbox');
		$navTopElt->setDescription(__('Navigation buttons (top position)'));
		$navTopElt->setAttributes(array('size' => 4));
		$this->form->addElement($navTopElt);
		
		//nav left
		$navLeftElt = tao_helpers_form_FormFactory::getElement('navleft', 'Textbox');
		$navLeftElt->setDescription(__('Navigation buttons (left position)'));
		$navLeftElt->setAttributes(array('size' => 4));
		$this->form->addElement($navLeftElt);
		
		//progress bar top
		$pbTopElt = tao_helpers_form_FormFactory::getElement('progressbartop', 'Textbox');
		$pbTopElt->setDescription(__('Progressbar (top position)'));
		$pbTopElt->setAttributes(array('size' => 4));
		$this->form->addElement($pbTopElt);
		
		//progress bar left
		$pbLeftElt = tao_helpers_form_FormFactory::getElement('progressbarleft', 'Textbox');
		$pbLeftElt->setDescription(__('Progressbar (left position)'));
		$pbLeftElt->setAttributes(array('size' => 4));
		$this->form->addElement($pbLeftElt);
		
		//left button image
		$btLeftElt = tao_helpers_form_FormFactory::getElement('urlleft', 'Textbox');
		$btLeftElt ->setDescription(__('Left button image'));
		$btLeftElt->setAttributes(array('class' => 'imageable', 'size' => 30));
		$this->form->addElement($btLeftElt );
		
		//right button image
		$btRightElt = tao_helpers_form_FormFactory::getElement('urlright', 'Textbox');
		$btRightElt->setDescription(__('Right button image'));
		$btRightElt->setAttributes(array('class' => 'imageable','size' => 30));
		$this->form->addElement($btRightElt);
		
		$this->form->createGroup("g7", __("Display"), array('display', 'itemtop', 'itemleft', 'navtop', 'navleft', 'progressbartop', 'progressbarleft', 'urlleft', 'urlright'));
		
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DED end
    }

} /* end of class taoTests_actions_form_TestAuthoring */

?>