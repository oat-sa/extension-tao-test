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

namespace oat\taoTests\test\unit\models\classes\Translation\Service;

use core_kernel_classes_Resource;
use oat\taoTests\models\Translation\Service\TranslationPostCreationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TranslationPostCreationServiceTest extends TestCase
{
    private TranslationPostCreationService $service;

    /** @var LoggerInterface|MockObject */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new TranslationPostCreationService($this->logger);
    }

    public function testInvokeLogsMessage(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resourceUri = 'http://example.com/resource/1';
        $resource
            ->method('getUri')
            ->willReturn($resourceUri);

        $this->logger
            ->expects($this->once())
            ->method('debug');

        ($this->service)($resource);
    }
}
