<?php
/*
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-     (update and modification) Open Assessment Technologies SA;
 */
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */

use oat\tao\model\user\TaoRoles;
use oat\taoTests\scripts\update\Updater;
use oat\taoTests\scripts\install\RegisterFrontendPaths;
use oat\taoTests\scripts\install\RegisterTestProviderService;
use oat\taoTests\scripts\install\RegisterTestPluginService;

$extPath = __DIR__ . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoTests',
    'label' => 'Test core extension',
    'description' => 'TAO Tests extension contains the abstraction of the test-runners, but requires an implementation in order to be able to run tests',
    'license' => 'GPL-2.0',
    'version' => '14.3.0',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => [
        'generis' => '>=12.15.0',
        'taoItems' => '>=6.0.0',
        'taoBackOffice' => '>=3.0.0',
        'tao' => '>=38.5.0'
    ],
    'models' => [
        'http://www.tao.lu/Ontologies/TAOTest.rdf',
    ],
    'install' => [
        'rdf' => [
            __DIR__ . '/models/ontology/taotest.rdf'
        ],
            'php' => [
                RegisterTestPluginService::class,
                RegisterTestProviderService::class,
                RegisterFrontendPaths::class
            ]
    ],
    'update' => Updater::class,
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestsManagerRole',
    'acl' => [
        ['grant', 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestsManagerRole', ['ext' => 'taoTests']],
        ['grant', TaoRoles::REST_PUBLISHER, ['ext' => 'taoTests', 'mod' => 'RestTests']],
    ],
    'optimizableClasses' => [
        'http://www.tao.lu/Ontologies/TAOTest.rdf#Test'
    ],
    'constants' => [
        # actions directory
        "DIR_ACTIONS"           => $extPath . "actions" . DIRECTORY_SEPARATOR,

        # views directory
        "DIR_VIEWS"             => $extPath . "views" . DIRECTORY_SEPARATOR,

        # default module name
        'DEFAULT_MODULE_NAME'   => 'Tests',

        #default action name
        'DEFAULT_ACTION_NAME'   => 'index',

        #BASE PATH: the root path in the file system (usually the document root)
        'BASE_PATH'             => $extPath,

        #BASE URL (usually the domain root)
        'BASE_URL'              => ROOT_URL . 'taoTests/',
    ]
];
