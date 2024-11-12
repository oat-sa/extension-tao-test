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

namespace oat\taoTests\test\unit\models\classes\Translation\Listener;

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
use oat\tao\model\Translation\Service\ResourceLanguageRetriever;
use oat\taoTests\models\event\TestCreatedEvent;
use oat\taoTests\models\Translation\Listener\TestCreatedEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TestCreatedEventListenerTest extends TestCase
{
    /** @var TestCreatedEvent|MockObject */
    private TestCreatedEvent $testCreatedEvent;

    /** @var core_kernel_classes_Resource|MockObject */
    private core_kernel_classes_Resource $test;

    /** @var core_kernel_classes_Property|MockObject */
    private core_kernel_classes_Property $languageProperty;

    /** @var core_kernel_classes_Property|MockObject */
    private core_kernel_classes_Property $translationTypeProperty;

    /** @var core_kernel_classes_Property|MockObject */
    private core_kernel_classes_Property $translationStatusProperty;

    /** @var FeatureFlagCheckerInterface|MockObject */
    private FeatureFlagCheckerInterface $featureFlagChecker;

    /** @var Ontology|MockObject */
    private Ontology $ontology;

    /** @var ResourceLanguageRetriever|MockObject */
    private ResourceLanguageRetriever $resourceLanguageRetriever;

    /** @var LoggerInterface|MockObject */
    private LoggerInterface $logger;

    private TestCreatedEventListener $sut;

    protected function setUp(): void
    {
        $this->testCreatedEvent = $this->createMock(TestCreatedEvent::class);
        $this->test = $this->createMock(core_kernel_classes_Resource::class);
        $this->languageProperty = $this->createMock(core_kernel_classes_Property::class);
        $this->translationTypeProperty = $this->createMock(core_kernel_classes_Property::class);
        $this->translationStatusProperty = $this->createMock(core_kernel_classes_Property::class);

        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);
        $this->ontology = $this->createMock(Ontology::class);
        $this->resourceLanguageRetriever = $this->createMock(ResourceLanguageRetriever::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->sut = new TestCreatedEventListener(
            $this->featureFlagChecker,
            $this->ontology,
            $this->resourceLanguageRetriever,
            $this->logger
        );
    }

    public function testPopulateTranslationPropertiesTranslationDisabled(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_FLAG_TRANSLATION_ENABLED')
            ->willReturn(false);

        $this->ontology
            ->expects($this->never())
            ->method($this->anything());
        $this->logger
            ->expects($this->never())
            ->method($this->anything());
        $this->testCreatedEvent
            ->expects($this->never())
            ->method($this->anything());
        $this->test
            ->expects($this->never())
            ->method($this->anything());

        $this->sut->populateTranslationProperties($this->testCreatedEvent);
    }

    public function testPopulateTranslationProperties(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_FLAG_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->testCreatedEvent
            ->expects($this->once())
            ->method('getTestUri')
            ->willReturn('testUri');

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('testUri')
            ->willReturn($this->test);

        $this->ontology
            ->expects($this->exactly(3))
            ->method('getProperty')
            ->withConsecutive(
                [TaoOntology::PROPERTY_LANGUAGE],
                [TaoOntology::PROPERTY_TRANSLATION_TYPE],
                [TaoOntology::PROPERTY_TRANSLATION_STATUS],
            )
            ->willReturnOnConsecutiveCalls(
                $this->languageProperty,
                $this->translationTypeProperty,
                $this->translationStatusProperty,
            );

        $this->test
            ->expects($this->exactly(3))
            ->method('getOnePropertyValue')
            ->withConsecutive(
                [$this->languageProperty],
                [$this->translationTypeProperty],
                [$this->translationStatusProperty],
            )
            ->willReturnOnConsecutiveCalls(null, null, null);

        $this->logger
            ->expects($this->never())
            ->method('info');

        $this->resourceLanguageRetriever
            ->expects($this->once())
            ->method('retrieve')
            ->with($this->test)
            ->willReturn('en-US');

        $this->test
            ->expects($this->exactly(3))
            ->method('editPropertyValues')
            ->withConsecutive(
                [$this->languageProperty, TaoOntology::LANGUAGE_PREFIX . 'en-US'],
                [$this->translationTypeProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL],
                [$this->translationStatusProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_STATUS_NOT_READY],
            );

        $this->sut->populateTranslationProperties($this->testCreatedEvent);
    }

    public function testPopulateTranslationPropertiesValueSet(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_FLAG_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->testCreatedEvent
            ->expects($this->once())
            ->method('getTestUri')
            ->willReturn('testUri');

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('testUri')
            ->willReturn($this->test);

        $this->ontology
            ->expects($this->exactly(3))
            ->method('getProperty')
            ->withConsecutive(
                [TaoOntology::PROPERTY_LANGUAGE],
                [TaoOntology::PROPERTY_TRANSLATION_TYPE],
                [TaoOntology::PROPERTY_TRANSLATION_STATUS],
            )
            ->willReturnOnConsecutiveCalls(
                $this->languageProperty,
                $this->translationTypeProperty,
                $this->translationStatusProperty,
            );

        $this->test
            ->expects($this->exactly(3))
            ->method('getOnePropertyValue')
            ->withConsecutive(
                [$this->languageProperty],
                [$this->translationTypeProperty],
                [$this->translationStatusProperty],
            )
            ->willReturnOnConsecutiveCalls(
                $this->createMock(core_kernel_classes_Resource::class),
                $this->createMock(core_kernel_classes_Resource::class),
                $this->createMock(core_kernel_classes_Resource::class)
            );

        $this->logger
            ->expects($this->exactly(3))
            ->method('info');

        $this->resourceLanguageRetriever
            ->expects($this->once())
            ->method('retrieve')
            ->with($this->test)
            ->willReturn('en-US');

        $this->test
            ->expects($this->never())
            ->method('editPropertyValues');

        $this->sut->populateTranslationProperties($this->testCreatedEvent);
    }
}
