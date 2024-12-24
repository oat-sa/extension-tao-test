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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoTests\models\Translation\Service;

use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\taoTests\models\event\TestUpdatedEvent;
use Psr\Log\LoggerInterface;

class TranslateIntoLanguagesHandler
{
    private LoggerInterface $logger;
    private EventManager $eventManager;

    public function __construct(LoggerInterface $logger, EventManager $eventManager)
    {
        $this->logger = $logger;
        $this->eventManager = $eventManager;
    }

    public function __invoke(core_kernel_classes_Resource $originalTest): void
    {
        $this->logger->debug(sprintf('Force original test sync %s at %s', $originalTest->getUri(), __METHOD__));

        $this->eventManager->trigger(new TestUpdatedEvent($originalTest->getUri()));
    }
}
