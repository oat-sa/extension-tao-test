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
    'jquery',
    'lodash',
    'i18n',
    'core/eventifier',
    'taoTests/runner/proxyRegistry'
], function($, _, __, eventifier, proxyRegistry) {
    'use strict';

    var _defaults = {};

    /**
     *
     * @param {String} proxyName
     * @param {Object} config
     * @returns {proxy}
     */
    function proxyFactory(proxyName, config) {
        var proxyAdapter = proxyFactory.getProxy(proxyName);
        var initConfig = _.defaults(config || {}, _defaults);

        /**
         * Delegate a function call to the selected proxy
         *
         * @param {String} fnName
         * @param {Array} [args] - array of arguments to apply to the method
         * @private
         * @returns {undefined}
         */
        function delegate(fnName, args) {
            if (proxyAdapter) {
                if (_.isFunction(proxyAdapter[fnName])) {
                    return proxyAdapter[fnName].apply(proxy, _.isArray(args) ? args : []);
                }
            }
        }

        /**
         * Defines the test runner proxy
         * @type {proxy}
         */
        var proxy = eventifier({
            /**
             * Initializes the proxy
             * @returns {proxy}
             * @fires init
             */
            init: function init() {
                delegate('init', [initConfig]);

                /**
                 * @event proxy#init
                 * @param {Object} config
                 */
                this.trigger('init', initConfig);

                return this;
            },

            /**
             * Uninstalls the proxy
             * @returns {proxy}
             * @fires destroy
             */
            destroy: function destroy() {
                delegate('destroy');

                /**
                 * @event proxy#destroy
                 */
                this.trigger('destroy');

                return this;
            },

            /**
             * Gets an item by its URI
             * @param string uri - The URI of the item to get
             * @returns {Promise} - Returns a promise that will be resolved with the item data
             * @fires getItem
             */
            getItem: function getItem(uri) {
                var promise = delegate('getItem', [uri]);

                /**
                 * @event proxy#getItem
                 * @param {String} uri
                 * @param {Promise} promise
                 */
                this.trigger('getItem', uri, promise);

                return promise;
            },

            /**
             * Submits the current item state
             * @param {String} uri - The URI of the item to update
             * @param {Object} state
             * @returns {Promise}
             * @fires submitItemState
             */
            submitItemState: function submitItemState(uri, state) {
                var promise = delegate('submitItemState', [uri, state]);

                /**
                 * @event proxy#submitItemState
                 * @param {String} uri
                 * @param {Object} state
                 * @param {Promise} promise
                 */
                this.trigger('submitItemState', uri, state, promise);

                return promise;
            },

            /**
             * Stores the current item response
             * @param {String} uri - The URI of the item to update
             * @param {Object} response
             * @returns {Promise}
             * @fires storeItemResponse
             */
            storeItemResponse: function storeItemResponse(uri, response) {
                var promise = delegate('storeItemResponse', [uri, response]);

                /**
                 * @event proxy#storeItemResponse
                 * @param {String} uri
                 * @param {Object} response
                 * @param {Promise} promise
                 */
                this.trigger('storeItemResponse', uri, response, promise);

                return promise;
            },

            /**
             * Calls a particular action
             * @param {String} uri - The URI of the item to update
             * @param {String} action
             * @param {Object} params
             * @returns {Promise}
             * @fires actionCall
             */
            actionCall: function actionCall(uri, action, params) {
                var promise = delegate('actionCall', [uri, action, params]);

                /**
                 * @event proxy#actionCall
                 * @param {String} uri
                 * @param {String} action
                 * @param {Object} params
                 * @param {Promise} promise
                 */
                this.trigger('actionCall', uri, action, params, promise);

                return promise;
            }

        });

        return proxy;
    }

    return proxyRegistry(proxyFactory);
});
