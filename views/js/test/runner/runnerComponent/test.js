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
 * Copyright (c) 2017-2019 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'taoTests/runner/runnerComponent',
    'taoTests/runner/runner',
    'json!taoTests/test/runner/mocks/config.json'
], function(runnerComponent, runner, sampleConfig) {
    'use strict';


    QUnit.module('factory');

    QUnit.test('module', assert => {
        const container = document.getElementById('fixture-module');

        assert.expect(3);

        assert.equal(typeof runnerComponent, 'function', 'The runnerComponent module exposes a function');
        assert.equal(typeof runnerComponent(container, sampleConfig), 'object', 'The runnerComponent factory produces an object');

        assert.notStrictEqual(
            runnerComponent(container, sampleConfig),
            runnerComponent(container, sampleConfig),
            'The runnerComponent factory provides a different object on each call'
        );
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
    ]).test('API ', (data, assert) => {
        const container = document.getElementById('fixture-module');

        assert.expect(1);

        assert.equal(
            typeof runnerComponent(container, sampleConfig)[data.title],
            'function',
            `The runnerComponent instance exposes a "${data.title}" function`
        );
    });


    QUnit.module('Component configuration', {
        beforeEach(){
            runner.clearProviders();
        }
    });

    QUnit.test('Wrong configuration', assert => {
        assert.expect(3);

        const container = document.getElementById('fixture-module');

        assert.throws(
            () => runnerComponent(),
            /A container element must be defined to contain the runnerComponent/
        );

        assert.throws(
            () => runnerComponent(container),
            /the following properties : providers,options,serviceCallId/
        );

        assert.throws(
            () => runnerComponent(container, { serviceCallId : 'foo', options : {} }),
            /the following properties : providers,options,serviceCallId/
        );
    });

    QUnit.module('Component lifecycle', {
        beforeEach(){
            runner.clearProviders();
        }
    });

    QUnit.test('Initialize from a registered provider', assert => {
        const ready = assert.async();
        const container = document.getElementById('fixture-init');
        const config = {
            serviceCallId : 'foo',
            provider: {
                runner : 'mock-init'
            },
            providers: {},
            options: {}
        };

        function mockTpl() {
            assert.ok(true, 'The provided template is used');
            return '<div id="foo-runner"></div>';
        }

        assert.expect(9);

        runner.registerProvider('mock-init', {
            loadAreaBroker: () => {},
            init: function() {
                assert.ok(true, 'The init method has been called');
            },
            render: function() {
                assert.ok(true, 'The render method has been called');
            },
            destroy: function() {
                assert.ok(true, 'The destroy method has been called');

                setTimeout(() => {
                    assert.equal(container.childNodes.length, 0, 'The component has been destroyed');

                    ready();
                }, 200);
            }
        });

        assert.equal(container.childNodes.length, 0, 'The runner is not rendered');

        runnerComponent(container, config, mockTpl)
            .on('error', err => assert.ok(false, err.message) )
            .on('ready', function() {
                assert.ok(true, 'The runner is ready');
                assert.equal(container.childNodes.length, 1, 'The runner is rendered');
                assert.equal(container.querySelectorAll('#foo-runner').length, 1, 'The right template is used');

                this.destroy();
            });
    });

    QUnit.test('Initialize from providers to load', assert => {
        const ready = assert.async();
        const container = document.getElementById('fixture-init');

        assert.expect(3);

        assert.deepEqual(runner.getAvailableProviders(), [], 'No providers are registered');

        runnerComponent(container, sampleConfig)
            .on('error', err => assert.ok(false, err.message) )
            .on('render', function() {
                assert.deepEqual(runner.getAvailableProviders(), ['mock-runner'], 'The correct provider is loaded');
                this.getRunner().on('mock-runner-loaded', () => {

                    assert.ok(true, 'The mock provider has triggered and event');

                    this.destroy();
                });
            })
            .on('destroy', ready);
    });


    QUnit.test('spread error event from test runner', function(assert) {
        const ready = assert.async();
        const container = document.getElementById('fixture-error');
        const error = 'oops!';

        assert.expect(2);

        runnerComponent(container, sampleConfig)
            .on('error', function(err) {
                assert.equal(err, error, 'The error has been forwarded');
                ready();
            })
            .on('ready', function(testRunner) {
                assert.equal(typeof testRunner, 'object', 'The runner instance is provided');

                testRunner.trigger('error', error);
            });
    });

    QUnit.test('spread error event from test runner', function(assert) {
        const ready = assert.async();
        const container = document.getElementById('fixture-error');
        const error = 'oops!';

        assert.expect(2);

        runnerComponent(container, sampleConfig)
            .on('error', function(err) {
                assert.equal(err, error, 'The error has been forwarded');
                ready();
            })
            .on('ready', function(testRunner) {
                assert.equal(typeof testRunner, 'object', 'The runner instance is provided');

                testRunner.trigger('error', error);
            });
    });

    QUnit.test('getRunner', function(assert) {
        const ready = assert.async();
        const container = document.getElementById('fixture-get');

        assert.expect(3);

        runnerComponent(container, sampleConfig)
            .on('ready', function(testRunner) {
                assert.equal(typeof testRunner, 'object', 'The runner instance is provided');

                assert.equal(testRunner, this.getRunner(), 'The runner is reachable');
                this.destroy();
            })
            .on('destroy', function() {
                assert.equal(this.getRunner(), null, 'The runner has been destroyed');
                ready();
            });
    });
});
