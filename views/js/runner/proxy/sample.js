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
    'core/promise'
], function(_, Promise) {
    'use strict';

    /**
     * Sample proxy definition
     * @type {Object}
     */
    var sampleProxy = {
        /**
         * Initializes the proxy
         * @param {Object} config - The config provided to the proxy factory
         */
        init: function init(config) {
            // do initialisation
        },

        /**
         * Uninstalls the proxy
         */
        destroy: function destroy() {
            // do uninstall actions
        },

        /**
         * Gets an item by its URI
         * @param string uri - The URI of the item to get
         * @returns {Promise} - Returns a promise that will be resolved with the item data
         */
        getItem: function getItem(uri) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // get the item data
                // once the item is loaded provide the data by resolving the promise
                resolve(/* the item data */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Submits the current item state
         * @param {String} uri - The URI of the item to update
         * @param {Object} state
         * @returns {Promise}
         */
        submitItemState: function submitItemState(uri, state) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // submit the item state

                // once the state has been processed notify the success by resolving the promise
                resolve(/* the action response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Stores the current item response
         * @param {String} uri - The URI of the item to update
         * @param {Object} response
         * @returns {Promise}
         */
        storeItemResponse: function storeItemResponse(uri, response) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // store the item response

                // once the response has been stored notify the success by resolving the promise
                resolve(/* the action response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        },

        /**
         * Calls a particular action
         * @param {String} uri - The URI of the item to update
         * @param {String} action
         * @param {Object} params
         * @returns {Promise}
         */
        actionCall: function actionCall(uri, action, params) {
            // the method must return a promise
            return new Promise(function(resolve, reject) {
                // call the action

                // once the action has been processed notify the success by resolving the promise
                resolve(/* the action response */);

                // you can also notify error by rejecting the promise
                // reject(error);
            });
        }

    };

    return sampleProxy;
});
