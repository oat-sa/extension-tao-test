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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoTests\test\unit\models\classes\Copier;

use oat\oatbox\event\EventManager;
use oat\taoTests\models\Copier\TestContentCopier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use taoTests_models_classes_TestsService;

class TestContentCopierTest extends TestCase
{
    /** @var EventManager|MockObject */
    private $eventManager;
    /** @var MockObject|taoTests_models_classes_TestsService */
    private $testsService;
    private TestContentCopier $sut;

    public function setUp(): void
    {
        $this->testsService = $this->createMock(taoTests_models_classes_TestsService::class);
        $this->eventManager = $this->createMock(EventManager::class);
        $this->sut = new TestContentCopier($this->testsService, $this->eventManager);
    }

    public function testCopy(): void
    {
        //@TODO finish test
    }
}
