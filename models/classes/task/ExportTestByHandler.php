<?php

namespace oat\taoTests\models\task;

use oat\oatbox\event\EventManager;
use oat\taoItems\model\task\ExportItemByHandler;
use oat\taoTests\models\event\TestExportEvent;

class ExportTestByHandler extends ExportItemByHandler
{
    /**
     * @param string $selectedResourceUri
     */
    protected function onExport($selectedResourceUri)
    {
        $this->getServiceLocator()
            ->get(EventManager::SERVICE_ID)
            ->trigger(new TestExportEvent($selectedResourceUri));
    }
}