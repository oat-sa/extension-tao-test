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
$extpath = __DIR__.DIRECTORY_SEPARATOR;

return array(
    'name' => 'taoTests',
    'label' => 'Test core extension',
    'description' => 'TAO Tests extension contains the abstraction of the test-runners, but requires an implementation in order to be able to run tests',
    'license' => 'GPL-2.0',
    'version' => '8.4.1',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => array(
        'generis' => '>=7.1.0',
        'taoItems' => '>=6.0.0',
        'taoBackOffice' => '>=3.0.0',
        'tao' => '>=22.14.6'
    ),
    'models' => array(
        'http://www.tao.lu/Ontologies/TAOTest.rdf',
    ),
	'install' => array(
		'rdf' => array(
            __DIR__. '/models/ontology/taotest.rdf'
		),
            'php' => [
                'oat\\taoTests\\scripts\\install\\RegisterTestPluginService',
                'oat\\taoTests\\scripts\\install\\RegisterTestProviderService'
            ]
	),
	'update' => "oat\\taoTests\\scripts\\update\\Updater",
	'managementRole' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestsManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestsManagerRole', array('ext'=>'taoTests')),
        array('grant', \oat\tao\model\user\TaoRoles::REST_PUBLISHER, array('ext'=>'taoTests', 'mod' => 'RestTests')),
    ),
	'optimizableClasses' => array(
		'http://www.tao.lu/Ontologies/TAOTest.rdf#Test'
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,

		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,

		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Tests',

		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',

		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,

		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoTests/',
	)
);
