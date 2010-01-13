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
		
		//duration 
		$durationElt = tao_helpers_form_FormFactory::getElement('duration', 'Textbox');
		$durationElt->setDescription(__('Maximum time allowed'));
		$this->form->addElement($durationElt);
		
		//password
		$passElt = tao_helpers_form_FormFactory::getElement('password', 'Hiddenbox');
		$passElt->setDescription(__('Password'));
		$this->form->addElement($passElt);
		
		//display params
		$displayElt = tao_helpers_form_FormFactory::getElement('display', 'Checkbox');
		$displayElt->setDescription(__('Display'));
		$displayElt->setOptions(array(
			'showlistbox' => 'Display items list during the test',
			'showprogessbar' => 'Show a progress bar during the test',
			'showLabel' => 'Display label',
			'showComment' => 'Display comments'
		));
		$this->form->addElement($displayElt);
		
		//sequence mode
		$sequenceElt = tao_helpers_form_FormFactory::getElement('hassequencemode', 'Combobox');
		$sequenceElt->setDescription(__('Sequnce mode'));
		$sequenceElt->setOptions(array(
			'sequencial'	=> __('Sequencial'),
			'random'		=> __('Random'),
			'maxfisher'		=> __('Maxfisher')
		));
		$this->form->addElement($sequenceElt);
		
		//delay
		$delayElt = tao_helpers_form_FormFactory::getElement('delay', 'Textbox');
		$delayElt->setDescription(__('Delay'));
		$this->form->addElement($delayElt);
		
		//scoring method
		$scoringElt = tao_helpers_form_FormFactory::getElement('hasscoringmethod', 'Combobox');
		$scoringElt->setDescription(__('Sequence mode'));
		$scoringElt->setOptions(array(
			'CLASSICALRATIO'		=> __('Classical ratio'),
			'MAXIMUMLIKELIHOOD'		=> __('Maximum likelihood'),
			'MAXIMUMAPOSTERIORI'	=> __('Maximum a posteriori'),
			'EXPECTEDAPOSTERIORI'	=> __('Expected a posteriori')
		));
		$this->form->addElement($scoringElt);
		
		//cumul model
		$cumulElt = tao_helpers_form_FormFactory::getElement('cumulmodel', 'Combobox');
		$cumulElt->setDescription(__('Sequnce mode'));
		$cumulElt->setOptions(array(
			'CLASSICAL'		=> __('Classical'),
			'LIKELIHOOD'		=> __('Likelihood'),
			'LOG-LIKELIHOOD'		=> __('Log-likelihood')
		));
		$this->form->addElement($cumulElt);
		
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
		$this->form->addElement($thresholdElt);
		
		//max
		$maxElt = tao_helpers_form_FormFactory::getElement('max', 'Textbox');
		$maxElt->setDescription(__('Max'));
		$this->form->addElement($maxElt);
		
		$this->form->createGroup("parameters", "Test Parameters", array(
				'duration', 
				'password', 
				'display', 
				'hassequencemode',
				'delay',
				'hasscoringmethod',
				'cumulmodel',
				'haltcriteria',
				'deltascorethreshold',
				'max'
			)
		);
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DED end
    }

} /* end of class taoTests_actions_form_TestAuthoring */

?>