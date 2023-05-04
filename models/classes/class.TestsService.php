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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2021 (original work) Open Assessment Technologies SA;
 */

use oat\generis\model\OntologyRdf;
use oat\tao\model\resources\Service\InstanceCopier;
use oat\tao\model\TaoOntology;
use oat\taoTests\models\event\TestCreatedEvent;
use oat\taoTests\models\event\TestDuplicatedEvent;
use oat\taoTests\models\event\TestRemovedEvent;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\tao\model\service\ServiceFileStorage;
use oat\taoTests\models\TestModel;
use oat\taoTests\models\MissingTestmodelException;
use oat\tao\model\OntologyClassService;

/**
 * Service methods to manage the Tests business models using the RDF API.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
class taoTests_models_classes_TestsService extends OntologyClassService
{
    public const CLASS_TEST_MODEL = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestModel';

    // phpcs:ignore
    public const PROPERTY_TEST_MODEL_IMPLEMENTATION = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestModelImplementation';

    public const PROPERTY_TEST_TESTMODEL = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestTestModel';

    /** @deprecated  self::PROPERTY_TEST_CONTENT should be used */
    public const TEST_TESTCONTENT_PROP = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent';

    public const PROPERTY_TEST_CONTENT = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent';

    /**
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function deleteTest(core_kernel_classes_Resource $test): bool
    {
        $returnValue = false;
        if (!is_null($test)) {
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

    public function deleteResource(core_kernel_classes_Resource $resource): bool
    {
        return $this->deleteTest($resource);
    }

    /**
     * @author Joel Bout, <joel@taotesting.com>
     */
    public function getRootClass(): core_kernel_classes_Class
    {
        return $this->getClass(TaoOntology::CLASS_URI_TEST);
    }

    /**
     * Check if the Class in parameter is a subclass of Test
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function isTestClass(core_kernel_classes_Class $clazz): bool
    {
        if ($clazz->getUri() == $this->getClass(TaoOntology::CLASS_URI_TEST)->getUri()) {
            return true;
        }

        foreach ($this->getClass(TaoOntology::CLASS_URI_TEST)->getSubClasses(true) as $subclass) {
            if ($clazz->getUri() == $subclass->getUri()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated use $this->deleteClass instead
     */
    public function deleteTestClass(core_kernel_classes_Class $clazz): bool
    {
        return $this->deleteClass($clazz);
    }

    /**
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function getAllItems(): array
    {
        $returnValue = [];

        $itemClazz = $this->getClass(TaoOntology::CLASS_URI_ITEM);
        foreach ($itemClazz->getInstances(true) as $instance) {
            $returnValue[$instance->getUri()] = $instance->getLabel();
        }

        return $returnValue;
    }

    /**
     * Used to be called whenever the label of the Test changed
     * Deprecated in favor of eventmanager
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     */
    public function onChangeTestLabel(core_kernel_classes_Resource $test = null)
    {
        common_Logger::w('Call to deprecated ' . __FUNCTION__);
        return false;
    }

    /**
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated Use service id 'oat\tao\model\resources\Service\InstanceCopier::TESTS'
     */
    public function cloneInstance(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Class $clazz = null
    ): ?core_kernel_classes_Resource {
        $returnValue = null;

        //call the parent create instance to prevent useless process test to be created:
        $label = $instance->getLabel();
        $cloneLabel = "$label bis";
        $clone = parent::createInstance($clazz, $cloneLabel);

        if (!is_null($clone)) {
            $noCloningProperties = [
                self::PROPERTY_TEST_CONTENT,
                OntologyRdf::RDF_TYPE
            ];

            foreach ($clazz->getProperties(true) as $property) {
                if (!in_array($property->getUri(), $noCloningProperties)) {
                    //allow clone of every property value but the deliverycontent, which is a process:
                    foreach ($instance->getPropertyValues($property) as $propertyValue) {
                        $clone->setPropertyValue($property, $propertyValue);
                    }
                }
            }
            //Fix label
            if (preg_match("/bis/", $label)) {
                $cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
                $cloneNumber++;
                $cloneLabel = preg_replace("/bis(.?)*$/", "", $label) . "bis $cloneNumber" ;
            }
            $clone->setLabel($cloneLabel);

            $this->cloneContent($instance, $clone);
            $this->getEventManager()->trigger(new TestDuplicatedEvent($instance->getUri(), $clone->getUri()));

            $returnValue = $clone;
        }

        return $returnValue;
    }

    public function cloneContent(core_kernel_classes_Resource $original, core_kernel_classes_Resource $clone): void
    {
        $impl = $this->getTestModelImplementation($this->getTestModel($original));
        $impl->cloneContent($original, $clone);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    protected function setDefaultModel(core_kernel_classes_Resource $test)
    {
        $testModelClass = $this->getClass(self::CLASS_TEST_MODEL);
        $models = $testModelClass->getInstances();
        if (count($models) > 0) {
            $this->setTestModel($test, current($models));
        }
    }

    /**
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function createInstance(core_kernel_classes_Class $clazz, $label = ''): core_kernel_classes_Resource
    {
        $test = parent::createInstance($clazz, $label);
        $this->setDefaultModel($test);

        $this->getEventManager()->trigger(new TestCreatedEvent($test->getUri()));

        return $test;
    }

    /**
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function getTestItems(core_kernel_classes_Resource $test): array
    {
        try {
            $model = $this->getTestModel($test);
            $returnValue = $this->getTestModelImplementation($model)->getItems($test);
        } catch (MissingTestmodelException $e) {
            $returnValue = [];
        }
        return $returnValue;
    }

    /**
     * Changes the model of the test, while trying
     * to carry over the items of the test
     */
    public function setTestModel(core_kernel_classes_Resource $test, core_kernel_classes_Resource $testModel): void
    {
        $current = $test->getOnePropertyValue($this->getProperty(self::PROPERTY_TEST_TESTMODEL));
        // did the model change?
        if (is_null($current) || !$current->equals($testModel)) {
            $items = [];
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
     */
    public function getCompiler(
        core_kernel_classes_Resource $test,
        ServiceFileStorage $storage
    ): tao_models_classes_Compiler {
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
     * @deprecated $this->getCompiler should be used
     */
    public function getCompilerClass(core_kernel_classes_Resource $test): string
    {
        $testModel = $this->getTestModel($test);
        return $this->getTestModelImplementation($testModel)->getCompilerClass();
    }

    /**
     * Returns the model of the current test
     *
     * @throws MissingTestmodelException
     */
    public function getTestModel(core_kernel_classes_Resource $test): core_kernel_classes_Resource
    {
        $testModel = $test->getOnePropertyValue($this->getPropertyByUri(self::PROPERTY_TEST_TESTMODEL));

        if (is_null($testModel)) {
            throw new MissingTestmodelException('Undefined testmodel for test ' . $test->getUri());
        }

        return $testModel;
    }

    /**
     * Returns the implementation of an items test model
     */
    public function getTestModelImplementation(
        core_kernel_classes_Resource $testModel
    ): taoTests_models_classes_TestModel {
        $serviceId = (string) $testModel->getOnePropertyValue(
            $this->getPropertyByUri(self::PROPERTY_TEST_MODEL_IMPLEMENTATION)
        );

        if (empty($serviceId)) {
            throw new common_exception_NoImplementation(
                'No implementation found for testmodel ' . $testModel->getUri()
            );
        }

        try {
            $testModelService = $this->getServiceManager()->get($serviceId);
        } catch (\oat\oatbox\service\ServiceNotFoundException $e) {
            if (!class_exists($serviceId)) {
                throw new common_exception_Error('Test model service ' . $serviceId . ' not found');
            }
            // for backward compatibility support classname instead of a serviceid
            common_Logger::w('Outdated model definition "' . $serviceId . '", please use test model service');
            $testModelService = new $serviceId();
        }

        if (!$testModelService instanceof \taoTests_models_classes_TestModel) {
            throw new common_exception_Error(
                sprintf(
                    'Test model service %s not compatible for test model %s',
                    get_class($testModelService),
                    $testModel->getUri()
                )
            );
        }

        return $testModelService;
    }

    /**
     * @deprecated $this->getProperty should be used
     */
    public function getPropertyByUri(string $uri): core_kernel_classes_Property
    {
        return $this->getProperty($uri);
    }

    /**
     * Get serializer to persist filesystem object
     */
    protected function getFileReferenceSerializer(): FileReferenceSerializer
    {
        return $this->getServiceManager()->get(FileReferenceSerializer::SERVICE_ID);
    }

    public function hasItems(core_kernel_classes_Resource $test): bool
    {
        return !empty($this->getTestItems($test));
    }
}
