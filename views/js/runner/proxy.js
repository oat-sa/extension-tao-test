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
    'async',
    'core/delegator',
    'core/eventifier',
    'core/promise',
    'core/providerRegistry',
    'core/tokenHandler',
    'core/connectivity'
], function(_, async, delegator, eventifier, Promise, providerRegistry, tokenHandlerFactory, connectivity) {
    'use strict';

    var _defaults = {};

    var _slice = [].slice;

    /**
     * Defines a proxy bound to a particular adapter
     *
     * @param {String} proxyName - The name of the proxy adapter to use in the returned proxy instance
     * @param {Object} [config] - Some optional config depending of implementation,
     *                            this object will be forwarded to the proxy adapter
     * @returns {proxy} - The proxy instance, bound to the selected proxy adapter
     */
    function proxyFactory(proxyName, config) {
        var proxy, delegateProxy, communicator, communicatorPromise;
        var testDataHolder;

        var extraCallParams = {};
        var proxyAdapter    = proxyFactory.getProvider(proxyName);
        var initConfig      = _.defaults(config || {}, _defaults);
        var tokenHandler    = tokenHandlerFactory();
        var middlewares     = {};
        var initialized     = false;
        var onlineStatus    = connectivity.isOnline();


        /**
         * Gets parameters merged with extra parameters
         * @param {Object} [params]
         * @return {Object}
         */
        function getParams(params) {
            var mergedParams = _.merge({}, params, extraCallParams);
            extraCallParams = {};
            return mergedParams;
        }

        /**
         * Gets the aggregated list of middlewares for a particular queue name
         * @param {String} queue - The name of the queue to get
         * @returns {Array}
         */
        function getMiddlewares(queue) {
            var list = middlewares[queue] || [];
            if (middlewares.all) {
                list = list.concat(middlewares.all);
            }
            return list;
        }

        /**
         * Applies the list of registered middlewares onto the received response
         * @param {Object} request - The request descriptor
         * @param {String} request.command - The name of the requested command
         * @param {Object} request.params - The map of provided parameters
         * @param {Object} response The response descriptor
         * @param {String} response.status The status of the response, can be either 'success' or 'error'
         * @param {Object} response.data The full response data
         * @returns {Promise}
         */
        function applyMiddlewares(request, response) {
            // wrap each middleware to provide parameters
            var list = _.map(getMiddlewares(request.command), function(middleware) {
                return function(next) {
                    middleware(request, response, next);
                };
            });

            // apply each middleware in series, then resolve or reject the promise
            return new Promise(function(resolve, reject) {
                async.series(list, function(err) {
                    // handle implicit error from response descriptor
                    if (!err && 'error' === response.status) {
                        err = response.data;
                    }

                    if (err) {
                        reject(err);
                    } else {
                        proxy.trigger('receive', response.data, 'proxy');
                        resolve(response.data);
                    }
                });
            });
        }

        /**
         * Delegates the call to the proxy implementation and apply the middleware.
         *
         * @param {String} fnName - The name of the delegated method to call
         * @returns {Promise} - The delegated method must return a promise
         * @private
         * @throws Error
         */
        function delegate(fnName) {
            var request = {command: fnName, params: _slice.call(arguments, 1)};
            if (!initialized && !_.contains(['install', 'init'], fnName)) {
                return Promise.reject(new Error('Proxy is not properly initialized or has been destroyed!'));
            }
            return delegateProxy.apply(null, arguments)
                .then(function(data) {
                    // If the delegate call succeed the proxy is initialized.
                    // Place this set here to avoid to wrap the init() into another promise.
                    initialized = true;

                    // handle successful request
                    return applyMiddlewares(request, {
                        status: 'success',
                        data: data
                    });
                })
                .catch(function(data) {
                    // handle failed request
                    return applyMiddlewares(request, {
                        status: 'error',
                        data: data
                    });
                });
        }

        /**
         * Defines the test runner proxy
         * @typedef {proxy}
         */
        proxy = eventifier({
            /**
             * Add a middleware
             * @param {String} [command] The command queue in which add the middleware (default: 'all')
             * @param {Function...} callback - A middleware callback. Must accept 3 parameters: request, response, next.
             * @returns {proxy}
             */
            use: function use(command) {
                var queue = command && _.isString(command) ? command : 'all';
                var list = middlewares[queue] || [];
                middlewares[queue] = list;

                _.each(arguments, function(cb) {
                    if (_.isFunction(cb)) {
                        list.push(cb);
                    }
                });
                return this;
            },

            /**
             * Install the proxy.
             * This step let's attach some features before the proxy reallys starts (before init).
             *
             * @param {Map} dataHolder - the test runner data holder
             * @returns {*}
             */
            install: function install(dataHolder) {
                if(dataHolder){
                    testDataHolder = dataHolder;
                }
                return delegate('install', initConfig);
            },

            /**
             * Initializes the proxy
             * @param {Object} [params] - An optional list of parameters
             * @returns {Promise} - Returns a promise. The proxy will be fully initialized on resolve.
             *                      Any error will be provided if rejected.
             * @fires init
             */
            init: function init(params) {
                /**
                 * @event proxy#init
                 * @param {Promise} promise
                 * @param {Object} config
                 * @param {Object} params
                 */
                return delegate('init', initConfig, getParams(params));
            },

            /**
             * Uninstalls the proxy
             * @returns {Promise} - Returns a promise. The proxy will be fully uninstalled on resolve.
             *                      Any error will be provided if rejected.
             * @fires destroy
             */
            destroy: function destroy() {
                /**
                 * @event proxy#destroy
                 * @param {Promise} promise
                 */
                return delegate('destroy').then(function() {
                    // The proxy is now destroyed. A call to init() is mandatory to be able to use it again.
                    initialized = false;

                    // a communicator has been invoked and...
                    if (communicatorPromise) {
                        return new Promise(function(resolve, reject) {

                            function destroyCommunicator() {
                                communicator.destroy()
                                    .then(resolve)
                                    .catch(reject);
                            }

                            communicatorPromise
                                // ... has been loaded successfully, then destroy it
                                .then(function() {
                                    destroyCommunicator();
                                })
                                // ...has failed to be loaded, maybe no need to destroy it
                                .catch(function() {
                                    if (communicator) {
                                        destroyCommunicator();
                                    } else {
                                        resolve();
                                    }
                                });
                        });
                    }
                });
            },

            /**
             * Get the map that holds the test data
             * @returns {Map|Object} the dataHolder
             */
            getDataHolder : function getDataHolder(){
                return testDataHolder;
            },

            /**
             * Set the proxy as online
             * @returns {proxy} chains
             * @fires {proxy#reconnect}
             */
            setOnline : function setOnline(){
                if(this.isOffline()){
                    onlineStatus = true;
                    this.trigger('reconnect');
                }
                return this;
            },

            /**
             * Set the proxy as offline
             * @param {String} [source] - source of the connectivity change
             * @returns {proxy} chains
             * @fires {proxy#disconnect}
             */
            setOffline : function setOffline(source){
                if(this.isOnline()){
                    onlineStatus = false;
                    this.trigger('disconnect', source);
                }
                return this;
            },

            /**
             * Are we online ?
             * @returns {Boolean}
             */
            isOnline : function isOnline(){
                return onlineStatus;
            },

            /**
             * Are we offline
             * @returns {Boolean}
             */
            isOffline : function isOffline(){
                return !onlineStatus;
            },

            /**
             * For the proxy a connection error is an error object with
             * source 'network', a 0 code and a false sent attribute.
             *
             * @param {Error|Object} err - the error to verify
             * @returns {Boolean} true if a connection error.
             */
            isConnectivityError : function isConnectivityError(err){
                return _.isObject(err) && err.source === 'network' && err.code === 0 && err.sent === false;
            },

            /**
             * Gets the security token handler
             * @returns {tokenHandler}
             */
            getTokenHandler : function getTokenHandler() {
                return tokenHandler;
            },

            /**
             * Checks if a communication channel has been requested.
             * @returns {Boolean}
             */
            hasCommunicator : function hasCommunicator() {
                return !!communicatorPromise;
            },

            /**
             * Gets access to the communication channel, load it if not present
             * @returns {Promise} Returns a promise that will resolve the communication channel
             */
            getCommunicator : function getCommunicator() {
                var self = this;
                if (!initialized) {
                    return Promise.reject(new Error('Proxy is not properly initialized or has been destroyed!'));
                }
                if (!communicatorPromise) {
                    communicatorPromise = new Promise(function(resolve, reject) {
                        if (_.isFunction(proxyAdapter.loadCommunicator)) {
                            communicator = proxyAdapter.loadCommunicator.call(self);
                            if (communicator) {
                                communicator
                                    .before('error', function(e, err){
                                        if(self.isConnectivityError(err)){
                                            self.setOffline('communicator');
                                        }
                                    })
                                    .on('error', function(err) {
                                        self.trigger('error', err);
                                    })
                                    .on('receive', function(response) {
                                        self.setOnline();
                                        self.trigger('receive', response, 'communicator');
                                    })
                                    .init()
                                    .then(function () {
                                        return communicator.open()
                                            .then(function() {
                                                resolve(communicator);
                                            })
                                            .catch(reject);
                                    })
                                    .catch(reject);
                            } else {
                                reject(new Error('No communicator has been set up!'));
                            }
                        } else {
                            reject(new Error('The proxy provider does not have a loadCommunicator method'));
                        }
                    });
                }
                return communicatorPromise;
            },

            /**
             * Registers a listener on a particular channel
             * @param {String} name - The name of the channel to listen
             * @param {Function} handler - The listener callback
             * @returns {proxy}
             * @throws TypeError if the name is missing or the handler is not a callback
             */
            channel: function channel(name, handler) {
                if (!_.isString(name) || name.length <= 0) {
                    throw new TypeError('A channel must have a name');
                }

                if (!_.isFunction(handler)) {
                    throw new TypeError('A handler must be attached to a channel');
                }

                this.getCommunicator()
                    .then(function(communicatorInstance) {
                        communicatorInstance.channel(name, handler);
                    })
                    // just an empty catch to avoid any error to be displayed in the console when the communicator is not enabled
                    .catch(_.noop);

                this.on('channel-' + name, handler);

                return this;
            },

            /**
             * Sends an messages through the communication implementation.
             * @param {String} channel - The name of the communication channel to use
             * @param {Object} message - The message to send
             * @returns {Promise} The delegated provider's method must return a promise
             */
            send: function send(channel, message) {
                return this.getCommunicator()
                    .then(function(communicatorInstance) {
                        return communicatorInstance.send(channel, message);
                    });
            },

            /**
             * Add extra parameters that will be added to the init or the next callTestAction or callItemAction
             * This enables plugins to place parameters for next calls
             * @param {Object} params - the extra parameters
             * @returns {proxy}
             */
            addCallActionParams : function addCallActionParams(params){
                if(_.isPlainObject(params)){
                    _.merge(extraCallParams, params);
                }
                return this;
            },

            /**
             * Gets the test definition data
             * @returns {Promise} - Returns a promise. The test definition data will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires getTestData
             */
            getTestData: function getTestData() {
                /**
                 * @event proxy#getTestData
                 * @param {Promise} promise
                 */
                return delegate('getTestData');
            },

            /**
             * Gets the test context
             * @returns {Promise} - Returns a promise. The context object will be provided on resolve.
             *                      Any error will be provided if rejected.
             */
            getTestContext: function getTestContext() {
                /**
                 * @event proxy#getTestContext
                 * @param {Promise} promise
                 */
                return delegate('getTestContext');
            },

            /**
             * Gets the test map
             * @returns {Promise} - Returns a promise. The test map object will be provided on resolve.
             *                      Any error will be provided if rejected.
             */
            getTestMap: function getTestMap() {
                /**
                 * @event proxy#getTestMap
                 * @param {Promise} promise
                 */
                return delegate('getTestMap');
            },

            /**
             * Sends the test variables
             * @param {Object} variables
             * @param {Boolean} deferred whether action can be scheduled (put into queue) to be sent in a bunch of actions later (default: false).
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires sendVariables
             */
            sendVariables: function sendVariables(variables, deferred) {
                /**
                 * @event proxy#sendVariables
                 * @param {Promise} promise
                 */
                return delegate('sendVariables', variables, deferred);
            },

            /**
             * Calls an action related to the test
             * @param {String} action - The name of the action to call
             * @param {Object} [params] - Some optional parameters to join to the call
             * @param {Boolean} deferred whether action can be scheduled (put into queue) to be sent in a bunch of actions later.
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires callTestAction
             */
            callTestAction: function callTestAction(action, params, deferred) {
                /**
                 * @event proxy#callTestAction
                 * @param {Promise} promise
                 * @param {String} action
                 * @param {Object} params
                 */
                return delegate('callTestAction', action, getParams(params), deferred);
            },

            /**
             * Gets an item definition by its URI, also gets its current state
             * @param {String} uri - The URI of the item to get
             * @param {Object} [params] - addtional params to be appended
             * @returns {Promise} - Returns a promise. The item data will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires getItem
             */
            getItem: function getItem(uri, params) {
                /**
                 * @event proxy#getItem
                 * @param {Promise} promise
                 * @param {String} uri
                 */
                return delegate('getItem', uri, params);
            },

            /**
             * Submits the state and the response of a particular item
             * @param {String} uri - The URI of the item to update
             * @param {Object} state - The state to submit
             * @param {Object} response - The response object to submit
             * @param {Object} [params] - addtional params to be appended
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires submitItem
             */
            submitItem: function submitItem(uri, state, response, params) {

                /**
                 * @event proxy#submitItem
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {Object} state
                 * @param {Object} response
                 */
                return delegate('submitItem', uri, state, response, getParams(params));
            },

            /**
             * Calls an action related to a particular item
             * @param {String} uri - The URI of the item for which call the action
             * @param {String} action - The name of the action to call
             * @param {Object} [params] - Some optional parameters to join to the call
             * @param {Boolean} deferred whether action can be scheduled (put into queue) to be sent in a bunch of actions later.
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires callItemAction
             */
            callItemAction: function callItemAction(uri, action, params, deferred) {
                /**
                 * @event proxy#callItemAction
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {String} action
                 * @param {Object} params
                 */
                return delegate('callItemAction', uri, action, getParams(params), deferred);
            },

            /**
             * Sends a telemetry signal
             * @param {String} uri - The URI of the item for which sends the telemetry signal
             * @param {String} signal - The name of the signal to send
             * @param {Object} [params] - Some optional parameters to join to the signal
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires telemetry
             */
            telemetry: function telemetry(uri, signal, params) {
                /**
                 * @event proxy#telemetry
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {String} signal
                 * @param {Object} params
                 */
                return delegate('telemetry', uri, signal, params);
            }
        });

        //listen for connectivty changes
        connectivity
            .on('offline', function(){
                proxy.setOffline('device');
            })
            .on('online', function(){
                proxy.setOnline();
            });

        // catch platform messages that come outside of the communicator component, then each is dispatched to the right channel
        proxy
            .on('message', function (channel, message) {
                this.trigger('channel-' + channel, message);
            })
            .use(function(request, response, next) {
                if (response.data && response.data.messages) {
                    // receive server messages
                    _.forEach(response.data.messages, function (msg) {
                        if (msg.channel) {
                            proxy.trigger('message', msg.channel, msg.message);
                        } else {
                            proxy.trigger('message', 'malformed', msg);
                        }
                    });
                }
                next();
            })
            //detect failing request and change the online status
            .use(function(request, response, next){
                if(proxy.isConnectivityError(response.data)){
                    proxy.setOffline('request');
                } else if (response.data && response.data.sent === true){
                    proxy.setOnline();
                }
                next();
            });

        delegateProxy = delegator(proxy, proxyAdapter, {
            name: 'proxy',
            wrapper: function pluginWrapper(response){
                return Promise.resolve(response);
            }
        });

        return proxy;
    }

    return providerRegistry(proxyFactory);
});
