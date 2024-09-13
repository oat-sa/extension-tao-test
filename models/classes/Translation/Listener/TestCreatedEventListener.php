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

namespace oat\taoTests\models\Translation\Listener;

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\oatbox\user\UserLanguageService;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
use oat\taoTests\models\event\TestCreatedEvent;
use Psr\Log\LoggerInterface;

class TestCreatedEventListener
{
    private FeatureFlagCheckerInterface $featureFlagChecker;
    private Ontology $ontology;
    private UserLanguageService $userLanguageService;
    private LoggerInterface $logger;

    public function __construct(
        FeatureFlagCheckerInterface $featureFlagChecker,
        Ontology $ontology,
        UserLanguageService $userLanguageService,
        LoggerInterface $logger
    ) {
        $this->featureFlagChecker = $featureFlagChecker;
        $this->ontology = $ontology;
        $this->userLanguageService = $userLanguageService;
        $this->logger = $logger;
    }

    public function populateTranslationProperties(TestCreatedEvent $event): void
    {
        if (!$this->featureFlagChecker->isEnabled('FEATURE_TRANSLATION_ENABLED')) {
            return;
        }

        $test = $this->ontology->getResource($event->getTestUri());

        $this->setLanguage($test);
        $this->setTranslationType($test);
        $this->setTranslationStatus($test);
    }

    private function setLanguage(core_kernel_classes_Resource $test): void
    {
        $translationLanguageProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_LANGUAGE);

        if ($this->isPropertySet($test, $translationLanguageProperty)) {
            return;
        }

        $defaultLanguage = $this->userLanguageService->getDefaultLanguage();
        $test->setPropertyValue($translationLanguageProperty, TaoOntology::LANGUAGE_PREFIX . $defaultLanguage);
    }

    private function setTranslationType(core_kernel_classes_Resource $test): void
    {
        $translationTypeProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_TRANSLATION_TYPE);

        if ($this->isPropertySet($test, $translationTypeProperty)) {
            return;
        }

        $test->setPropertyValue($translationTypeProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL);
    }

    private function setTranslationStatus(core_kernel_classes_Resource $test): void
    {
        $translationStatusProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_TRANSLATION_STATUS);

        if ($this->isPropertySet($test, $translationStatusProperty)) {
            return;
        }

        $test->setPropertyValue($translationStatusProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_STATUS_NOT_READY);
    }

    private function isPropertySet(core_kernel_classes_Resource $test, core_kernel_classes_Property $property): bool
    {
        if ($test->getOnePropertyValue($property) === null) {
            return false;
        }

        $this->logger->info(
            sprintf(
                'The property "%s" for the test "%s" has already been set.',
                $property->getUri(),
                $test->getUri()
            )
        );

        return true;
    }
}
