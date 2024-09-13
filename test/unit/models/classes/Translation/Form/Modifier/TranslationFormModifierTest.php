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

namespace oat\taoTests\test\unit\models\classes\Translation\Form\Modifier;

use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\taoTests\models\TaoTestOntology;
use oat\taoTests\models\Translation\Form\Modifier\TranslationFormModifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use tao_helpers_form_Form;
use tao_helpers_Uri;

class TranslationFormModifierTest extends TestCase
{
    /** @var tao_helpers_form_Form|MockObject */
    private tao_helpers_form_Form $form;

    /** @var FeatureFlagCheckerInterface|MockObject */
    private FeatureFlagCheckerInterface $featureFlagChecker;

    private TranslationFormModifier $sut;

    protected function setUp(): void
    {
        $this->form = $this->createMock(tao_helpers_form_Form::class);
        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);
        $this->sut = new TranslationFormModifier($this->featureFlagChecker);
    }

    public function testModifyTranslationEnabled(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->form
            ->expects($this->never())
            ->method('removeElement');

        $this->sut->modify($this->form);
    }

    public function testModifyTranslationDisabled(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_TRANSLATION_ENABLED')
            ->willReturn(false);

        $this->form
            ->expects($this->once())
            ->method('removeElement')
            ->with(tao_helpers_Uri::encode(TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION));

        $this->sut->modify($this->form);
    }
}
