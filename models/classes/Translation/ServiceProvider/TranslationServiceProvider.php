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

namespace oat\taoTests\models\Translation\ServiceProvider;

use oat\generis\model\data\Ontology;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerService;
use oat\tao\model\resources\Service\InstanceCopier;
use oat\tao\model\TaoOntology;
use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\tao\model\Translation\Form\Modifier\TranslationFormModifier as TaoTranslationFormModifier;
use oat\tao\model\Translation\Service\ResourceLanguageRetriever;
use oat\tao\model\Translation\Service\ResourceMetadataPopulateService;
use oat\tao\model\Translation\Service\TranslatedIntoLanguagesSynchronizer;
use oat\tao\model\Translation\Service\TranslationCreationService;
use oat\taoTests\models\TaoTestOntology;
use oat\taoTests\models\Translation\Form\Modifier\TranslationFormModifier;
use oat\taoTests\models\Translation\Form\Modifier\TranslationFormModifierProxy;
use oat\taoTests\models\Translation\Listener\TestCreatedEventListener;
use oat\taoTests\models\Translation\Service\TranslateIntoLanguagesHandler;
use oat\taoTests\models\Translation\Service\TranslationPostCreationService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @codeCoverageIgnore
 */
class TranslationServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->get(ResourceMetadataPopulateService::class)
            ->call(
                'addMetadata',
                [
                    TaoOntology::CLASS_URI_TEST,
                    TaoTestOntology::PROPERTY_TRANSLATION_COMPLETION,
                ]
            );

        $services
            ->set(TranslationFormModifier::class, TranslationFormModifier::class)
            ->args([
                service(FeatureFlagChecker::class),
                service(Ontology::SERVICE_ID),
            ]);

        $services
            ->set(TranslationFormModifierProxy::class, TranslationFormModifierProxy::class)
            ->call(
                'addModifier',
                [
                    service(TaoTranslationFormModifier::class),
                ]
            )
            ->call(
                'addModifier',
                [
                    service(TranslationFormModifier::class),
                ]
            )->public();

        $services
            ->set(TestCreatedEventListener::class, TestCreatedEventListener::class)
            ->public()
            ->args([
                service(FeatureFlagChecker::class),
                service(Ontology::SERVICE_ID),
                service(ResourceLanguageRetriever::class),
                service(LoggerService::SERVICE_ID),
            ]);

        $services
            ->set(TranslationPostCreationService::class, TranslationPostCreationService::class)
            ->args(
                [
                    service(LoggerService::SERVICE_ID),
                ]
            );

        $services
            ->set(TranslateIntoLanguagesHandler::class, TranslateIntoLanguagesHandler::class)
            ->args(
                [
                    service(LoggerService::SERVICE_ID),
                    service(EventManager::SERVICE_ID),
                ]
            );

        $services
            ->get(TranslationCreationService::class)
            ->call(
                'setResourceTransfer',
                [
                    TaoOntology::CLASS_URI_TEST,
                    service(InstanceCopier::class . '::TESTS')
                ]
            )
            ->call(
                'addPostCreation',
                [
                    TaoOntology::CLASS_URI_TEST,
                    service(TranslationPostCreationService::class)
                ]
            );

        $services
            ->get(TranslatedIntoLanguagesSynchronizer::class)
            ->call(
                'addCallback',
                [
                    TaoOntology::CLASS_URI_TEST,
                    service(TranslateIntoLanguagesHandler::class)
                ]
            );
    }
}
