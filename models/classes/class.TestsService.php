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
use oat\generis\model\OntologyRdf;
use oat\tao\model\TaoOntology;
use oat\taoTests\models\event\TestCreatedEvent;
use oat\taoTests\models\event\TestDuplicatedEvent;
use oat\taoTests\models\event\TestRemovedEvent;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\tao\model\service\ServiceFileStorage;
use oat\taoTests\models\TestModel;
use oat\taoTests\models\MissingTestmodelException;

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoTests
 
 */
class taoTests_models_classes_TestsService
    extends tao_models_classes_ClassService
{

    const CLASS_TEST_MODEL ='http://www.tao.lu/Ontologies/TAOTest.rdf#TestModel';

    const PROPERTY_TEST_MODEL_IMPLEMENTATION ='http://www.tao.lu/Ontologies/TAOTest.rdf#TestModelImplementation';

    const PROPERTY_TEST_TESTMODEL = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestTestModel';

    /** @deprecated  */
    const TEST_TESTCONTENT_PROP = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent';

    const PROPERTY_TEST_CONTENT = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent';
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level test class
     *
     * @access protected
     * @var core_kernel_classes_Class
     */
    protected $testClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    protected function __construct()
    {
		parent::__construct();
		$this->testClass = new core_kernel_classes_Class(TaoOntology::CLASS_URI_TEST );
    }

    /**
     * delete a test instance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource $test
     * @return boolean
     */
    public function deleteTest( core_kernel_classes_Resource $test)
    {
        $returnValue = (bool) false;
        if(!is_null($test)){
            try {
		        //delete the associated content
                $model = $this->getTestModel($test);
                $impl = $this->getTestModelImplementation($model);
                $impl->deleteContent($test);
            } catch (MissingTestmodelException $e) {
                // no content present, skip
            }
            $returnValue = $test->delete();
            $this->getEventManager()->trigger(new TestRemovedEvent($test->getUri()));
        }
        return (bool) $returnValue;
    }

    public function deleteResource(core_kernel_classes_Resource $resource)
    {
        return $this->deleteTest($resource);
    }

    /**
     * get the test class
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     */
    public function getRootclass()
    {
		return $this->testClass;
    }

    /**
     * Check if the Class in parameter is a subclass of Test
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;


		if($clazz->getUri() == $this->testClass->getUri()){
			$returnValue = true;
		}
		else{
			foreach($this->testClass->getSubClasses(true) as $subclass){
				if($clazz->getUri() == $subclass->getUri()){
					$returnValue = true;
					break;
				}
			}
		}


        return (bool) $returnValue;
    }

    /**
     * delete a test class or sublcass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteTestClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;


		if(!is_null($clazz)){
			if($this->isTestClass($clazz) && $clazz->getUri() != $this->testClass->getUri()){
				$returnValue = $clazz->delete();
			}
		}


        return (bool) $returnValue;
    }

    /**
     * Get all available items
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAllItems()
    {
        $returnValue = array();


		$itemClazz = new core_kernel_classes_Class(TaoOntology::CLASS_URI_ITEM);
		foreach($itemClazz->getInstances(true) as $instance){
			$returnValue[$instance->getUri()] = $instance->getLabel();
		}


        return (array) $returnValue;
    }

    /**
     * Used to be called whenever the label of the Test changed
     * Deprecated in favor of eventmanager
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return boolean
     * @deprecated
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test = null)
    {
        common_Logger::w('Call to deprecated '.__FUNCTION__);
        return false;
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource $instance
     * @param  core_kernel_classes_Class $clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;


		//call the parent create instance to prevent useless process test to be created:
		$label = $instance->getLabel();
		$cloneLabel = "$label bis";
		$clone = parent::createInstance($clazz, $cloneLabel);

		if(!is_null($clone)){
			$noCloningProperties = array(
				self::PROPERTY_TEST_CONTENT,
				OntologyRdf::RDF_TYPE
			);

			foreach($clazz->getProperties(true) as $property){

				if(!in_array($property->getUri(), $noCloningProperties)){
					//allow clone of every property value but the deliverycontent, which is a process:
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$clone->setPropertyValue($property, $propertyValue);
					}
				}
			}
			//Fix label
			if(preg_match("/bis/", $label)) {
				$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
				$cloneNumber++;
				$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)."bis $cloneNumber" ;
			}
			$clone->setLabel($cloneLabel);
			
			$impl = $this->getTestModelImplementation($this->getTestModel($instance));
			$impl->cloneContent($instance, $clone);

            $this->getEventManager()->trigger(new TestDuplicatedEvent($instance->getUri(), $clone->getUri()));

            $returnValue = $clone;
		}


        return $returnValue;
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param core_kernel_classes_Resource $test
     */
    protected function setDefaultModel($test)
    {
        $testModelClass = new core_kernel_classes_Class(self::CLASS_TEST_MODEL);
        $models = $testModelClass->getInstances();
        if (count($models) > 0) {
            $this->setTestModel($test, current($models));
        }
    }
    
    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Class $clazz
     * @param  string $label
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Class $clazz, $label = '')
    {
		$test = parent::createInstance($clazz, $label);
        $this->setDefaultModel($test);

        $this->getEventManager()->trigger(new TestCreatedEvent($test->getUri()));

        return $test;
    }

    /**
     * Short description of method getTestItems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource test
     * @return array
     */
    public function getTestItems( core_kernel_classes_Resource $test)
    {
        try {
            $model = $this->getTestModel($test);
            $returnValue = $this->getTestModelImplementation($model)->getItems($test);
        } catch (MissingTestmodelException $e) {
            $returnValue = array();
        }
        return (array) $returnValue;
    }
    
    /**
     * Changes the model of the test, while trying
     * to carry over the items of the test
     * 
     * @param core_kernel_classes_Resource $test
     * @param core_kernel_classes_Resource $testModel
     */
    public function setTestModel(core_kernel_classes_Resource $test, core_kernel_classes_Resource $testModel) {
        $current = $test->getOnePropertyValue($this->getProperty(self::PROPERTY_TEST_TESTMODEL));
		// did the model change?
		if (is_null($current) || !$current->equals($testModel)) {
			$items = array();
			if (!is_null($current)) {
				$former = $this->getTestModelImplementation($current);
				if (!empty($former)) {
					$items = $former->getItems($test);
					$former->deleteContent($test);
				}	
			}
			$test->editPropertyValues($this->getProperty(self::PROPERTY_TEST_TESTMODEL), $testModel);
			$newImpl = $this->getTestModelImplementation($testModel);
			if (!empty($newImpl)) {
				$newImpl->prepareContent($test, $items);
			}
		}
    }

    /**
     * Returns a compiler instance for a given test
     * @param core_kernel_classes_Resource $test
     * @param ServiceFileStorage $storage
     * @return tao_models_classes_Compiler
     */
    public function getCompiler(core_kernel_classes_Resource $test, ServiceFileStorage $storage) {
        $testModel = $this->getTestModelImplementation($this->getTestModel($test));
        if ($testModel instanceof TestModel) {
            $compiler = $testModel->getCompiler($test, $storage);
        } else {
            $testCompilerClass = $testModel->getCompilerClass();
            $compiler = new $testCompilerClass($test, $storage);
            $compiler->setServiceLocator($storage->getServiceLocator());
        }
        return $compiler;
    }

    /**
     * Returns the class of the compiler
     * @param core_kernel_classes_Resource $test
     * @return string
     * @deprecated
     */
    public function getCompilerClass(core_kernel_classes_Resource $test) {
        $testModel = $this->getTestModel($test);
        return $this->getTestModelImplementation($testModel)->getCompilerClass();
    }
    
    /**
     * Returns the model of the current test
     * 
     * @param core_kernel_classes_Resource $test
     * @return core_kernel_classes_Resource
     * @throws MissingTestmodelException::
     */
    public function getTestModel(core_kernel_classes_Resource $test) {
		$testModel = $test->getOnePropertyValue(new core_kernel_classes_Property(self::PROPERTY_TEST_TESTMODEL));
		if (is_null($testModel)) {
		    throw new MissingTestmodelException('Undefined testmodel for test '.$test->getUri());
		}
		return $testModel;
    }

    /**
     * Returns the implementation of an items test model
     * 
     * @param core_kernel_classes_Resource $test
     * @return taoTests_models_classes_TestModel
     */
    public function getTestModelImplementation(core_kernel_classes_Resource $testModel) {

		$serviceId = (string)$testModel->getOnePropertyValue(new core_kernel_classes_Property(self::PROPERTY_TEST_MODEL_IMPLEMENTATION));
		if (empty($serviceId)) {
			throw new common_exception_NoImplementation('No implementation found for testmodel '.$testModel->getUri());
		}
        try{
            $testModelService = $this->getServiceManager()->get($serviceId);
        } catch(\oat\oatbox\service\ServiceNotFoundException $e){
            if(!class_exists($serviceId)){
                throw new common_exception_Error('Test model service '.$serviceId.' not found');
            }
            // for backward compatibility support classname instead of a serviceid
            common_Logger::w('Outdated model definition "'.$serviceId.'", please use test model service');
            $testModelService = new $serviceId();

        }
		if (!$testModelService instanceof \taoTests_models_classes_TestModel) {
			throw new common_exception_Error('Test model service '.get_class($testModelService).' not compatible for test model '.$testModel->getUri());
		}
		return $testModelService;
    }

    /**
     * Get serializer to persist filesystem object
     *
     * @return FileReferenceSerializer
     */
    protected function getFileReferenceSerializer()
    {
        return $this->getServiceManager()->get(FileReferenceSerializer::SERVICE_ID);
    }
}