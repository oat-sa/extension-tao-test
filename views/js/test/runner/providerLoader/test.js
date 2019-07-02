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
    'taoTests/runner/providerLoader',
    'taoTests/runner/runner',
    'json!taoTests/test/runner/mocks/config.json'
], function(providerLoader, runner, sampleConfig) {
    'use strict';

    QUnit.module('API');

    QUnit.test('module', assert => {
        assert.expect(1);

        assert.equal(typeof providerLoader, 'function', 'The providerLoader module is a function');
    });

    QUnit.module('Load and register', {
        beforeEach(){
            runner.clearProviders();
        }
    });

    QUnit.test('Nothing to load', assert => {
        const ready = assert.async();
        assert.expect(1);

        providerLoader()
            .then( result => assert.deepEqual(result, {}, 'Nothing to load, nothing loaded') )
            .catch(err => assert.ok(false, err.message) )
            .then( ready );
    });

    QUnit.test('Load one runner provider', assert => {
        const ready = assert.async();
        assert.expect(3);

        assert.deepEqual(runner.getAvailableProviders(), [], 'No provider registered');

        providerLoader({ runner : sampleConfig.providers.runner }, false)
            .then( result => {
                assert.deepEqual(result.runner, runner, 'The provider target is returned');
                assert.deepEqual(
                    runner.getAvailableProviders(),
                    [ sampleConfig.providers.runner.id ],
                    'The expected provider is registered'
                );
            })
            .catch(err => assert.ok(false, err.message) )
            .then( ready );
    });

    QUnit.test('Load multiple runner providers', assert => {
        const ready = assert.async();
        assert.expect(3);

        assert.deepEqual(runner.getAvailableProviders(), [], 'No provider registered');

        providerLoader({
            "runner": [{
                "id": "mock-runner",
                "module": "taoTests/test/runner/mocks/mockRunnerProvider",
                "bundle": "taoTests/test/runner/mocks/mockBundle.min",
                "category": "testrunner"
            }, {
                "id": "mock-alt-runner",
                "module": "taoTests/test/runner/mocks/mockAltRunnerProvider",
                "bundle": "taoTests/test/runner/mocks/mockBundle.min",
                "category": "testrunner"
            }]
        }, false)
        .then( result => {
            assert.deepEqual(result.runner, runner, 'The provider target is returned');
            assert.deepEqual(runner.getAvailableProviders(), ['mock-runner', 'mock-alt-runner'], 'The expected provider is registered');
        })
        .catch(err => assert.ok(false, err.message) )
        .then( ready );
    });

    QUnit.test('Load from bundle', assert => {
        const ready = assert.async();
        assert.expect(3);

        assert.deepEqual(runner.getAvailableProviders(), [], 'No provider registered');

        providerLoader({
            "runner": [{
                "id": "mock-runner-from-bundle",
                "module": "taoTests/test/runner/mocks/bundledMockRunnerProvider",
                "bundle": "taoTests/test/runner/mocks/mockBundle.min",
                "category": "testrunner"
            }]
        }, true)
        .then( result => {
            assert.deepEqual(result.runner, runner, 'The provider target is returned');
            assert.deepEqual(runner.getAvailableProviders(), ['mock-runner-from-bundle'], 'The expected provider is registered');
        })
        .catch(err => assert.ok(false, err.message) )
        .then( ready );
    });

    QUnit.test('Invalid provider configuration', assert => {
        assert.expect(3);

        assert.throws( () => {
            providerLoader({
                "runner": { }
            });
        }, TypeError);

        assert.throws( () => {
            providerLoader({
                "runner": {
                    "module": "taoTests/test/runner/mocks/mockRunnerProvider",
                }
            });
        }, TypeError);

        assert.throws( () => {
            providerLoader({
                "runner": {
                    "category": "foo",
                }
            });
        }, TypeError);
    });

    QUnit.test('Load wrong module', assert => {
        assert.expect(1);

        assert.rejects(
            providerLoader({
                "runner": {
                    "id": "mock-runner",
                    "module": "taoFoo/test/runner/mocks/mockRunnerProvider",
                    "bundle": "taoTests/test/runner/mocks/mockBundle.min",
                    "category": "testrunner"
                }
            }),
            /Script error for "taoFoo/
        );
    });

    QUnit.test('Load an invalid module', assert => {
        assert.expect(1);

        assert.rejects(
            providerLoader({
                "runner": {
                    "id": "mock-runner",
                    "module": "taoTests/test/runner/mocks/mockInvalidRunnerProvider",
                    "bundle": "taoTests/test/runner/mocks/mockBundle.min",
                    "category": "testrunner"
                }
            }),
            `The module 'taoTests/test/runner/mocks/mockInvalidRunnerProvider' is not valid`
        );
    });

    QUnit.test('Load plugins', assert => {
        const ready = assert.async();
        assert.expect(3);

        providerLoader({ plugins: sampleConfig.providers.plugins })
            .then( result => {
                assert.equal(result.plugins.length, 2, '2 plugins have been loaded');
                assert.equal(typeof result.plugins[0], 'function', 'The plugin factory is exposed');
                assert.equal(typeof result.plugins[1], 'function', 'The plugin factory is exposed');
            })
            .catch(err => assert.ok(false, err.message) )
            .then( ready );
    });
});
