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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define(['lodash', 'taoTests/runner/proxy'], function(_, proxyFactory) {
    'use strict';

    QUnit.module('proxyFactory');

    var defaultProxy = {
        init : function() {},
        destroy : function() {},
        getTestData : function() {},
        getTestContext : function() {},
        callTestAction : function() {},
        getItemData : function() {},
        getItemState : function() {},
        submitItemState : function() {},
        storeItemResponse : function() {},
        callItemAction : function() {}
    };


    QUnit.test('module', 5, function(assert) {
        assert.equal(typeof proxyFactory, 'function', "The proxyFactory module exposes a function");
        assert.equal(typeof proxyFactory.registerProxy, 'function', "The proxyFactory module exposes a registerProxy method");
        assert.equal(typeof proxyFactory.getProxy, 'function', "The proxyFactory module exposes a getProxy method");

        proxyFactory.registerProxy('default', defaultProxy);

        assert.equal(typeof proxyFactory(), 'object', "The proxyFactory factory produces an object");
        assert.notStrictEqual(proxyFactory(), proxyFactory(), "The proxyFactory factory provides a different object on each call");
    });


    // small coverage check to facilitate dev of unit tests
    var coverage = {};
    QUnit.moduleDone(function() {
        _.forEach(proxyApi, function(api) {
            if (!coverage[api.name]) {
                console.log('Missing unit test for method proxyFactory.' + api.name);
            }
        });
    });

    var proxyApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'getTestData', title : 'getTestData' },
        { name : 'getTestContext', title : 'getTestContext' },
        { name : 'callTestAction', title : 'callTestAction' },
        { name : 'getItemData', title : 'getItemData' },
        { name : 'getItemState', title : 'getItemState' },
        { name : 'submitItemState', title : 'submitItemState' },
        { name : 'storeItemResponse', title : 'storeItemResponse' },
        { name : 'callItemAction', title : 'callItemAction' }
    ];

    QUnit
        .cases(proxyApi)
        .test('instance API ', 1, function(data, assert) {
            var instance = proxyFactory();
            assert.equal(typeof instance[data.name], 'function', 'The proxyFactory instance exposes a "' + data.title + '" function');
        });


    QUnit.asyncTest('proxyFactory.init', 6, function(assert) {
        var initConfig = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.init = true;

        proxyFactory.registerProxy('default', _.defaults({
            init : function(config) {
                assert.ok(true, 'The proxyFactory has delegated the call to init');
                assert.equal(config, initConfig, 'The proxyFactory has provided the config object to the init method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default', initConfig).on('init', function(p, config) {
            assert.ok(true, 'The proxyFactory has fired the "init" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "init" event');
            assert.equal(config, initConfig, 'The proxyFactory has provided the config object through the "init" event');
            QUnit.start();
        }).init();

        assert.equal(result, promise, 'The proxyFactory.init method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.destroy', 4, function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.destroy = true;

        proxyFactory.registerProxy('default', _.defaults({
            destroy : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to destroy');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('destroy', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "destroy" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "destroy" event');
            QUnit.start();
        }).destroy();

        assert.equal(result, promise, 'The proxyFactory.destroy method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getTestData', 4, function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.getTestData = true;

        proxyFactory.registerProxy('default', _.defaults({
            getTestData : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestData');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getTestData', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "getTestData" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getTestData" event');
            QUnit.start();
        }).getTestData();

        assert.equal(result, promise, 'The proxyFactory.getTestData method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getTestContext', 4, function(assert) {
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.getTestContext = true;

        proxyFactory.registerProxy('default', _.defaults({
            getTestContext : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestContext');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getTestContext', function(p) {
            assert.ok(true, 'The proxyFactory has fired the "getTestContext" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getTestContext" event');
            QUnit.start();
        }).getTestContext();

        assert.equal(result, promise, 'The proxyFactory.getTestContext method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.callTestAction', 8, function(assert) {
        var expectedAction = 'test';
        var expectedParams = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.callTestAction = true;

        proxyFactory.registerProxy('default', _.defaults({
            callTestAction : function(action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to callTestAction');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the callTestAction method');
                assert.equal(params, expectedParams, 'The proxyFactory has provided the params to the callTestAction method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('callTestAction', function(p, action, params) {
            assert.ok(true, 'The proxyFactory has fired the "callTestAction" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "callTestAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callTestAction" event');
            assert.equal(params, expectedParams, 'The proxyFactory has provided the params through the "callTestAction" event');
            QUnit.start();
        }).callTestAction(expectedAction, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.callTestAction method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getItemData', 6, function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.getItemData = true;

        proxyFactory.registerProxy('default', _.defaults({
            getItemData : function(uri) {
                assert.ok(true, 'The proxyFactory has delegated the call to getItemData');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the getItemData method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getItemData', function(p, uri) {
            assert.ok(true, 'The proxyFactory has fired the "getItemData" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getItemData" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "getItemData" event');
            QUnit.start();
        }).getItemData(expectedUri);

        assert.equal(result, promise, 'The proxyFactory.getItemData method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.getItemState', 6, function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.getItemState = true;

        proxyFactory.registerProxy('default', _.defaults({
            getItemState : function(uri) {
                assert.ok(true, 'The proxyFactory has delegated the call to getItemState');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the getItemState method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getItemState', function(p, uri) {
            assert.ok(true, 'The proxyFactory has fired the "getItemState" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getItemState" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "getItemState" event');
            QUnit.start();
        }).getItemState(expectedUri);

        assert.equal(result, promise, 'The proxyFactory.getItemState method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.submitItemState', 8, function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedState = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.submitItemState = true;

        proxyFactory.registerProxy('default', _.defaults({
            submitItemState : function(uri, state) {
                assert.ok(true, 'The proxyFactory has delegated the call to submitItemState');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the submitItemState method');
                assert.equal(state, expectedState, 'The proxyFactory has provided the state to the submitItemState method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('submitItemState', function(p, uri, state) {
            assert.ok(true, 'The proxyFactory has fired the "submitItemState" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "submitItemState" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "submitItemState" event');
            assert.equal(state, expectedState, 'The proxyFactory has provided the state through the "submitItemState" event');
            QUnit.start();
        }).submitItemState(expectedUri, expectedState);

        assert.equal(result, promise, 'The proxyFactory.submitItemState method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.storeItemResponse', 8, function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedResponse = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.storeItemResponse = true;

        proxyFactory.registerProxy('default', _.defaults({
            storeItemResponse : function(uri, response) {
                assert.ok(true, 'The proxyFactory has delegated the call to storeItemResponse');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the storeItemResponse method');
                assert.equal(response, expectedResponse, 'The proxyFactory has provided the response to the storeItemResponse method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('storeItemResponse', function(p, uri, response) {
            assert.ok(true, 'The proxyFactory has fired the "storeItemResponse" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "storeItemResponse" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "storeItemResponse" event');
            assert.equal(response, expectedResponse, 'The proxyFactory has provided the response through the "storeItemResponse" event');
            QUnit.start();
        }).storeItemResponse(expectedUri, expectedResponse);

        assert.equal(result, promise, 'The proxyFactory.storeItemResponse method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.callItemAction', 10, function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedAction = 'test';
        var expectedParams = {};
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();
        coverage.callItemAction = true;

        proxyFactory.registerProxy('default', _.defaults({
            callItemAction : function(uri, action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to callItemAction');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the callItemAction method');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the callItemAction method');
                assert.equal(params, expectedParams, 'The proxyFactory has provided the params to the callItemAction method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('callItemAction', function(p, uri, action, params) {
            assert.ok(true, 'The proxyFactory has fired the "callItemAction" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "callItemAction" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "callItemAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callItemAction" event');
            assert.equal(params, expectedParams, 'The proxyFactory has provided the params through the "callItemAction" event');
            QUnit.start();
        }).callItemAction(expectedUri, expectedAction, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.callItemAction method has returned a promise');
    });
});
