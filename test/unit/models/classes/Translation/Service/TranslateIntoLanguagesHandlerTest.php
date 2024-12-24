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

namespace unit\models\classes\Translation\Service;

use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\taoTests\models\Translation\Service\TranslateIntoLanguagesHandler;
use oat\taoTests\models\event\TestUpdatedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TranslateIntoLanguagesHandlerTest extends TestCase
{
    private TranslateIntoLanguagesHandler $handler;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var EventManager|MockObject */
    private $eventManager;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventManager = $this->createMock(EventManager::class);

        $this->handler = new TranslateIntoLanguagesHandler($this->logger, $this->eventManager);
    }

    public function testInvoke(): void
    {
        $resourceUri = 'http://example.com/resource/1';
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource
            ->method('getUri')
            ->willReturn($resourceUri);

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with(new TestUpdatedEvent($resourceUri));

        $this->handler->__invoke($resource);
    }
}
