<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoTests/models/classes/class.TestAuthoringService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.10.2012, 11:23:06 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTests
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfAuthoring_models_classes_ProcessService
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('wfAuthoring/models/classes/class.ProcessService.php');

/* user defined includes */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-includes begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-includes end

/* user defined constants */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-constants begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E3C-constants end

/**
 * Short description of class taoTests_models_classes_TestAuthoringService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTests
 * @subpackage models_classes
 */
class taoTests_models_classes_TestAuthoringService
    extends wfAuthoring_models_classes_ProcessService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the url of the TAO item runner service
     *
     * @access protected
     * @var string
     */
    protected $itemRunnerUrl = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getItemByActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getItemByActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E43 begin
		if(!empty($activity)){
			
			//check all interactive services:
			foreach ($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->getIterator() as $iService){
				if($iService instanceof core_kernel_classes_Resource){
					
					$serviceDefinition = $iService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
					
					//if service definition has the url of the service item runner
					$itemRunnerServiceDefinition = wfEngine_helpers_ProcessUtil::getServiceDefinition($this->itemRunnerUrl);
					
					if(!is_null($itemRunnerServiceDefinition)){
						if($serviceDefinition->getUri() == $itemRunnerServiceDefinition->getUri()){
					
							foreach($iService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN))->getIterator() as $actualParam){
								
								$formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
								if($formalParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)) == 'itemUri'){
									$item = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
									if(!is_null($item)){
										$returnValue = $item;
										break(2);
									}
								}
							}
							
						}
					}
						
				}
				
			}
			
		}
		
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E43 end

        return $returnValue;
    }

    /**
     * Short description of method getItemRunnerUrl
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getItemRunnerUrl()
    {
        $returnValue = (string) '';

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E46 begin
		$returnValue = $this->itemRunnerUrl;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E46 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getTestFromProcess
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return core_kernel_classes_Resource
     */
    public function getTestFromProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E48 begin
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$tests = $testClass->searchInstances(array(TEST_TESTCONTENT_PROP => $process->getUri()), array('like'=>false, 'recursive' => 1000));
		if(!empty($tests)){
			$returnValue = $tests[0];
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000002E48 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function __construct()
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FA9 begin
		parent::__construct();
		$service = new core_kernel_classes_Resource(INSTANCE_SERVICEDEFINITION_ITEMRUNNER);
		$this->itemRunnerUrl = (string)$service->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FA9 end
    }

} /* end of class taoTests_models_classes_TestAuthoringService */

?>