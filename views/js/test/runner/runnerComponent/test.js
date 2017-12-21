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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA ;
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
        QUnit.expect(3);
        assert.equal(typeof runnerComponentFactory, 'function', "The runnerComponent module exposes a function");
        assert.equal(typeof runnerComponentFactory({provider: 'mock'}), 'object', "The runnerComponent factory produces an object");
        assert.notStrictEqual(runnerComponentFactory({provider: 'mock'}), runnerComponentFactory({provider: 'mock'}), "The runnerComponent factory provides a different object on each call");
    });


    QUnit.cases([
        {title: 'init'},
        {title: 'render'},
        {title: 'destroy'},
        {title: 'show'},
        {title: 'hide'},
        {title: 'hasOption'},
        {title: 'getOption'},
        {title: 'getConfig'},
        {title: 'getRunner'},
        {title: 'on'},
        {title: 'before'},
        {title: 'after'},
        {title: 'trigger'}
    ]).test('api ', function (data, assert) {
        var instance = runnerComponentFactory({provider: 'mock'});
        QUnit.expect(1);
        assert.equal(typeof instance[data.title], 'function', 'The runnerComponent instance exposes a "' + data.title + '" function');
    });


    QUnit.module('provider', {
        teardown: function () {
            runnerFactory.clearProviders();
        }
    });


    QUnit.test('init', function (assert) {
        var instance;

        QUnit.expect(3);

        instance = runnerComponentFactory({provider: 'mock'});
        assert.equal(instance.getOption('provider'), 'mock', 'The right provider is set in the config');
        assert.equal(instance.getConfig(), instance.config, 'The instance gets the config');
        assert.deepEqual(instance.getConfig(), {provider: 'mock'}, 'The config object is returned');
    });


    QUnit.test('init error', function (assert) {
        QUnit.expect(1);

        assert.throws(function () {
            runnerComponentFactory();
        }, 'An error should be thrown if the provider is not set');
    });


    QUnit.asyncTest('render', function (assert) {
        var $container = $("#fixture-1");
        var instance;

        function mockTpl() {
            assert.ok(true, 'The provided template is used');
            return '<div id="foo-runner"></div>';
        }

        QUnit.expect(12);

        runnerFactory.registerProvider('foo', {
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
                    assert.equal(instance.getRunner(), null, 'The runner is destroyed');
                    assert.equal($container.children().length, 0, 'The component has been destroyed');

                    QUnit.start();
                }, 200);
            }
        });

        assert.equal($container.children().length, 0, 'The runner is not rendered');

        instance = runnerComponentFactory({
            provider: 'foo',
            renderTo: $container
        }, mockTpl)
            .on('runner', function (runner) {
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                assert.equal(runner, instance.getRunner(), 'The runner is reachable');
            })
            .on('ready', function () {
                assert.ok(true, 'The runner is ready');

                assert.equal($container.children().length, 1, 'The runner is rendered');
                assert.equal($container.find('#foo-runner').length, 1, 'The right template is used');

                instance.destroy();
            });
    });


    QUnit.asyncTest('dynamic providers', function (assert) {
        var $container = $("#fixture-2");
        var instance;

        QUnit.expect(4);
        QUnit.stop(1);

        instance = runnerComponentFactory({
            provider: 'mock',
            providers: [{
                module: 'taoTests/test/runner/runnerComponent/mockProvider',
                bundle: 'taoTests/test/runner/runnerComponent/mockBundle.min',
                category: 'mock'
            }],
            renderTo: $container
        })
            .on('runner', function (runner) {
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                assert.equal(runner, instance.getRunner(), 'The runner is reachable');

                runner.on('mock-provider-loaded', function () {
                    assert.ok(true, 'The right provider has been loaded');
                    QUnit.start();
                });
            })
            .on('ready', function () {
                assert.ok(true, 'The runner is ready');
                QUnit.start();
            });
    });


    QUnit.asyncTest('dynamic plugin', function (assert) {
        var $container = $("#fixture-3");
        var instance;

        QUnit.expect(6);
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

        instance = runnerComponentFactory({
            provider: 'bar',
            plugins: [{
                module: 'taoTests/test/runner/runnerComponent/mockPlugin',
                bundle: 'taoTests/test/runner/runnerComponent/mockBundle.min',
                category: 'mock'
            }],
            renderTo: $container
        })
            .on('runner', function (runner) {
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                assert.equal(runner, instance.getRunner(), 'The runner is reachable');

                runner.on('plugin-loaded.mock', function () {
                    assert.ok(true, 'The right plugin has been loaded');
                    QUnit.start();
                });
            })
            .on('ready', function () {
                assert.ok(true, 'The runner is ready');
                QUnit.start();
            });
    });


    QUnit.asyncTest('error event', function (assert) {
        var $container = $("#fixture-4");
        var error = 'oops!';
        var instance;

        QUnit.expect(6);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            }
        });

        instance = runnerComponentFactory({
            provider: 'foo',
            renderTo: $container
        })
            .on('runner', function (runner) {
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                assert.equal(runner, instance.getRunner(), 'The runner is reachable');

                runner.trigger('error', error);
            })
            .on('error', function (err) {
                assert.equal(err, error, 'The error has been forwarded');
            })
            .on('ready', function () {
                assert.ok(true, 'The runner is ready');
                QUnit.start();
            });
    });


    QUnit.asyncTest('whenReady', function (assert) {
        var $container = $("#fixture-4");
        var instance;
        var whenReady;

        QUnit.expect(8);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            }
        });

        instance = runnerComponentFactory({
            provider: 'foo',
            renderTo: $container
        })
            .on('runner', function (runner) {
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                assert.equal(runner, instance.getRunner(), 'The runner is reachable');
            })
            .on('ready', function () {
                assert.ok(true, 'The runner is ready');
            });

        whenReady = instance.whenReady(function () {
            assert.ok(true, 'The runner has called the whenReady callback');
        });

        assert.ok(whenReady instanceof Promise, 'The runner provided a promise to wait for its ready state');

        whenReady.then(function () {
            assert.ok(true, 'The runner is ready');
            QUnit.start();
        });
    });


    QUnit.asyncTest('loadItem', function (assert) {
        var $container = $("#fixture-4");
        var instance;
        var itemLoaded;

        QUnit.expect(9);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker: _.noop,
            init: function () {
                assert.ok(true, 'The init method has been called');
            },
            render: function () {
                assert.ok(true, 'The render method has been called');
            },
            loadItem: function() {
                assert.ok(true, 'The item is loaded');
            },
            renderItem: function() {
                assert.ok(true, 'The item is rendered');
            }
        });

        instance = runnerComponentFactory({
            provider: 'foo',
            renderTo: $container
        })
            .on('runner', function (runner) {
                assert.equal(typeof runner, 'object', 'The runner instance is provided');
                assert.equal(runner, instance.getRunner(), 'The runner is reachable');
            })
            .on('ready', function () {
                assert.ok(true, 'The runner is ready');
            });

        itemLoaded = instance.loadItem('foo');

        assert.ok(itemLoaded instanceof Promise, 'The runner provided a promise to wait for the item to be fully loaded');

        itemLoaded.then(function () {
            assert.ok(true, 'The item has been loaded');
            QUnit.start();
        });
    });
});
