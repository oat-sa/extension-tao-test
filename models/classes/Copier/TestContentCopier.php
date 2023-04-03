<?php

/**
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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoTests\models\Copier;

use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\tao\model\resources\Contract\InstanceContentCopierInterface;
use taoTests_models_classes_TestsService;

class TestContentCopier implements InstanceContentCopierInterface
{
    private FileReferenceSerializer $fileReferenceSerializer;
    private taoTests_models_classes_TestsService $testsService;
    private EventManager $eventManager;

    public function __construct(
        FileReferenceSerializer $fileReferenceSerializer,
        taoTests_models_classes_TestsService $testsService,
        EventManager $eventManager
    ) {
        $this->fileReferenceSerializer = $fileReferenceSerializer;
        $this->testsService = $testsService;
        $this->eventManager = $eventManager;
    }

    public function copy(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Resource $destinationInstance
    ): void {
    }
}
