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
 * Copyright (c) 2017-2018 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'core/promise',
    'taoTests/runner/runner',
    'taoTests/runner/runnerComponent'
], function ($, _, Promise, runnerFactory, runnerComponentFactory) {
    'use strict';

    QUnit.module('factory');


    QUnit.test('module', function (assert) {
        var $container = $('#fixture-module');

        QUnit.expect(3);

        assert.equal(typeof runnerComponentFactory, 'function', "The runnerComponent module exposes a function");
        assert.equal(typeof runnerComponentFactory($container, {provider: 'mock'}), 'object', "The runnerComponent factory produces an object");
        assert.notStrictEqual(runnerComponentFactory($container, {provider: 'mock'}), runnerComponentFactory($container, {provider: 'mock'}), "The runnerComponent factory provides a different object on each call");
    });


    QUnit.cases([
        {title: 'init'},
        {title: 'destroy'},
        {title: 'show'},
        {title: 'hide'},
        {title: 'getOption'},
        {title: 'getRunner'},
        {title: 'on'},
        {title: 'before'},
        {title: 'after'},
        {title: 'trigger'}
    ]).test('api ', function (data, assert) {
        var $container = $('#fixture-method');
        var instance = runnerComponentFactory($container, {provider: 'mock'});

        QUnit.expect(1);

        assert.equal(typeof instance[data.title], 'function', 'The runnerComponent instance exposes a "' + data.title + '" function');
    });


    QUnit.module('provider', {
        teardown: function () {
            runnerFactory.clearProviders();
        }
    });


    QUnit.asyncTest('init', function (assert) {
        var $container = $('#fixture-init');
        var instance;

        function mockTpl() {
            assert.ok(true, 'The provided template is used');
            return '<div id="foo-runner"></div>';
        }

        QUnit.expect(10);

        runnerFactory.registerProvider('mock-init', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            },
            destroy: function () {
                assert.ok(true, 'The destroy method has been called');

                _.delay(function () {
                    assert.equal($container.children().length, 0, 'The component has been destroyed');

                    QUnit.start();
                }, 200);
            }
        });

        assert.equal($container.children().length, 0, 'The runner is not rendered');

        instance = runnerComponentFactory($container, {provider: 'mock-init'}, mockTpl).on('ready', function () {
            assert.ok(true, 'The runner is ready');
            assert.equal($container.children().length, 1, 'The runner is rendered');
            assert.equal($container.find('#foo-runner').length, 1, 'The right template is used');

            instance.destroy();
        });
        assert.equal(instance.getOption('provider'), 'mock-init', 'The right provider is set in the config');
    });


    QUnit.test('init error', function (assert) {
        QUnit.expect(1);

        assert.throws(function () {
            runnerComponentFactory();
        }, 'An error should be thrown if the provider is not set');
    });


    QUnit.asyncTest('dynamic providers', function (assert) {
        var $container = $("#fixture-providers");

        QUnit.expect(3);
        QUnit.stop(1);

        runnerComponentFactory($container, {
            provider: 'mock',
            providers: [{
                module: 'taoTests/test/runner/runnerComponent/mockProvider',
                bundle: 'taoTests/test/runner/runnerComponent/mockBundle.min',
                category: 'mock'
            }]
        })
            .on('ready', function (runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                runner.on('mock-provider-loaded', function () {
                    assert.ok(true, 'The right provider has been loaded');
                    QUnit.start();
                });

                QUnit.start();
            });
    });


    QUnit.asyncTest('dynamic plugins', function (assert) {
        var $container = $("#fixture-plugins");

        QUnit.expect(5);
        QUnit.stop(1);

        runnerFactory.registerProvider('bar', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            }
        });

        runnerComponentFactory($container, {
            provider: 'bar',
            plugins: [{
                module: 'taoTests/test/runner/runnerComponent/mockPlugin',
                bundle: 'taoTests/test/runner/runnerComponent/mockBundle.min',
                category: 'mock'
            }]
        })
            .on('ready', function (runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                runner.on('plugin-loaded.mock', function () {
                    assert.ok(true, 'The right plugin has been loaded');
                    QUnit.start();
                });

                QUnit.start();
            });
    });


    QUnit.asyncTest('error event', function (assert) {
        var $container = $("#fixture-error");
        var error = 'oops!';

        QUnit.expect(5);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            }
        });

        runnerComponentFactory($container, {provider: 'foo'})
            .on('error', function (err) {
                assert.equal(err, error, 'The error has been forwarded');
            })
            .on('ready', function (runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');

                runner.trigger('error', error);

                QUnit.start();
            });
    });


    QUnit.asyncTest('getRunner', function (assert) {
        var $container = $("#fixture-get");
        var instance;

        QUnit.expect(7);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            }
        });

        instance = runnerComponentFactory($container, {provider: 'foo'})
            .on('ready', function (runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');

                assert.equal(runner, instance.getRunner(), 'The runner is reachable');
                instance.destroy();
            })
            .on('destroy', function() {
                assert.equal(instance.getRunner(), null, 'The runner has been destroyed');
                QUnit.start();
            });

        assert.equal(instance.getRunner(), null, 'The runner is not ready at this time');
    });
});
