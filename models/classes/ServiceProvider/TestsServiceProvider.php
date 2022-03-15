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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoTests\models\ServiceProvider;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\taoTests\models\Property\FeatureFlagExcludedPropertyMapper;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class TestsServiceProvider implements ContainerServiceProviderInterface
{
    public const PARAM_FEATURE_FLAG_FORM_FIELDS = 'featureFlagFormFields';
    public const PROPERTY_ASSESSMENT_PROJECT_ID = 'http://www.tao.lu/Ontologies/TAOTest.rdf#AssessmentProjectId';
    public const FEATURE_FLAG_REMOTE_PUBLISHING_FROM_TEST = 'FEATURE_FLAG_REMOTE_PUBLISHING_FROM_TEST';

    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();
        $parameters = $configurator->parameters();

        $parameters->set(
            self::PARAM_FEATURE_FLAG_FORM_FIELDS,
            [
                self::PROPERTY_ASSESSMENT_PROJECT_ID => [
                    self::FEATURE_FLAG_REMOTE_PUBLISHING_FROM_TEST
                ]
            ]
        );

        $services
            ->set(FeatureFlagExcludedPropertyMapper::class)
            ->public()
            ->args(
                [
                    param(self::PARAM_FEATURE_FLAG_FORM_FIELDS),
                    service(FeatureFlagChecker::class),
                ]
            );
    }
}
