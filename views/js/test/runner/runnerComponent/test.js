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
], function($, _, Promise, runnerFactory, runnerComponentFactory) {
    'use strict';

    QUnit.module('factory');

    QUnit.test('module', function(assert) {
        var $container = $('#fixture-module');

        assert.expect(3);
        runnerFactory.registerProvider('mock', {
            loadAreaBroker: _.noop,
            init: function() {
            }
        });
        assert.equal(typeof runnerComponentFactory, 'function', 'The runnerComponent module exposes a function');
        assert.equal(typeof runnerComponentFactory($container, {provider: 'mock'}), 'object', 'The runnerComponent factory produces an object');
        assert.notStrictEqual(runnerComponentFactory($container, {provider: 'mock'}), runnerComponentFactory($container, {provider: 'mock'}), 'The runnerComponent factory provides a different object on each call');
    });

    QUnit.cases.init([
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
    ]).test('api ', function(data, assert) {
        var $container = $('#fixture-method');
        var instance = runnerComponentFactory($container, {provider: 'mock'});

        assert.expect(1);

        assert.equal(typeof instance[data.title], 'function', 'The runnerComponent instance exposes a "' + data.title + '" function');
    });

    QUnit.test('init', function(assert) {
        var ready = assert.async();
        var $container = $('#fixture-init');
        var instance;

        function mockTpl() {
            assert.ok(true, 'The provided template is used');
            return '<div id="foo-runner"></div>';
        }

        assert.expect(10);

        runnerFactory.registerProvider('mock-init', {
            loadAreaBroker: _.noop,
            init: function() {
                assert.ok(true, 'The init method has been called');
            },
            render: function() {
                assert.ok(true, 'The render method has been called');
            },
            destroy: function() {
                assert.ok(true, 'The destroy method has been called');

                _.delay(function() {
                    assert.equal($container.children().length, 0, 'The component has been destroyed');

                    ready();
                }, 200);
            }
        });

        assert.equal($container.children().length, 0, 'The runner is not rendered');

        instance = runnerComponentFactory($container, {provider: 'mock-init'}, mockTpl).on('ready', function() {
            assert.ok(true, 'The runner is ready');
            assert.equal($container.children().length, 1, 'The runner is rendered');
            assert.equal($container.find('#foo-runner').length, 1, 'The right template is used');

            instance.destroy();
        });
        assert.equal(instance.getOption('provider'), 'mock-init', 'The right provider is set in the config');
    });

    QUnit.test('init error', function(assert) {
        assert.expect(1);

        assert.throws(function() {
            runnerComponentFactory();
        }, 'An error should be thrown if the provider is not set');
    });

    QUnit.test('dynamic providers', function(assert) {
        var ready1 = assert.async();
        var $container = $('#fixture-providers');

        assert.expect(3);
        var ready = assert.async();

        runnerComponentFactory($container, {
            provider: 'mock',
            providers: [{
                module: 'taoTests/test/runner/runnerComponent/mockProvider',
                bundle: 'taoTests/test/runner/runnerComponent/mockBundle.min',
                category: 'mock'
            }]
        })
            .on('ready', function(runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                runner.on('mock-provider-loaded', function() {
                    assert.ok(true, 'The right provider has been loaded');
                    ready();

                });

                ready1();
            });
    });

    QUnit.test('dynamic plugins', function(assert) {
        var ready1 = assert.async();
        var $container = $('#fixture-plugins');

        var ready = assert.async();
        assert.expect(5);

        runnerFactory.registerProvider('bar', {
            loadAreaBroker: _.noop,
            init: function() {
                assert.ok(true, 'The init method has been called');
            },
            render: function() {
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
            .on('ready', function(runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                runner.on('plugin-loaded.mock', function() {
                    assert.ok(true, 'The right plugin has been loaded');
                    ready();
                });

                ready1();
            });
    });

    QUnit.test('error event', function(assert) {
        var ready = assert.async();
        var $container = $('#fixture-error');
        var error = 'oops!';

        assert.expect(5);
        runnerFactory.clearProviders();

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function() {
                assert.ok(true, 'The init method has been called');
            },
            render: function() {
                assert.ok(true, 'The render method has been called');
            }
        });

        runnerComponentFactory($container, {provider: 'foo'})
            .on('error', function(err) {
                assert.equal(err, error, 'The error has been forwarded');
            })
            .on('ready', function(runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');

                runner.trigger('error', error);
                runnerFactory.clearProviders();


                ready();
            });
    });

    QUnit.test('getRunner', function(assert) {
        var ready = assert.async();
        var $container = $('#fixture-get');
        var instance;

        assert.expect(7);
        runnerFactory.clearProviders();

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function() {
                assert.ok(true, 'The init method has been called');
            },
            render: function() {
                assert.ok(true, 'The render method has been called');
            }
        });

        instance = runnerComponentFactory($container, {provider: 'foo'})
            .on('ready', function(runner) {
                assert.ok(true, 'The runner is ready');
                assert.equal(typeof runner, 'object', 'The runner instance is provided');

                assert.equal(runner, instance.getRunner(), 'The runner is reachable');
                instance.destroy();
            })
            .on('destroy', function() {
                assert.equal(instance.getRunner(), null, 'The runner has been destroyed');
                ready();
            });

        assert.equal(instance.getRunner(), null, 'The runner is not ready at this time');
    });
});
