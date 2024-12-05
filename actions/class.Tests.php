<?php

// phpcs:disable Generic.Files.LineLength
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
// phpcs:enable

use oat\oatbox\event\EventManager;
use oat\tao\model\lock\LockManager;
use oat\oatbox\validator\ValidatorInterface;
use oat\tao\model\resources\ResourceWatcher;
use oat\tao\model\TaoOntology;
use oat\taoTests\models\event\TestUpdatedEvent;
use oat\tao\model\controller\SignedFormInstance;
use oat\tao\model\resources\Service\ClassDeleter;
use oat\tao\model\routing\AnnotationReader\security;
use oat\taoTests\models\Form\Modifier\FormModifierProxy;
use oat\taoTests\models\Translation\Form\Modifier\TranslationFormModifierProxy;
use tao_helpers_form_FormContainer as FormContainer;
use oat\generis\model\resource\Service\ResourceDeleter;
use oat\tao\model\resources\Contract\ClassDeleterInterface;
use oat\generis\model\resource\Contract\ResourceDeleterInterface;
use oat\generis\model\resource\exception\ResourceDeletionException;
use oat\tao\model\resources\Exception\PartialClassDeletionException;
use oat\tao\model\Lists\Business\Validation\DependsOnPropertyValidator;
use oat\tao\model\Translation\Service\TranslationSyncService;

/**
 * Tests Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
// phpcs:ignore
class taoTests_actions_Tests extends tao_actions_SaSModule
{
    /**
     * @return EventManager
     */
    protected function getEventManager()
    {
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }

    protected function getClassService()
    {
        return taoTests_models_classes_TestsService::singleton();
    }

    /**
     * constructor: initialize the service and the default data
     * @security("hide")
     */
    public function __construct()
    {
        parent::__construct();

        //the service is initialized by default
        $this->service = taoTests_models_classes_TestsService::singleton();
        $this->defaultData();
    }

    /*
    * controller actions
    */


    /**
     * edit a test instance
     * @requiresRight id READ
     */
    public function editTest()
    {
        $test = new core_kernel_classes_Resource($this->getRequestParameter('id'));

        $this->setData('isPreviewEnabled', $this->service->hasItems($test));

        if (!$this->isLocked($test)) {
            // my lock
            $lock = LockManager::getImplementation()->getLockData($test);
            $sessionIdentifier = common_session_SessionManager::getSession()->getUser()->getIdentifier();

            if (!is_null($lock) && $lock->getOwnerId() === $sessionIdentifier) {
                $this->setData('lockDate', $lock->getCreationTime());
                $this->setData('id', $lock->getResource()->getUri());
            }

            $clazz = $this->getCurrentClass();
            $formContainer = new SignedFormInstance(
                $clazz,
                $test,
                [
                    FormContainer::CSRF_PROTECTION_OPTION => true,
                    FormContainer::ATTRIBUTE_VALIDATORS => [
                        'data-depends-on-property' => [
                            $this->getDependsOnPropertyValidator(),
                        ],
                    ],
                    FormContainer::FORM_MODIFIERS => [
                        FormModifierProxy::class,
                        TranslationFormModifierProxy::class,
                    ],
                ]
            );
            $myForm = $formContainer->getForm();

            $myForm->setOptions([
                'resourceType' => TaoOntology::CLASS_URI_TEST
            ]);

            if ($myForm->isSubmited() && $myForm->isValid()) {
                $this->validateInstanceRoot($test->getUri());

                $propertyValues = $myForm->getValues();

                // don't hande the testmodel via bindProperties
                if (array_key_exists(taoTests_models_classes_TestsService::PROPERTY_TEST_TESTMODEL, $propertyValues)) {
                    $modelUri = $propertyValues[taoTests_models_classes_TestsService::PROPERTY_TEST_TESTMODEL];
                    unset($propertyValues[taoTests_models_classes_TestsService::PROPERTY_TEST_TESTMODEL]);
                    if (!empty($modelUri)) {
                        $testModel = new core_kernel_classes_Resource($modelUri);
                        $this->service->setTestModel($test, $testModel);
                    }
                } else {
                    common_Logger::w('No testmodel on test form', 'taoTests');
                }

                //then save the property values as usual
                $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($test);
                $test = $binder->bind($propertyValues);
                $this->getEventManager()->trigger(new TestUpdatedEvent($test->getUri(), $propertyValues));

                $this->setData('selectNode', tao_helpers_Uri::encode($test->getUri()));
                $this->setData('message', __('Test saved'));
                $this->setData('reload', true);
            }

            $myForm->removeElement(tao_helpers_Uri::encode(
                taoTests_models_classes_TestsService::PROPERTY_TEST_CONTENT
            ));
            $updatedAt = $this->getServiceLocator()->get(ResourceWatcher::SERVICE_ID)->getUpdatedAt($test);
            $this->setData('updatedAt', $updatedAt);
            $this->setData('uri', tao_helpers_Uri::encode($test->getUri()));
            $this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
            $this->setData('formTitle', __('Test properties'));
            $this->setData('myForm', $myForm->render());
            $this->setView('Tests/editTest.tpl');
        }
    }

    /**
     * delete a test or a test class. called via ajax
     *
     * @throws Exception
     * @throws common_exception_BadRequest
     * @requiresRight id WRITE
     */
    public function delete()
    {
        try {
            $this->validateCsrf();
        } catch (common_exception_Unauthorized $e) {
            $this->response = $this->getPsrResponse()->withStatus(403, __('Unable to process your request'));
            return;
        }
        if (!tao_helpers_Request::isAjax()) {
            throw new common_exception_BadRequest('wrong request mode');
        }

        $uri = $this->getRequestParameter('id');

        $this->validateInstanceRoot($uri);

        $instance = $this->getCurrentInstance('id');

        $lockManager = LockManager::getImplementation();
        $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();

        if ($lockManager->isLocked($instance)) {
            $lockManager->releaseLock($instance, $userId);
        }

        $label = $instance->getLabel();
        /**
         * @var ResourceDeleterInterface|ClassDeleterInterface $deleter
         * @var core_kernel_classes_Resource|core_kernel_classes_Class $instanceToDelete
         */
        [$deleter, $instanceToDelete] = $instance->isClass()
            ? [$this->getClassDeleter(), $this->getClass($instance)]
            : [$this->getResourceDeleter(), $instance];

        try {
            $deleter->delete($instanceToDelete);
            $success = true;
            $deleted = true;
            $message = __('%s has been deleted', $label);
        } catch (PartialClassDeletionException | ResourceDeletionException $exception) {
            $success = $exception instanceof PartialClassDeletionException;
            $deleted = false;
            $message = $exception->getUserMessage();
        }

        $this->returnJson([
            'success' => $success,
            'message' => $message,
            'deleted' => $deleted,
        ]);
    }

    /**
     * Redirect the test's authoring
     * @requiresRight id WRITE
     */
    public function authoring()
    {
        $test = new core_kernel_classes_Resource($this->getRequestParameter('id'));
        if (!$this->isLocked($test)) {
            $testModel = $this->service->getTestModel($test);
            $testModelImpl = $this->service->getTestModelImplementation($testModel);
            $authoringUrl = $testModelImpl->getAuthoringUrl($test);
            if (!empty($authoringUrl)) {
                $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();
                LockManager::getImplementation()->setLock($test, $userId);

                // Add support for the translation and the side-by-side authoring tool
                if ($this->getRequestParameter('translation') !== null) {
                    $authoringUrl = sprintf(
                        '%s&translation=%s',
                        $authoringUrl,
                        $this->getRequestParameter('translation')
                    );
                }
                if ($this->getRequestParameter('originResourceUri') !== null) {
                    $this->getTranslationSyncService()->syncById($this->getRequestParameter('originResourceUri'));
                    $authoringUrl = sprintf(
                        '%s&originResourceUri=%s',
                        $authoringUrl,
                        $this->getRequestParameter('originResourceUri')
                    );
                }

                return $this->forwardUrl($authoringUrl);
            }
            throw new common_exception_NoImplementation();
        }
    }

    /**
     * overwrite the parent moveInstance to add the requiresRight only in Tests
     * @see tao_actions_TaoModule::moveInstance()
     * @requiresRight uri WRITE
     * @requiresRight destinationClassUri WRITE
     */
    public function moveInstance()
    {
        parent::moveInstance();
    }

    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveAll()
     * @requiresRight ids WRITE
     */
    public function moveAll()
    {
        return parent::moveAll();
    }

    /**
     * overwrite the parent cloneInstance to add the requiresRight only in Tests
     * @see tao_actions_TaoModule::cloneInstance()
     * @requiresRight uri READ
     * @requiresRight classUri WRITE
     */
    public function cloneInstance()
    {
        return parent::cloneInstance();
    }

    /**
     * Delete all given resources
     *
     * @requiresRight ids WRITE
     *
     * @throws Exception
     */
    public function deleteAll()
    {
        return parent::deleteAll();
    }

    /**
     * Move class to another location
     * @requiresRight classUri WRITE
     */
    public function moveClass()
    {
        return parent::moveResource();
    }

    private function getDependsOnPropertyValidator(): ValidatorInterface
    {
        return $this->getPsrContainer()->get(DependsOnPropertyValidator::class);
    }

    private function getClassDeleter(): ClassDeleterInterface
    {
        return $this->getPsrContainer()->get(ClassDeleter::class);
    }

    private function getResourceDeleter(): ResourceDeleterInterface
    {
        return $this->getPsrContainer()->get(ResourceDeleter::class);
    }

    private function getTranslationSyncService(): TranslationSyncService
    {
        return $this->getPsrContainer()->get(TranslationSyncService::class);
    }
}
