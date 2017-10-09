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
define([
    'lodash',
    'core/promise',
    'core/eventifier',
    'core/collections',
    'taoTests/runner/proxy'
], function(_, Promise, eventifier, collections, proxyFactory) {
    'use strict';

    var defaultProxy = {
        init : _.noop,
        destroy : _.noop,
        getTestData : _.noop,
        getTestContext : _.noop,
        getTestMap : _.noop,
        callTestAction : _.noop,
        getItem : _.noop,
        submitItem : _.noop,
        callItemAction : _.noop,
        telemetry : _.noop
    };

    QUnit.module('proxyFactory', {
        setup: function () {
            proxyFactory.clearProviders();
        }
    });

    QUnit.test('module', function(assert) {
        QUnit.expect(5);

        assert.equal(typeof proxyFactory, 'function', "The proxyFactory module exposes a function");
        assert.equal(typeof proxyFactory.registerProvider, 'function', "The proxyFactory module exposes a registerProvider method");
        assert.equal(typeof proxyFactory.getProvider, 'function', "The proxyFactory module exposes a getProvider method");

        proxyFactory.registerProvider('default', defaultProxy);

        assert.equal(typeof proxyFactory(), 'object', "The proxyFactory factory produces an object");
        assert.notStrictEqual(proxyFactory(), proxyFactory(), "The proxyFactory factory provides a different object on each call");
    });

    QUnit.cases([
        { name : 'install', title : 'install' },
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'getTokenHandler', title : 'getTokenHandler' },
        { name : 'getCommunicator', title : 'getCommunicator' },
        { name : 'channel', title : 'channel' },
        { name : 'send', title : 'send' },
        { name : 'addCallActionParams', title : 'addCallActionParams' },
        { name : 'getTestData', title : 'getTestData' },
        { name : 'getTestContext', title : 'getTestContext' },
        { name : 'getTestMap', title : 'getTestMap' },
        { name : 'sendVariables', title : 'sendVariables' },
        { name : 'callTestAction', title : 'callTestAction' },
        { name : 'getItem', title : 'getItem' },
        { name : 'submitItem', title : 'submitItem' },
        { name : 'callItemAction', title : 'callItemAction' },
        { name : 'getDataHolder', title : 'getDataHolder' },
        { name : 'isOnline', title : 'isOnline' },
        { name : 'isOffline',title : 'isOffline'},
        { name : 'setOnline', title : 'setOnline'},
        { name : 'setOffline',  title : 'setOffline'},
        { name : 'isConnectivityError',  title : 'isConnectivityError'}
    ]).test('instance API ', function(data, assert) {
        var instance;

        proxyFactory.registerProvider('default', defaultProxy);
        instance = proxyFactory('default');
        QUnit.expect(1);
        assert.equal(typeof instance[data.name], 'function', 'The proxyFactory instance exposes a "' + data.title + '" function');
    });


    QUnit.asyncTest('proxyFactory.init', function(assert) {
        var initConfig = {};
        var expectedParams = {
            storeId: '342FEEF6-ECA0-418E-81E3-4E6F7D1F90E4'
        };

        QUnit.expect(7);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            init : function(config, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to init');
                assert.equal(config, initConfig, 'The proxyFactory has provided the config object to the init method');
                assert.deepEqual(params, expectedParams, 'The delegated method received the expected params');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var result = proxyFactory('default', initConfig).on('init', function(promise, config) {
            assert.ok(true, 'The proxyFactory has fired the "init" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "init" event');
            assert.equal(config, initConfig, 'The proxyFactory has provided the config object through the "init" event');
            QUnit.start();
        }).init(expectedParams);

        assert.ok(result instanceof Promise, 'The proxyFactory.init method has returned a promise');
    });


    QUnit.asyncTest('proxyFactory.init with params', function(assert) {
        var initConfig = {};
        var extraParams = {param1:"1", param2: 2};
        var initParams = {
            storeId: '342FEEF6-ECA0-418E-81E3-4E6F7D1F90E4'
        };
        var expectedParams = _.merge({}, initParams, extraParams);

        QUnit.expect(8);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            init : function(config, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to init');
                assert.equal(config, initConfig, 'The proxyFactory has provided the config object to the init method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the init params to the init method');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var result = proxyFactory('default', initConfig).on('init', function(promise, config, params) {
            assert.ok(true, 'The proxyFactory has fired the "init" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "init" event');
            assert.equal(config, initConfig, 'The proxyFactory has provided the config object through the "init" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the init params through the "init" event');
            QUnit.start();
        }).addCallActionParams(extraParams).init(initParams);

        assert.ok(result instanceof Promise, 'The proxyFactory.init method has returned a promise');
    });

    QUnit.asyncTest('proxyFactory.install', function(assert) {
        var proxy;
        QUnit.expect(2);

        proxyFactory.registerProvider('default', {
            install : function(){
                this.foo = 12;
                this.bar = function(){
                    return this.foo;
                };
            },
            init : function() {
                assert.equal(this.foo, 12, 'The foo member if available and correct');
                assert.equal(this.bar(), 12, 'The bar member if available and correct');
                return Promise.resolve();
            }
        });

        proxy = proxyFactory('default');
        proxy.install();

        proxy.init().then(function() {
            QUnit.start();
        });
    });

    QUnit.asyncTest('get data', function(assert) {
        var proxy;
        var dataHolderMock = new collections.Map();
        dataHolderMock.set('testContext', {
            foo : true
        });

        QUnit.expect(3);

        proxyFactory.registerProvider('default', {
            init: function init(){
                assert.equal(typeof proxy.getDataHolder(), 'object', 'The dataHolder is now available');
                assert.ok(proxy.getDataHolder().get('testContext').foo, 'test context is set');
            }
        });

        proxy = proxyFactory('default');

        assert.equal(typeof proxy.getDataHolder(), 'undefined', 'The dataHolder is not yet available');

        proxy.install(dataHolderMock)
            .then(function(){
                return proxy.init();
            })
            .then(function(){
                QUnit.start();
            })
            .catch(function(err){
                assert.ok(false, err);
                QUnit.start();
            });
    });

    QUnit.asyncTest('proxyFactory.destroy', function(assert) {
        QUnit.expect(5);
        QUnit.stop(2);

        proxyFactory.registerProvider('default', _.defaults({
            destroy : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to destroy');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('destroy', function(promise) {
            assert.ok(true, 'The proxyFactory has fired the "destroy" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "destroy" event');
            QUnit.start();
        });

        proxy.init().then(function() {
            var result = proxy.destroy();

            assert.ok(result instanceof Promise, 'The proxyFactory.destroy method has returned a promise');

            result.then(function() {
                proxy.getTestContext()
                    .then(function() {
                        assert.ok(false, 'The proxy must be initialized');
                        QUnit.start();
                    })
                    .catch(function() {
                        assert.ok(true, 'The proxy must be initialized');
                        QUnit.start();
                    });
            });
        });

    });


    QUnit.asyncTest('proxyFactory.getTestData', function(assert) {
        QUnit.expect(5);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            getTestData : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestData');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('getTestData', function(promise) {
            assert.ok(true, 'The proxyFactory has fired the "getTestData" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "getTestData" event');
            QUnit.start();
        });

        proxy.getTestData()
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function() {
            var result = proxy.getTestData();

            assert.ok(result instanceof Promise, 'The proxyFactory.getTestData method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.getTestContext', function(assert) {
        QUnit.expect(5);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            getTestContext : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestContext');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('getTestContext', function(promise) {
            assert.ok(true, 'The proxyFactory has fired the "getTestContext" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "getTestContext" event');
            QUnit.start();
        });

        proxy.getTestContext()
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function() {
            var result = proxy.getTestContext();

            assert.ok(result instanceof Promise, 'The proxyFactory.getTestContext method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.getTestMap', function(assert) {
        QUnit.expect(5);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            getTestMap : function() {
                assert.ok(true, 'The proxyFactory has delegated the call to getTestMap');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('getTestMap', function(promise) {
            assert.ok(true, 'The proxyFactory has fired the "getTestMap" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "getTestMap" event');
            QUnit.start();
        });

        proxy.getTestMap()
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function() {
            var result = proxy.getTestMap();

            assert.ok(result instanceof Promise, 'The proxyFactory.getTestMap method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.sendVariables', function(assert) {
        var expectedVariables = {
            foo : 'bar'
        };

        QUnit.expect(7);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            sendVariables : function(variables) {
                assert.ok(true, 'The proxyFactory has delegated the call to sendVariables');
                assert.deepEqual(variables, expectedVariables, 'The proxyFactory has provided the variables to the sendVariables method');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('sendVariables', function(promise, variables) {
            assert.ok(true, 'The proxyFactory has fired the "sendVariables" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "sendVariables" event');
            assert.deepEqual(variables, expectedVariables, 'The proxyFactory has provided the variables through the "sendVariables" event');
            QUnit.start();
        });

        proxy.sendVariables(expectedVariables)
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            var result = proxy.sendVariables(expectedVariables);

            assert.ok(result instanceof Promise, 'The proxyFactory.sendVariables method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.callTestAction', function(assert) {
        var expectedAction = 'test';
        var expectedParams = {
            foo : 'bar'
        };

        QUnit.expect(9);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            callTestAction : function(action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to callTestAction');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the callTestAction method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the callTestAction method');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('callTestAction', function(promise, action, params) {
            assert.ok(true, 'The proxyFactory has fired the "callTestAction" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "callTestAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callTestAction" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callTestAction" event');
            QUnit.start();
        });

        proxy.callTestAction(expectedAction, expectedParams)
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            var result = proxy.callTestAction(expectedAction, expectedParams);

            assert.ok(result instanceof Promise, 'The proxyFactory.callTestAction method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.getItem', function(assert) {
        var proxy;
        var expectedUri    = 'http://tao.dev#item123';
        var expectedParams = {
            itemIdentifier : 'Item-123'
        };

        QUnit.expect(8);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            getItem : function(uri, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to getItem');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the getItem method');
                assert.deepEqual(params, expectedParams, 'The given parameters are corrects');

                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        proxy = proxyFactory('default').on('getItem', function(promise, uri) {
            assert.ok(true, 'The proxyFactory has fired the "getItem" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "getItem" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "getItem" event');

            QUnit.start();
        });

        proxy.getItem(expectedUri, expectedParams)
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            var result = proxy.getItem(expectedUri, expectedParams);

            assert.ok(result instanceof Promise, 'The proxyFactory.getItem method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.submitItem', function(assert) {
        var expectedUri      = 'http://tao.dev#item123';
        var expectedState    = { state: true };
        var expectedResponse = { response: true };
        var expectedParams   = { duration : 12.12324 };

        QUnit.expect(13);

        proxyFactory.registerProvider('default', _.defaults({
            submitItem : function(uri, state, response, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to submitItem');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the submitItem method');
                assert.equal(state, expectedState, 'The proxyFactory has provided the state to the submitItem method');
                assert.equal(response, expectedResponse, 'The proxyFactory has provided the response to the submitItem method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the submitItem method');

                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('submitItem', function(promise, uri, state, response, params) {
            assert.ok(true, 'The proxyFactory has fired the "submitItem" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "submitItem" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "submitItem" event');
            assert.equal(state, expectedState, 'The proxyFactory has provided the state through the "submitItem" event');
            assert.equal(response, expectedResponse, 'The proxyFactory has provided the response through the "submitItem" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "submitItem" event');

            QUnit.start();
        });

        proxy.submitItem(expectedUri, expectedState, expectedResponse, expectedParams)
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            var result = proxy.submitItem(expectedUri, expectedState, expectedResponse, expectedParams);

            assert.ok(result instanceof Promise, 'The proxyFactory.submitItem method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.callItemAction', function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedAction = 'test';
        var expectedParams = {};

        QUnit.expect(11);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            callItemAction : function(uri, action, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to callItemAction');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the callItemAction method');
                assert.equal(action, expectedAction, 'The proxyFactory has provided the action to the callItemAction method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the callItemAction method');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('callItemAction', function(promise, uri, action, params) {
            assert.ok(true, 'The proxyFactory has fired the "callItemAction" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "callItemAction" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "callItemAction" event');
            assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callItemAction" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callItemAction" event');
            QUnit.start();
        });

        proxy.callItemAction(expectedUri, expectedAction, expectedParams)
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            var result = proxy.callItemAction(expectedUri, expectedAction, expectedParams);

            assert.ok(result instanceof Promise, 'The proxyFactory.callItemAction method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.telemetry', function(assert) {
        var expectedUri = 'http://tao.dev#item123';
        var expectedSignal = 'test';
        var expectedParams = {};

        QUnit.expect(11);
        QUnit.stop();

        proxyFactory.registerProvider('default', _.defaults({
            telemetry : function(uri, signal, params) {
                assert.ok(true, 'The proxyFactory has delegated the call to telemetry');
                assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI to the telemetry method');
                assert.equal(signal, expectedSignal, 'The proxyFactory has provided the signal to the telemetry method');
                assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params to the telemetry method');
                QUnit.start();
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default').on('telemetry', function(promise, uri, signal, params) {
            assert.ok(true, 'The proxyFactory has fired the "telemetry" event');
            assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "telemetry" event');
            assert.equal(uri, expectedUri, 'The proxyFactory has provided the URI through the "telemetry" event');
            assert.equal(signal, expectedSignal, 'The proxyFactory has provided the signal through the "telemetry" event');
            assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "telemetry" event');
            QUnit.start();
        });

        proxy.telemetry(expectedUri, expectedSignal, expectedParams)
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            var result = proxy.telemetry(expectedUri, expectedSignal, expectedParams);

            assert.ok(result instanceof Promise, 'The proxyFactory.telemetry method has returned a promise');
        });
    });


    QUnit.asyncTest('proxyFactory.addCallActionParams', function(assert) {
        var expectedItemUri = 'http://tao.dev#item123';
        var expectedAction = 'test';
        var expectedParams = {
            foo : true,
            bar : ['a', 'b']
        };
        var extraParams = {
            noz : 'moo'
        };
        var expectedState    = { state: true };
        var expectedResponse = { response: true };
        var extraParamsSet = false;

        QUnit.expect(20);

        proxyFactory.registerProvider('default', _.defaults({
            callTestAction: function () {
                return Promise.resolve();
            },
            callItemAction: function () {
                return Promise.resolve();
            },
            submitItem: function () {
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default');

        proxy.init().then(function () {
            proxy
                .on('callItemAction', function(p, uri, action, params) {

                    assert.equal(uri, expectedItemUri, 'The proxyFactory has provided the URI through the "callItemAction" event');
                    assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callItemAction" event');

                    if (extraParamsSet) {
                        assert.deepEqual(params, _.merge({}, expectedParams, extraParams), 'The proxyFactory has provided the params through the "callItemAction" event with extra parameters');

                        extraParamsSet = false;
                        proxy.callItemAction(expectedItemUri, expectedAction, expectedParams);
                    } else {
                        assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callItemAction" event without extra parameters');

                        proxy.addCallActionParams(extraParams);
                        extraParamsSet = true;
                        proxy.callTestAction(expectedAction, expectedParams);
                    }
                })
                .on('callTestAction', function(p, action, params) {

                    assert.equal(action, expectedAction, 'The proxyFactory has provided the action through the "callTestAction" event');

                    if (extraParamsSet) {
                        assert.deepEqual(params, _.merge({}, expectedParams, extraParams), 'The proxyFactory has provided the params through the "callTestAction" event with extra parameters');

                        extraParamsSet = false;
                        proxy.callTestAction(expectedAction, expectedParams);
                    } else {
                        assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "callTestAction" event without extra parameters');

                        proxy.addCallActionParams(extraParams);
                        extraParamsSet = true;
                        proxy.submitItem(expectedItemUri, expectedState, expectedResponse, expectedParams);
                    }
                })
                .on('submitItem', function(promise, uri, state, response, params) {
                    assert.ok(promise instanceof Promise, 'The proxyFactory has provided the promise through the "submitItem" event');
                    assert.equal(uri, expectedItemUri, 'The proxyFactory has provided the URI through the "submitItem" event');
                    assert.equal(state, expectedState, 'The proxyFactory has provided the state through the "submitItem" event');
                    assert.equal(response, expectedResponse, 'The proxyFactory has provided the response through the "submitItem" event');

                    if (extraParamsSet) {
                        assert.deepEqual(params, _.merge({}, expectedParams, extraParams), 'The proxyFactory has provided the params through the "submitItem" event with extra parameters');

                        extraParamsSet = false;
                        proxy.submitItem(expectedItemUri, expectedState, expectedResponse, expectedParams);
                    } else {
                        assert.deepEqual(params, expectedParams, 'The proxyFactory has provided the params through the "submitItem" event without extra parameters');

                        QUnit.start();
                    }
                });

            proxy.addCallActionParams(extraParams);
            extraParamsSet = true;
            proxy.callItemAction(expectedItemUri, expectedAction, expectedParams);
        });
    });


    QUnit.test('proxyFactory.getTokenHandler', function(assert) {

        proxyFactory.registerProvider('default', defaultProxy);

        var proxy = proxyFactory('default');

        var securityToken = proxy.getTokenHandler();

        QUnit.expect(3);

        assert.equal(typeof securityToken, 'object', 'The proxy has built a securityToken handler');
        assert.equal(typeof securityToken.getToken, 'function', 'The securityToken handler has a getToken method');
        assert.equal(typeof securityToken.setToken, 'function', 'The securityToken handler has a setToken method');

    });


    QUnit.asyncTest('proxyFactory.hasCommunicator', function(assert) {
        QUnit.expect(7);

        var expectedCommunicator = {
            on: function() {
                return this;
            },
            before : function(){
                return this;
            },
            init: function() {
                assert.ok(true, 'The communicator is initialized');
                return Promise.resolve();
            },
            open: function() {
                assert.ok(true, 'The communicator is open');
                return Promise.resolve();
            },
            destroy: function() {
                assert.ok(true, 'The communicator must be destroyed when the proxy is destroying');
                return Promise.resolve();
            }
        };

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            destroy: function() {
                return Promise.resolve();
            },
            loadCommunicator: function() {
                return expectedCommunicator;
            }
        });

        var proxy = proxyFactory('communicator');

        assert.equal(proxy.hasCommunicator(), false, 'No communicator has been requested before init');

        proxy.init().then(function () {

            assert.equal(proxy.hasCommunicator(), false, 'No communicator has been requested at init');

            proxy.getCommunicator().then(function(communicator) {
                assert.equal(communicator, expectedCommunicator, 'The proxy has built a communicator handler');

                assert.equal(proxy.hasCommunicator(), true, 'A communicator has been requested');

                proxy.destroy()
                    .then(function() {
                        QUnit.start();
                });
            });
        });
    });


    QUnit.asyncTest('proxyFactory.getCommunicator', function(assert) {
        QUnit.expect(6);

        var expectedCommunicator = {
            on: function() {
                return this;
            },
            before : function(){
                return this;
            },
            init: function() {
                assert.ok(true, 'The communicator is initialized');
                return Promise.resolve();
            },
            open: function() {
                assert.ok(true, 'The communicator is open');
                return Promise.resolve();
            },
            destroy: function() {
                assert.ok(true, 'The communicator must be destroyed when the proxy is destroying');
                return Promise.resolve();
            }
        };

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            destroy: function() {
                return Promise.resolve();
            },
            loadCommunicator: function() {
                return expectedCommunicator;
            }
        });

        var proxy = proxyFactory('communicator');

        proxy.getCommunicator()
            .then(function() {
                assert.ok(false, 'The proxy must be initialized');
            })
            .catch(function() {
                assert.ok(true, 'The proxy must be initialized');
            });

        proxy.init().then(function () {
            proxy.getCommunicator().then(function(communicator) {
                assert.equal(communicator, expectedCommunicator, 'The proxy has built a communicator handler');

                proxy.getCommunicator().then(function(communicator) {
                    assert.equal(communicator, expectedCommunicator, 'The proxy returned the already built communicator handler');

                    proxy.destroy()
                        .then(function() {
                            QUnit.start();
                        });
                });
            });
        });
    });

    QUnit.asyncTest('proxyFactory.getCommunicator #failed to open', function(assert) {
        QUnit.expect(5);

        var expectedCommunicator = {
            on: function() {
                return this;
            },
            before : function(){
                return this;
            },
            init: function() {
                assert.ok(true, 'The communicator is initialized');
                return Promise.resolve();
            },
            open: function() {
                assert.ok(true, 'The communicator is not open');
                return Promise.reject();
            },
            destroy: function() {
                assert.ok(true, 'The communicator must be destroyed when the proxy is destroying');
                return Promise.resolve();
            }
        };

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            destroy: function() {
                return Promise.resolve();
            },
            loadCommunicator: function() {
                return expectedCommunicator;
            }
        });

        var proxy = proxyFactory('communicator');
        proxy.init().then(function () {
            proxy.getCommunicator().catch(function() {
                assert.ok(true, 'The proxy has failed to build a communicator handler');
                proxy.destroy()
                    .then(function() {
                        assert.ok(true, 'The proxy has been destroyed');
                        QUnit.start();
                    });
            });
        });
    });


    QUnit.asyncTest('proxyFactory.getCommunicator #no communicator', function(assert) {
        QUnit.expect(2);

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            loadCommunicator: _.noop,
            destroy: function() {
                return Promise.resolve();
            }
        });

        var proxy = proxyFactory('communicator');
        proxy.init().then(function () {
            proxy.getCommunicator().catch(function() {
                assert.ok(true, 'An error is thrown when the loadCommunicator() does not return any communicator');
                proxy.destroy()
                    .then(function() {
                        assert.ok(true, 'The proxy has been destroyed');
                        QUnit.start();
                    });
            });
        });
    });


    QUnit.asyncTest('proxyFactory.getCommunicator #missing loadCommunicator', function(assert) {
        QUnit.expect(1);

        proxyFactory.registerProvider('default', defaultProxy);

        var proxy = proxyFactory('default');
        proxy.init().then(function () {
            proxy.getCommunicator().catch(function() {
                assert.ok(true, 'An error is thrown when the loadCommunicator() method does not exists');
                QUnit.start();
            });
        });
    });


    QUnit.asyncTest('proxyFactory.getCommunicator #events', function(assert) {
        QUnit.expect(6);
        QUnit.stop(1);

        var expectedCommunicator = eventifier({
            init: function() {
                assert.ok(true, 'The communicator is initialized');
                return Promise.resolve();
            },
            open: function() {
                assert.ok(true, 'The communicator is open');
                return Promise.resolve();
            }
        });

        var expectedError = 'error';
        var expectedResponse = 'Hello';

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            loadCommunicator: function() {
                return expectedCommunicator;
            }
        });

        var proxy = proxyFactory('communicator');

        proxy.init().then(function () {
            proxy
                .on('error', function(error) {
                    assert.equal(error, expectedError, 'The right error has been caught');
                    QUnit.start();
                })
                .on('receive', function(response, context) {
                    assert.equal(response, expectedResponse, 'The right response has been received');
                    assert.equal(context, 'communicator', 'The right context has been set');
                    QUnit.start();
                })
                .getCommunicator().then(function(communicator) {
                    assert.equal(communicator, expectedCommunicator, 'The communicator is built');
                    communicator.trigger('error', expectedError);
                    communicator.trigger('receive', expectedResponse);
                });
        });
    });


    QUnit.asyncTest('proxyFactory.channel', function(assert) {
        QUnit.expect(5);

        var expectedCommunicator = {
            on: function() {
                return this;
            },
            before: function() {
                return this;
            },
            init: function() {
                assert.ok(true, 'The communicator is initialized');
                return Promise.resolve();
            },
            open: function() {
                assert.ok(true, 'The communicator is open');
                return Promise.resolve();
            },
            channel: function(name, handler) {
                assert.equal(name, expectedName, 'The channel is created with the right name');
                assert.equal(handler, expectedHandler, 'The channel is created with the right handler');
                QUnit.start();
            }
        };

        var expectedName = 'myChannel';
        var expectedHandler = function() {};

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            loadCommunicator: function() {
                return expectedCommunicator;
            }
        });

        var proxy = proxyFactory('communicator');

        proxy.init().then(function () {
            assert.equal(proxy.channel(expectedName, expectedHandler), proxy, 'The channel method returns the proxy instance');
        });
    });


    QUnit.asyncTest('proxyFactory.send', function(assert) {
        QUnit.expect(5);

        var expectedCommunicator = {
            on: function() {
                return this;
            },
            before: function() {
                return this;
            },
            init: function() {
                assert.ok(true, 'The communicator is initialized');
                return Promise.resolve();
            },
            open: function() {
                assert.ok(true, 'The communicator is open');
                return Promise.resolve();
            },
            send: function(channel, message) {
                assert.equal(channel, expectedChannel, 'The message is sent using the right channel');
                assert.equal(message, expectedMessage, 'The message is sent with the right content');
                return Promise.resolve();
            }
        };

        var expectedChannel = 'myChannel';
        var expectedMessage = 'Hello';

        proxyFactory.registerProvider('communicator', {
            init: _.noop,
            loadCommunicator: function() {
                return expectedCommunicator;
            }
        });

        var proxy = proxyFactory('communicator');

        proxy.init().then(function () {
            proxy.send(expectedChannel, expectedMessage).then(function() {
                assert.ok(true, 'The message has been sent');
                QUnit.start();
            });
        });
    });


    QUnit.asyncTest('proxyFactory.use#success', function (assert) {
        QUnit.expect(18);

        proxyFactory.registerProvider('default', _.defaults({
            init: function () {
                return Promise.resolve();
            },
            getTestData: function () {
                return Promise.resolve();
            }
        }, defaultProxy));

        var proxy = proxyFactory('default');

        proxy
            .use(function (req, res, next) {
                assert.ok(true, 'The global middleware has been called');
                assert.equal(typeof req, 'object', 'The request object has been provided');
                assert.equal(typeof req.command, 'string', 'The request command has been provided');
                assert.equal(typeof res, 'object', 'The response object has been provided');
                assert.equal(typeof res.status, 'string', 'The response status has been provided');
                assert.equal(res.status, 'success', 'The response has a success status');
                next();
            })
            .use('init', function (req, res, next) {
                assert.ok(true, 'The init middleware has been called');
                assert.equal(typeof req, 'object', 'The request object has been provided');
                assert.equal(typeof req.command, 'string', 'The request command has been provided');
                assert.equal(typeof res, 'object', 'The response object has been provided');
                assert.equal(typeof res.status, 'string', 'The response status has been provided');
                assert.equal(res.status, 'success', 'The response has a success status');
                next();
            })
            .init().then(function () {
                proxy.getTestData().then(function () {
                    QUnit.start();
                });
            });
    });


    QUnit.asyncTest('proxyFactory.use#fail', function (assert) {
        QUnit.expect(12);

        proxyFactory.registerProvider('default', _.defaults({
            init: function () {
                return Promise.reject('error');
            }
        }, defaultProxy));

        var proxy = proxyFactory('default');

        proxy
            .use(function (req, res, next) {
                assert.ok(true, 'The global middleware has been called');
                assert.equal(typeof req, 'object', 'The request object has been provided');
                assert.equal(typeof req.command, 'string', 'The request command has been provided');
                assert.equal(typeof res, 'object', 'The response object has been provided');
                assert.equal(typeof res.status, 'string', 'The response status has been provided');
                assert.equal(res.status, 'error', 'The response has a failed status');
                next();
            })
            .use('init', function (req, res, next) {
                assert.ok(true, 'The init middleware has been called');
                assert.equal(typeof req, 'object', 'The request object has been provided');
                assert.equal(typeof req.command, 'string', 'The request command has been provided');
                assert.equal(typeof res, 'object', 'The response object has been provided');
                assert.equal(typeof res.status, 'string', 'The response status has been provided');
                assert.equal(res.status, 'error', 'The response has a failed status');
                next();
            })
            .init().catch(function () {
                QUnit.start();
            });
    });

    QUnit.test('proxyFactory online/offline', function (assert) {
        var proxy;
        proxyFactory.registerProvider('default', defaultProxy);

        QUnit.expect(6);

        proxy = proxyFactory('default');

        assert.ok(proxy.isOnline(), 'We start online');
        assert.ok(!proxy.isOffline(), 'If we are online, we are not offline');

        proxy.setOffline();

        assert.ok(proxy.isOffline(), 'We are offline');
        assert.ok(!proxy.isOnline(), 'If we are offline, we are not online');

        proxy.setOnline();

        assert.ok(proxy.isOnline(), 'We are online');
        assert.ok(!proxy.isOffline(), 'If we are online, we are not offline');
    });

    QUnit.cases([{
        title : 'null',
        err   : null,
        expected : false
    }, {
        title : 'empty object',
        err   : {},
        expected : false
    }, {
        title : 'server error',
        err   : {
            source : 'network',
            code   : 500
        },
        expected : false
    }, {
        title : 'not found error',
        err   : {
            source : 'network',
            code   : 404,
            sent   : true
        },
        expected : false
    }, {
        title : 'connectivity error',
        err   : {
            source : 'network',
            code   : 0,
            sent   : false
        },
        expected : true
    }]).test('proxyFactory.isConnectivityError', function (data, assert) {
        var proxy;
        proxyFactory.registerProvider('default', defaultProxy);

        QUnit.expect(1);

        proxy = proxyFactory('default');
        assert.equal(proxy.isConnectivityError(data.err), data.expected);
    });
});
