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

use oat\oatbox\event\EventManagerAwareTrait;
use oat\taoQtiTest\models\export\metadata\TestMetadataByClassExportHandler;
use oat\taoTaskQueue\model\QueueDispatcher;
use oat\taoTaskQueue\model\TaskLogActionTrait;
use oat\taoTests\models\event\TestExportEvent;
use oat\taoTests\models\task\ExportTestByHandler;

/**
 * This controller provide the actions to export tests
 *
 * @author  CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoTests
 */
class taoTests_actions_TestExport extends tao_actions_Export
{
    use TaskLogActionTrait;
    use EventManagerAwareTrait;

    /**
     * overwrite the parent index to add the requiresRight for Tests
     *
     * @requiresRight id READ
     * @see           tao_actions_Export::index()
     */
    public function index()
    {
        parent::index();
    }

    /**
     * @param tao_models_classes_export_ExportHandler $exporter
     * @param array                                   $exportData
     * @param core_kernel_classes_Resource            $selectedResource
     * @return mixed
     * @throws Exception
     */
    protected function handleSubmittedData(tao_models_classes_export_ExportHandler $exporter, array $exportData, core_kernel_classes_Resource $selectedResource)
    {
        // use task for only QTI export
        if ($exporter instanceof taoQtiTest_models_classes_export_TestExport || $exporter instanceof TestMetadataByClassExportHandler) {
            if (!\tao_helpers_Request::isAjax()) {
                //throw new \Exception('Only ajax call allowed.');
            }

            /** @var QueueDispatcher $queueDispatcher */
            $queueDispatcher = $this->getServiceLocator()->get(QueueDispatcher::SERVICE_ID);

            $task = $queueDispatcher->createTask(
                new ExportTestByHandler(),
                [
                    ExportTestByHandler::PARAM_EXPORT_HANDLER => get_class($exporter),
                    ExportTestByHandler::PARAM_EXPORT_DATA => $exportData,
                    ExportTestByHandler::PARAM_EXPORT_SELECTED_RESOURCE_URI => $selectedResource->getUri(),
                ],
                __('Export "%s" in "%s" format', $selectedResource->getLabel(), $exporter->getLabel())
            );

            return $this->returnTaskJson($task);
        } else {
            return parent::handleSubmittedData($exporter, $exportData, $selectedResource);
        }
    }

    protected function getAvailableExportHandlers()
    {
        $returnValue = parent::getAvailableExportHandlers();

        $resources = $this->getResourcesToExport();
        $testModels = [];
        foreach ($resources as $resource) {
            $model = taoTests_models_classes_TestsService::singleton()->getTestModel($resource);
            if (!is_null($model)) {
                $testModels[$model->getUri()] = $model;
            }
        }
        foreach ($testModels as $model) {
            $impl = taoTests_models_classes_TestsService::singleton()->getTestModelImplementation($model);
            if (in_array('tao_models_classes_export_ExportProvider', class_implements($impl))) {
                foreach ($impl->getExportHandlers() as $handler) {
                    array_unshift($returnValue, $handler);
                }
            }
        }

        return $returnValue;
    }

    /**
     * @param $file
     * @param \core_kernel_classes_Resource $selectedResource
     */
    protected function sendFileToClient($file, $selectedResource)
    {
        $this->getEventManager()->trigger(new TestExportEvent($selectedResource->getUri()));

        parent::sendFileToClient($file, $selectedResource);
    }


}