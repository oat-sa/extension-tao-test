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

use core_kernel_classes_Class;
use InvalidArgumentException;
use oat\generis\model\data\Ontology;
use oat\generis\test\MockObject;
use oat\tao\model\resources\Command\ResourceTransferCommand;
use oat\tao\model\resources\Contract\ResourceTransferInterface;
use oat\tao\model\resources\ResourceTransferResult;
use oat\taoTests\models\Copier\TestClassCopier;
use PHPUnit\Framework\TestCase;

class TestClassCopierTest extends TestCase
{
    /** @var ResourceTransferInterface|MockObject */
    private $classCopier;
    /** @var Ontology|MockObject */
    private $ontology;
    private TestClassCopier $sut;

    public function setUp(): void
    {
        $this->classCopier = $this->createMock(ResourceTransferInterface::class);
        $this->ontology = $this->createMock(Ontology::class);
        $this->sut = new TestClassCopier($this->classCopier, $this->ontology);
    }

    public function testTransfer(): void
    {
        $result = new ResourceTransferResult('newClassUri');
        $command = new ResourceTransferCommand(
            'fromClassUri',
            'toClassUri',
            ResourceTransferCommand::ACL_KEEP_ORIGINAL,
            ResourceTransferCommand::TRANSFER_MODE_COPY
        );

        $rootClass = $this->createMock(core_kernel_classes_Class::class);
        $fromClass = $this->createMock(core_kernel_classes_Class::class);

        $fromClass->method('equals')
            ->with($rootClass)
            ->willReturn(true);

        $fromClass->method('getClass')
            ->willReturn($rootClass);

        $this->ontology->method('getClass')
            ->willReturn($fromClass);

        $this->classCopier->method('transfer')
            ->with($command)
            ->willReturn($result);

        $this->assertSame($result, $this->sut->transfer($command));
    }

    public function testTransferWithInvalidClass(): void
    {
        $rootClass = $this->createMock(core_kernel_classes_Class::class);
        $fromClass = $this->createMock(core_kernel_classes_Class::class);

        $fromClass->method('equals')
            ->with($rootClass)
            ->willReturn(false);

        $fromClass->method('getUri')
            ->willReturn('fromClassUri');

        $fromClass->method('isSubClassOf')
            ->with($rootClass)
            ->willReturn(false);

        $fromClass->method('getClass')
            ->willReturn($rootClass);

        $this->ontology->method('getClass')
            ->willReturn($fromClass);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Selected class (fromClassUri) is not supported because it is not part of the root class ' .
            '(http://www.tao.lu/Ontologies/TAOTest.rdf#Test)'
        );

        $this->sut->transfer(
            new ResourceTransferCommand(
                'fromClassUri',
                'toClassUri',
                ResourceTransferCommand::ACL_KEEP_ORIGINAL,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );
    }
}
