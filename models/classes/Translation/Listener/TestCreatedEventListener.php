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

use oat\generis\model\data\Ontology;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
use oat\taoTests\models\event\TestCreatedEvent;
use Psr\Log\LoggerInterface;

class TestCreatedEventListener
{
    private FeatureFlagCheckerInterface $featureFlagChecker;
    private Ontology $ontology;
    private LoggerInterface $logger;

    public function __construct(
        FeatureFlagCheckerInterface $featureFlagChecker,
        Ontology $ontology,
        LoggerInterface $logger
    ) {
        $this->featureFlagChecker = $featureFlagChecker;
        $this->ontology = $ontology;
        $this->logger = $logger;
    }

    public function populateTranslationProperties(TestCreatedEvent $event): void
    {
        if (!$this->featureFlagChecker->isEnabled('FEATURE_TRANSLATION_ENABLED')) {
            return;
        }

        $translationTypeProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_TRANSLATION_TYPE);
        $test = $this->ontology->getResource($event->getTestUri());

        if ($test->getOnePropertyValue($translationTypeProperty) !== null) {
            $this->logger->info(
                sprintf(
                    'The property "%s" for the test "%s" has already been set.',
                    $translationTypeProperty->getUri(),
                    $test->getUri()
                )
            );

            return;
        }

        $test->setPropertyValue($translationTypeProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL);
    }
}
