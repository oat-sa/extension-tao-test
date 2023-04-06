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

use oat\generis\model\data\Ontology;
use oat\generis\test\MockObject;
use oat\tao\model\resources\Contract\ResourceTransferInterface;
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
        //@TODO Finish test.
    }
}
