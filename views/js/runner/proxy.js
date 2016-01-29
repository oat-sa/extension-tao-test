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
 */define([
    'lodash',
    'core/eventifier',
    'taoTests/runner/proxyRegistry'
], function(_, eventifier, proxyRegistry) {
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
        var proxyAdapter = proxyFactory.getProxy(proxyName);
        var initConfig = _.defaults(config || {}, _defaults);

        /**
         * Delegates a function call to the selected proxy.
         * Fires the related event
         *
         * @param {String} fnName - The name of the delegated method to call
         * @param {Array} [args] - An optional array of arguments to apply to the method
         * @returns {Promise} - The delegated method must return a promise
         * @private
         * @throws Error
         */
        function delegate(fnName, args) {
            var promise;

            if (proxyAdapter) {
                if (_.isFunction(proxyAdapter[fnName])) {
                    // need real array of params, even if empty
                    args = args ? _slice.call(args) : [];

                    // delegate the call to the adapter
                    promise = proxyAdapter[fnName].apply(proxy, args);

                    // fire the method related event
                    // the promise has to be provided as first argument in all events
                    proxy.trigger.apply(proxy, [fnName, promise].concat(args));
                } else {
                    throw new Error('There is no method called ' + fnName + ' in the proxy adapter!');
                }
            } else {
                throw new Error('There is no proxy adapter!');
            }

            return promise;
        }

        /**
         * Defines the test runner proxy
         * @type {proxy}
         */
        var proxy = eventifier({
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
                return delegate('init', [initConfig]);
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
                return delegate('destroy');
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
                /**
                 * @event proxy#callTestAction
                 * @param {Promise} promise
                 * @param {String} action
                 * @param {Object} params
                 */
                return delegate('callTestAction', [action, params]);
            },

            /**
             * Gets an item definition by its URI
             * @param {String} uri - The URI of the item to get
             * @returns {Promise} - Returns a promise. The item definition data will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires getItemData
             */
            getItemData: function getItemData(uri) {
                /**
                 * @event proxy#getItemData
                 * @param {Promise} promise
                 * @param {String} uri
                 */
                return delegate('getItemData', [uri]);
            },

            /**
             * Gets an item state by the item URI
             * @param {String} uri - The URI of the item for which get the state
             * @returns {Promise} - Returns a promise. The item state object will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires getItemState
             */
            getItemState: function getItemState(uri) {
                /**
                 * @event proxy#getItemState
                 * @param {Promise} promise
                 * @param {String} uri
                 */
                return delegate('getItemState', [uri]);
            },

            /**
             * Submits the state of a particular item
             * @param {String} uri - The URI of the item to update
             * @param {Object} state - The state to submit
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires submitItemState
             */
            submitItemState: function submitItemState(uri, state) {
                /**
                 * @event proxy#submitItemState
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {Object} state
                 */
                return delegate('submitItemState', [uri, state]);
            },

            /**
             * Stores the response for a particular item
             * @param {String} uri - The URI of the item to update
             * @param {Object} response - The response object to submit
             * @returns {Promise} - Returns a promise. The result of the request will be provided on resolve.
             *                      Any error will be provided if rejected.
             * @fires storeItemResponse
             */
            storeItemResponse: function storeItemResponse(uri, response) {
                /**
                 * @event proxy#storeItemResponse
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {Object} response
                 */
                return delegate('storeItemResponse', [uri, response]);
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
                /**
                 * @event proxy#callItemAction
                 * @param {Promise} promise
                 * @param {String} uri
                 * @param {String} action
                 * @param {Object} params
                 */
                return delegate('callItemAction', [uri, action, params]);
            }
        });

        return proxy;
    }

    return proxyRegistry(proxyFactory);
});
