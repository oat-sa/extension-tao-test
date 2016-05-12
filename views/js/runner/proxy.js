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
    'core/tokenHandler'
], function(_, async, delegator, eventifier, Promise, providerRegistry, tokenHandlerFactory) {
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

        var extraCallParams = {};
        var proxyAdapter    = proxyFactory.getProvider(proxyName);
        var initConfig      = _.defaults(config || {}, _defaults);
        var tokenHandler    = tokenHandlerFactory();
        var middlewares     = {};
        var delegateProxy, communicator;

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
            return delegateProxy.apply(null, arguments)
                .then(function(data) {
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
         * @type {proxy}
         */
        var proxy = eventifier({
            /**
             * Add a middleware
             * @param {String} [command] The command queue in which add the middleware (default: 'all')
             * @param {Function} callback A middleware callback. Must accept 3 parameters: request, response, next.
             * @returns {proxy}
             */
            use: function use(command, callback) {
                var queue = command && _.isString(command) ? command : 'all';
                var list = middlewares[queue] || [];
                middlewares[queue] = list;

                _.each(arguments, function(callback) {
                    if (_.isFunction(callback)) {
                        list.push(callback);
                    }
                });
                return this;
            },

            /**
             * Initializes the proxy
             * @returns {Promise} - Returns a promise. The proxy will be fully initialized on resolve.
             *                      Any error will be provided if rejected.
             * @fires init
             */
            init: function init() {
                /**
                 * @event proxy#init
                 * @param {Promise} promise
                 * @param {Object} config
                 */
                return delegate('init', initConfig);
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
                    if (communicator) {
                        return communicator.destroy();
                    }
                });
            },

            /**
             * Gets the security token handler
             * @returns {tokenHandler}
             */
            getTokenHandler : function getTokenHandler() {
                return tokenHandler;
            },

            /**
             * Gets access to the communication channel, load it if not present
             * @returns {communicator} the communication channel
             */
            getCommunicator : function getCommunicator() {
                if(!communicator){
                    if(!_.isFunction(proxyAdapter.loadCommunicator)){
                        throw new Error('The proxy provider does not have a loadCommunicator method');
                    }
                    communicator = proxyAdapter.loadCommunicator.call(this);
                }
                return communicator;
            },

            /**
             * Add extra parameters that will be added to the next callTestAction or callItemAction
             * This enables plugins to place parameters for next calls
             * @param {Object} params - the extra parameters
             */
            addCallActionParams : function addCallActionParams(params){
                if(_.isPlainObject(params)){
                    _.merge(extraCallParams, params);
                }
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
             * Calls an action related to the test
             * @param {String} action - The name of the action to call
             * @param {Object} [params] - Some optional parameters to join to the call
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires callTestAction
             */
            callTestAction: function callTestAction(action, params) {

                //merge extra parameters
                var mergedParams = _.merge({}, params, extraCallParams);
                extraCallParams = {};

                /**
                 * @event proxy#callTestAction
                 * @param {Promise} promise
                 * @param {String} action
                 * @param {Object} params
                 */
                return delegate('callTestAction', action, mergedParams);
            },

            /**
             * Gets an item definition by its URI, also gets its current state
             * @param {String} uri - The URI of the item to get
             * @returns {Promise} - Returns a promise. The item data will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires getItem
             */
            getItem: function getItem(uri) {
                /**
                 * @event proxy#getItem
                 * @param {Promise} promise
                 * @param {String} uri
                 */
                return delegate('getItem', uri);
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
                return delegate('submitItem', uri, state, response, params);
            },

            /**
             * Calls an action related to a particular item
             * @param {String} uri - The URI of the item for which call the action
             * @param {String} action - The name of the action to call
             * @param {Object} [params] - Some optional parameters to join to the call
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires callItemAction
             */
            callItemAction: function callItemAction(uri, action, params) {

                //merge extra parameters
                var mergedParams = _.merge({}, params, extraCallParams);
                extraCallParams = {};

                /**
                 * @event proxy#callItemAction
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {String} action
                 * @param {Object} params
                 */
                return delegate('callItemAction', uri, action, mergedParams);
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

        delegateProxy = delegator(proxy, proxyAdapter, {name: 'proxy'});

        return proxy;
    }

    return providerRegistry(proxyFactory);
});
