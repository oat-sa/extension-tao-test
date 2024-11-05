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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoTests\models\Copier;

use oat\generis\model\data\Ontology;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\tao\model\resources\Service\InstanceCopierProxy;
use oat\tao\model\TaoOntology;
use oat\oatbox\event\EventManager;
use oat\tao\model\resources\Service\ClassCopier;
use oat\tao\model\resources\Service\InstanceCopier;
use oat\tao\model\resources\Service\ClassCopierProxy;
use oat\tao\model\resources\Service\ClassMetadataCopier;
use oat\tao\model\resources\Service\ClassMetadataMapper;
use oat\tao\model\resources\Service\InstanceMetadataCopier;
use oat\tao\model\resources\Service\RootClassesListService;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use taoTests_models_classes_TestsService;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

/**
 * @codeCoverageIgnore
 */
class CopierServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(taoTests_models_classes_TestsService::class, taoTests_models_classes_TestsService::class)
            ->factory(taoTests_models_classes_TestsService::class . '::singleton');

        $services
            ->get(InstanceMetadataCopier::class)
            ->call(
                'addPropertyUrisToBlacklist',
                [
                    [
                        taoTests_models_classes_TestsService::PROPERTY_TEST_CONTENT,
                        OntologyRdfs::RDFS_LABEL,
                    ]
                ]
            );

        $services
            ->set(TestContentCopier::class, TestContentCopier::class)
            ->args(
                [
                    service(taoTests_models_classes_TestsService::class),
                    service(EventManager::SERVICE_ID),
                ]
            );

        $services
            ->set(InstanceCopier::class . '::TESTS', InstanceCopier::class)
            ->args(
                [
                    service(InstanceMetadataCopier::class),
                    service(Ontology::SERVICE_ID)
                ]
            )
            ->call(
                'withInstanceContentCopier',
                [
                    service(TestContentCopier::class),
                ]
            )
            ->call(
                'withPermissionCopiers',
                [
                    tagged_iterator('tao.copier.permissions'),
                ]
            )
            ->call(
                'withEventManager',
                [
                    service(EventManager::class),
                ]
            );

        $services
            ->set(ClassCopier::class . '::TESTS', ClassCopier::class)
            ->share(false)
            ->args(
                [
                    service(RootClassesListService::class),
                    service(ClassMetadataCopier::class),
                    service(InstanceCopier::class . '::TESTS'),
                    service(ClassMetadataMapper::class),
                    service(Ontology::SERVICE_ID),
                ]
            )
            ->call(
                'withPermissionCopiers',
                [
                    tagged_iterator('tao.copier.permissions'),
                ]
            );

        $services
            ->set(TestClassCopier::class, TestClassCopier::class)
            ->share(false)
            ->args(
                [
                    service(ClassCopier::class . '::TESTS'),
                    service(Ontology::SERVICE_ID),
                ]
            );

        $services
            ->get(ClassCopierProxy::class)
            ->call(
                'addClassCopier',
                [
                    TaoOntology::CLASS_URI_TEST,
                    service(TestClassCopier::class),
                ]
            );

        $services
            ->get(InstanceCopierProxy::class)
            ->call(
                'addInstanceCopier',
                [
                    TaoOntology::CLASS_URI_TEST,
                    service(InstanceCopier::class . '::TESTS'),
                ]
            );
    }
}
