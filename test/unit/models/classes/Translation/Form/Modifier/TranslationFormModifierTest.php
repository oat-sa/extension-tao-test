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

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
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

    /** @var Ontology|MockObject */
    private $ontology;

    private TranslationFormModifier $sut;

    protected function setUp(): void
    {
        $this->form = $this->createMock(tao_helpers_form_Form::class);

        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);
        $this->ontology = $this->createMock(Ontology::class);

        $this->sut = new TranslationFormModifier($this->featureFlagChecker, $this->ontology);
    }

    public function testModifyTranslationEnabledNoType(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_FLAG_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('getValue')
            ->with('uri')
            ->willReturn('instanceUri');

        $instance = $this->createMock(core_kernel_classes_Resource::class);

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('instanceUri')
            ->willReturn($instance);

        $property = $this->createMock(core_kernel_classes_Property::class);

        $this->ontology
            ->expects($this->once())
            ->method('getProperty')
            ->with(TaoOntology::PROPERTY_TRANSLATION_TYPE)
            ->willReturn($property);

        $instance
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($property)
            ->willReturn(null);

        $this->form
            ->expects($this->once())
            ->method('removeElement')
            ->with(tao_helpers_Uri::encode(TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION));

        $this->sut->modify($this->form);
    }

    /**
     * @dataProvider translationTypeDataProvider
     */
    public function testModifyTranslationEnabledWithType(string $type): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_FLAG_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('getValue')
            ->with('uri')
            ->willReturn('instanceUri');

        $instance = $this->createMock(core_kernel_classes_Resource::class);

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('instanceUri')
            ->willReturn($instance);

        $property = $this->createMock(core_kernel_classes_Property::class);

        $this->ontology
            ->expects($this->once())
            ->method('getProperty')
            ->with(TaoOntology::PROPERTY_TRANSLATION_TYPE)
            ->willReturn($property);

        $typeValue = $this->createMock(core_kernel_classes_Resource::class);

        $instance
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($property)
            ->willReturn($typeValue);

        $typeValue
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($type);

        $this->form
            ->expects(
                $type === TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL
                    ? $this->once()
                    : $this->never()
            )
            ->method('removeElement')
            ->with(tao_helpers_Uri::encode(TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION));

        $this->sut->modify($this->form);
    }

    public function testModifyTranslationDisabled(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_FLAG_TRANSLATION_ENABLED')
            ->willReturn(false);

        $this->form
            ->expects($this->once())
            ->method('removeElement')
            ->with(tao_helpers_Uri::encode(TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION));

        $this->ontology
            ->expects($this->never())
            ->method($this->anything());

        $this->sut->modify($this->form);
    }

    public function translationTypeDataProvider(): array
    {
        return [
            'Original' => [
                'type' => TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL,
            ],
            'Translation' => [
                'type' => TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_TRANSLATION,
            ],
        ];
    }
}
