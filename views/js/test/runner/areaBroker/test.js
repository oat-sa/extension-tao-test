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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */

/**
 * Test the areaBroker
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'taoTests/runner/areaBroker',
], function ($, areaBroker){
    'use strict';

    var fixture = '#qunit-fixture';


    QUnit.module('API');

    QUnit.test('module', function (assert){
        QUnit.expect(1);

        assert.equal(typeof areaBroker, 'function', "The module exposes a function");
    });

    QUnit.test('factory', function (assert){
        QUnit.expect(5);
        var $fixture = $(fixture);

        assert.throws(function(){
            areaBroker();
        }, TypeError, 'A provider create with a container');

        assert.throws(function(){
            areaBroker('foo');
        }, TypeError, 'A provider create with an existing container');

        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        assert.equal(typeof areaBroker($container), 'object', "The factory creates an object");
        assert.notEqual(areaBroker($container), areaBroker($container), "The factory creates new instances");
    });

    QUnit.test('broker api', function (assert){
        QUnit.expect(4);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var broker = areaBroker($container);
        assert.equal(typeof broker.defineAreas, 'function', 'The broker has a defineAreas function');
        assert.equal(typeof broker.getContainer, 'function', 'The broker has a getContainer function');
        assert.equal(typeof broker.getArea, 'function', 'The broker has a getArea function');
    });

    QUnit.module('Area mapping');

    QUnit.test('no mapping', function (assert){
        QUnit.expect(2);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var broker = areaBroker($container);

        assert.throws(function(){
            broker.getArea('foo');
        }, Error, 'The mapping is no yet defined');
    });

    QUnit.test('define mapping', function (assert){
        QUnit.expect(9);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);

        var broker = areaBroker($container);

        assert.throws(function(){
            broker.defineAreas();
        }, TypeError, 'requires a mapping object');

        assert.throws(function(){
            broker.defineAreas({});
        }, TypeError, 'required mapping missing');

        assert.throws(function(){
            broker.defineAreas({
                'content': $content,
                'navigation' : $navigation

            });
        }, TypeError, 'required mapping incomplete');

        broker.defineAreas({
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'panel'      : $panel
        });

        assert.deepEqual(broker.getArea('content'), $content, 'The area match');
        assert.deepEqual(broker.getArea('toolbox'), $toolbox, 'The area match');
        assert.deepEqual(broker.getArea('navigation'), $navigation, 'The area match');
        assert.deepEqual(broker.getArea('control'), $control, 'The area match');
        assert.deepEqual(broker.getArea('panel'), $panel, 'The area match');
    });

    QUnit.test('aliases', function (assert){
        QUnit.expect(6);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var $content    = $('.content', $container);
        var $toolbox    = $('.toolbox', $container);
        var $navigation = $('.navigation', $container);
        var $control    = $('.control', $container);
        var $panel      = $('.panel', $container);

        var broker = areaBroker($container);

        broker.defineAreas({
            'content'    : $content,
            'toolbox'    : $toolbox,
            'navigation' : $navigation,
            'control'    : $control,
            'panel'      : $panel
        });

        assert.deepEqual(broker.getContentArea(), $content, 'The area match');
        assert.deepEqual(broker.getToolboxArea(), $toolbox, 'The area match');
        assert.deepEqual(broker.getNavigationArea(), $navigation, 'The area match');
        assert.deepEqual(broker.getControlArea(), $control, 'The area match');
        assert.deepEqual(broker.getPanelArea(), $panel, 'The area match');
    });


    QUnit.module('container');

    QUnit.test('retrieve', function (assert){
        QUnit.expect(2);
        var $fixture = $(fixture);
        var $container = $('.test-runner', $fixture);

        assert.ok($container.length,  "The container exists");

        var broker = areaBroker($container);

        assert.deepEqual(broker.getContainer(), $container, 'The container match');
    });
});
