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
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define(['lodash', 'taoTests/runner/proxy'], function(_, proxyFactory) {
    'use strict';

    QUnit.module('proxyFactory');

    var defaultProxy = {
        init : function() {},
        destroy : function() {},
        getItem : function() {},
        submitItemState : function() {},
        storeItemResponse : function() {},
        actionCall : function() {}
    };


    QUnit.test('module', 5, function(assert) {
        assert.equal(typeof proxyFactory, 'function', "The proxyFactory module exposes a function");
        assert.equal(typeof proxyFactory.registerProxy, 'function', "The proxyFactory module exposes a registerProxy method");
        assert.equal(typeof proxyFactory.getProxy, 'function', "The proxyFactory module exposes a getProxy method");

        proxyFactory.registerProxy('default', defaultProxy);

        assert.equal(typeof proxyFactory(), 'object', "The proxyFactory factory produces an object");
        assert.notStrictEqual(proxyFactory(), proxyFactory(), "The proxyFactory factory provides a different object on each call");
    });


    var proxyApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'getItem', title : 'getItem' },
        { name : 'submitItemState', title : 'submitItemState' },
        { name : 'storeItemResponse', title : 'storeItemResponse' },
        { name : 'actionCall', title : 'actionCall' }
    ];

    QUnit
        .cases(proxyApi)
        .test('instance API ', 1, function(data, assert) {
            var instance = proxyFactory();
            assert.equal(typeof instance[data.name], 'function', 'The proxyFactory instance exposes a "' + data.title + '" function');
        });


    QUnit.asyncTest('proxyFactory.init', 4, function(assert) {
        var initConfig = {};

        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            init : function(config) {
                assert.ok(true, 'The proxyFactory has delegated the call to init');
                assert.equal(config, initConfig, 'The proxyFactory has provided the config object to the init method');
                QUnit.start();
            }
        }, defaultProxy));

        proxyFactory('default', initConfig).on('init', function(config) {
            assert.ok(true, 'The proxyFactory has fired the "init" event');
            assert.equal(config, initConfig, 'The proxyFactory has provided the config object through the "init" event');
            QUnit.start();
        }).init();
    });


    QUnit.asyncTest('proxyFactory.destroy', 2, function(assert) {
        var initConfig = {};

        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            destroy : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to destroy');
                QUnit.start();
            }
        }, defaultProxy));

        proxyFactory('default', initConfig).on('destroy', function() {
            assert.ok(true, 'The proxyFactory has fired the "destroy" event');
            QUnit.start();
        }).destroy();
    });


    QUnit.asyncTest('proxyFactory.getItem', 6, function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var promise = {
            resolve: function() {},
            reject: function() {},
            then: function() {},
            catch: function() {}
        };

        QUnit.stop();

        proxyFactory.registerProxy('default', _.defaults({
            getItem : function(uri) {
                assert.ok(true, 'The proxyFactory has delegated the call to getItem');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the getItem method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('getItem', function(uri, p) {
            assert.ok(true, 'The proxyFactory has fired the "getItem" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "getItem" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "getItem" event');
            QUnit.start();
        }).getItem(expectedUri);

        assert.equal(result, promise, 'The proxyFactory.getItem method has returned a promise');
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

        proxyFactory.registerProxy('default', _.defaults({
            submitItemState : function(uri, state) {
                assert.ok(true, 'The proxyFactory has delegated the call to submitItemState');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the submitItemState method');
                assert.equal(state, expectedState, 'The proxyFactory has provided the state to the submitItemState method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('submitItemState', function(uri, state, p) {
            assert.ok(true, 'The proxyFactory has fired the "submitItemState" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "submitItemState" event');
            assert.equal(state, expectedState, 'The proxyFactory has provided the state through the "submitItemState" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "submitItemState" event');
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

        proxyFactory.registerProxy('default', _.defaults({
            storeItemResponse : function(uri, response) {
                assert.ok(true, 'The proxyFactory has delegated the call to storeItemResponse');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the storeItemResponse method');
                assert.equal(response, expectedResponse, 'The proxyFactory has provided the response to the storeItemResponse method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('storeItemResponse', function(uri, response, p) {
            assert.ok(true, 'The proxyFactory has fired the "storeItemResponse" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "storeItemResponse" event');
            assert.equal(response, expectedResponse, 'The proxyFactory has provided the response through the "storeItemResponse" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "storeItemResponse" event');
            QUnit.start();
        }).storeItemResponse(expectedUri, expectedResponse);

        assert.equal(result, promise, 'The proxyFactory.storeItemResponse method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.actionCall', 10, function(assert) {
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

        proxyFactory.registerProxy('default', _.defaults({
            actionCall : function(uri, action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to actionCall');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the actionCall method');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the actionCall method');
                assert.equal(params, expectedParams, 'The proxyFactory has provided the params to the actionCall method');
                QUnit.start();
                return promise;
            }
        }, defaultProxy));

        var result = proxyFactory('default').on('actionCall', function(uri, action, params, p) {
            assert.ok(true, 'The proxyFactory has fired the "actionCall" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "actionCall" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "actionCall" event');
            assert.equal(params, expectedParams, 'The proxyFactory has provided the params through the "actionCall" event');
            assert.equal(p, promise, 'The proxyFactory has provided the promise through the "actionCall" event');
            QUnit.start();
        }).actionCall(expectedUri, expectedAction, expectedParams);

        assert.equal(result, promise, 'The proxyFactory.actionCall method has returned a promise');
    });
});
