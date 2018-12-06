/*
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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 */

/**
 * The test runner persistent data store,
 * to be used with sub stores.
 *
 * Supports the legacy mode where multiple stores were used by each plugin.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/promise',
    'core/store',
    'core/logger'
], function (_, Promise, store, loggerFactory) {
    'use strict';

    /**
     * The test store logger
     * @type {core/logger}
     */
    var logger = loggerFactory('taoQtiTest/runner/provider/testStore');

    /**
     * Database name prefix (suffixed by the test identifier)
     * to check if we use the fragmented mode
     * or the unified mode.
     * @type {String[]}
     */
    var legacyPrefixes = [
        'actions-', 'duration-', 'test-probe', 'timer-'
    ];

    /**
     * List the available modes
     */
    var modes = {
        unified    : 'unified',         //one db per test, new mode
        fragmented : 'fragmented'       //mutliple dbs per test, legacy mode
    };

    /**
     * Check and select the store mode.
     * If any of the "legacyPrefixes" store is found, we used the fragmented mode
     * otherwise we'll use the unified mode.
     * @param {String} testId
     * @param {Object} [preselectedBackend] - the storage backend
     * @returns {Promise<String>} resolves with the mode of the current test
     */
    var selectStoreMode = function selectStoreMode(testId, preselectedBackend){
        return store
            .getAll(function validate(storeName){
                return _.some(legacyPrefixes, function(prefix){
                    return !_.isEmpty(storeName) && prefix + testId === storeName;
                });
            }, preselectedBackend)
            .then(function(foundStores){
                if(_.isArray(foundStores) && foundStores.length > 0 ){
                    return modes.fragmented;
                }
                return modes.unified;
            });
    };


    /**
     * Get the store for the given test
     *
     * @param {String} testId - unique test instance id
     * @returns {testStore} a 'wrapped' store instance
     * @param {Object} [preselectedBackend] - the storage backend (automatically selected by default)
     * @throws {TypeError} without a testId
     */
    return function testStoreLoader(testId, preselectedBackend){

        var storeNames = [];
        var volatiles  = [];
        var changeTracking = {};
        var testMode;

        /**
         * Is the test using a unified store mode ?
         * @returns {Promise<Boolean>} true if unified
         */
        var isStoreModeUnified = function isStoreModeUnified(){
            if(_.isUndefined(testMode)){
                return selectStoreMode(testId, preselectedBackend).then(function(result){
                    if(result && typeof modes[result] !== 'undefined'){
                        testMode = result;
                    } else {
                        //use the unified mode by default
                        testMode = modes.unified;
                    }

                    logger.debug('Test store mode ' + result + ' for ' + testId);
                    return result === modes.unified;
                });
            }
            return Promise.resolve(testMode === modes.unified);
        };

        if(_.isEmpty(testId)){
            throw new TypeError('The store must be identified with a unique test identifier');
        }

        /**
         * Wraps a store and add the support of "volatile" storages
         * @typedef {Object} testStore
         */
        return {

            /**
             * Get a wrapped store instance, that let's you use multiple stores inside one store...
             * (or in multiple stores if the test is in legacy mode)
             * @param {String} storeName - the name of the sub store
             * @returns {Promise<storage>}
             */
            getStore : function getStore(storeName) {

                //call when the current storge has been changed
                //only if the store is set to track changes
                var trackChange = function trackChange(){
                    if(_.isBoolean(changeTracking[storeName])){
                        changeTracking[storeName] = true;
                    }
                };

                if(_.isEmpty(storeName)){
                    throw new TypeError('A store name must be provided to get the store');
                }

                if(!_.contains(storeNames, storeName)){
                    storeNames.push(storeName);
                }
                return isStoreModeUnified().then(function(isUnified){
                    var loadStore;
                    if (isUnified) {
                        loadStore = store(testId, preselectedBackend);
                    } else {
                        loadStore = store(storeName + '-' + testId, preselectedBackend);
                    }

                    return loadStore.then(function(loadedStore){
                        var keyPattern = new RegExp('^' + storeName + '__');
                        var storeKey = function storeKey(key){
                            return isUnified ? storeName + '__' + key : key;
                        };

                        /**
                         * The wrapped storage
                         * @type {Object}
                         */
                        return {


                            /**
                             * Get an item with the given key
                             * @param {String} key
                             * @returns {Promise<*>} with the result in resolve, undefined if nothing
                             */
                            getItem : function getItem(key){
                                return loadedStore.getItem(storeKey(key));
                            },

                            /**
                             * Get all store items
                             * @returns {Promise<Object>} with a collection of items
                             */
                            getItems : function getItems(){
                                if(isUnified){
                                    return loadedStore.getItems().then(function(entries){
                                        return _.transform(entries, function(acc, entry, key){
                                            if(keyPattern.test(key)){
                                                acc[key.replace(keyPattern, '')] = entry;
                                            }
                                            return acc;
                                        }, {});
                                    });
                                } else {
                                    return loadedStore.getItems();
                                }
                            },

                            /**
                             * Set an item with the given key
                             * @param {String} key - the item key
                             * @param {*} value - the item value
                             * @returns {Promise<Boolean>} with true in resolve if added/updated
                             */
                            setItem : function setItem(key, value){
                                trackChange();
                                return loadedStore.setItem(storeKey(key), value);
                            },

                            /**
                             * Remove an item with the given key
                             * @param {String} key - the item key
                             * @returns {Promise<Boolean>} with true in resolve if removed
                             */
                            removeItem : function removeItem(key){
                                trackChange();
                                return loadedStore.removeItem(storeKey(key));
                            },

                            /**
                             * Clear the current store
                             * @returns {Promise<Boolean>} with true in resolve once cleared
                             */
                            clear : function clear(){
                                trackChange();
                                if(isUnified){
                                    return loadedStore.getItems()
                                        .then(function(entries){
                                            _.forEach(entries, function(entry, key){
                                                if(keyPattern.test(key)){
                                                    loadedStore.removeItem(key);
                                                }
                                            });
                                        });
                                } else {
                                    return loadedStore.clear();
                                }
                            }
                        };
                    });
                });
            },

            /**
             * Define the given store as "volatile".
             * It means the store data can be revoked
             * if the user change browser for example
             * @param {String} storeName - the name of the store to set as volatile
             * @returns {testStore} chains
             */
            setVolatile : function setVolatile(storeName){
                if(!_.contains(volatiles, storeName)){
                    volatiles.push(storeName);
                }
                return this;
            },

            /**
             * Check the given storeId. If different from the current stored identifier
             * we initiate the invalidation of the volatile data.
             * @param {String} storeId - the id to check
             * @returns {Promise<Boolean>} true if cleared
             */
            clearVolatileIfStoreChange : function clearVolatileIfStoreChange(storeId){
                var self = this;
                var shouldClear = false;
                return store.getIdentifier(preselectedBackend)
                    .then(function(savedStoreId){
                        if (!_.isEmpty(storeId) && !_.isEmpty(savedStoreId) &&
                            savedStoreId !== storeId ){

                            logger.info('Storage change detected (' + savedStoreId + ' != ' + storeId + ') => volatiles data wipe out !');
                            shouldClear = true;
                        }
                        return shouldClear;
                    })
                    .then(function(clear){
                        if(clear){
                            return self.clearVolatileStores();
                        }
                        return false;
                    });
            },

            /**
             * Clear the storages marked as volatile
             * @returns {Promise<Boolean>} true if cleared
             */
            clearVolatileStores : function clearVolatileStores(){
                var self = this;
                var clearing = volatiles.map(function(storeName){
                    return self.getStore(storeName).then(function(storeInstance){
                        return storeInstance.clear();
                    });
                });

                return Promise.all(clearing).then(function(results){
                    return results && results.length === volatiles.length;
                });
            },

            /**
             * Observe changes on the given store
             *
             * @param {String} storeName - the name of the store to observe
             * @returns {testStore} chains
             */
            startChangeTracking : function startChangeTracking(storeName){
                changeTracking[storeName] = false;
                return this;
            },

            /**
             * Has the store some changes
             *
             * @param {String} storeName - the name of the store to set as volatile
             * @returns {Boolean} true if the given store has some changes
             */
            hasChanges : function hasChanges(storeName){
                return changeTracking[storeName] === true;
            },

            /**
             * Reset the change listening
             *
             * @param {String} storeName - the name of the store
             * @returns {testStore} chains
             */
            resetChanges : function resetChanges(storeName){
                if(_.isBoolean(changeTracking[storeName])){
                    changeTracking[storeName] = false;
                }
                return this;
            },

            /**
             * Remove the whole store
             * @returns {Promise<Boolean>} true if done
             */
            remove : function remove(){
                var legacyStoreExp = new RegExp('-' + testId + '$');
                return isStoreModeUnified().then(function(isUnified){
                    if(isUnified){
                        return store(testId, preselectedBackend).then(function(storeInstance){
                            return storeInstance.removeStore();
                        });
                    }
                    return store.removeAll(function(storeName){
                        return legacyStoreExp.test(storeName);
                    }, preselectedBackend);
                });
            },

            /**
             * Wraps the identifier retrieval
             * @returns {Promise<String>} the current store id
             */
            getStorageIdentifier : function getStorageIdentifier(){
                return store.getIdentifier(preselectedBackend);
            }
        };
    };
});
