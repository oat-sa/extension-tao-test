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
 * Copyright (c) 2019 Open Assessment Technologies SA ;
 */

/**
 * Loads all the required providers and the underlying modules
 * required by the test runner.
 *
 *
 * @example
 * providerLoader({
 *    "runner": {
 *      "id": "mock-runner",
 *      "module": "taoTests/test/runner/mocks/mockRunnerProvider",
 *      "bundle": "taoTests/test/runner/mocks/mockBundle.min",
 *      "category": "testrunner"
 *    },
 *    "proxy": {
 *      "id": "mock-proxy",
 *      "module": "taoTests/test/runner/mocks/mockProxyProvider",
 *      "bundle": "taoTests/test/runner/mocks/mockBundle.min",
 *      "category": "online"
 *    },
 *    "communicator": {
 *      "id": "request",
 *      "module": "core/communicator/request",
 *      "bundle": "loader/vendor.min",
 *      "category": "request"
 *    },
 *    "plugins": [{
 *      "id": "fooglin",
 *      "module": "taoTests/test/runner/mocks/mockPlugin1",
 *      "bundle": "taoTests/test/runner/mocks/mockBundle.min",
 *      "category": "content"
 *    }, {
 *      "id": "barglin",
 *      "module": "taoTests/test/runner/mocks/mockPlugin2",
 *      "bundle": "taoTests/test/runner/mocks/mockBundle.min",
 *      "category": "tools"
 *    }]
 *  }, false)
 *  .then( ({ runnerProvider, plugins }) => {
 *      //...
 *  })
 *  .catch( err => console.error(err) );
 *
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'core/logger',
    'core/providerLoader',
    'core/pluginLoader',
    'core/communicator',
    'taoTests/runner/runner',
    'taoTests/runner/proxy',
], function(loggerFactory, providerLoader, pluginLoader, communicator, runner, proxy){
    'use strict';

    const logger = loggerFactory('taoTests/runner/loader');

    /**
     * Load the providers that match the registration
     * @param {Object} providers
     * @param {Object|Object[]} providers.runner
     * @param {Object|Object[]} [providers.proxy]
     * @param {Object|Object[]} [providers.communicator]
     * @param {Object|Object[]} [providers.plugins]
     * @param {Boolean} loadFromBundle - does the loader load the modules from the sources (dev mode) or the bundles
     * @returns {Promise<Object>} resolves with the loaded providers per provider type
     */
    return function loadTestRunnerProviders(providers = {}, loadFromBundle = false) {

        /**
         * Default way to load the modules and register the providers
         * @param {Object[]} providersToLoad - the list of providers
         * @param {Object} target - a provider target (an object that use the providers), it needs to expose registerProvider
         * @returns {Promise<Object>} resolves with the target
         * @throws {TypeError} if the target is not a provider target
         */
        const loadAndRegisterProvider = (providersToLoad = [], target) => {
            if(!target || typeof target.registerProvider !== 'function'){
                throw new TypeError('Trying to register providers on a target that is not a provider API');
            }
            return providerLoader()
                    .addList(providersToLoad)
                    .load(loadFromBundle)
                    .then( loadedProviders => {
                        loadedProviders.forEach( provider => target.registerProvider(provider.name, provider));
                        return target;
                    });
        };

        /**
         * Available provider registration
         */
        const registration = {
            runner(runnerProviders = []){
                return loadAndRegisterProvider(runnerProviders, runner);
            },
            communicator(communicatorProviders = []){
                return loadAndRegisterProvider(communicatorProviders, communicator);
            },
            proxy(proxyProviders = []){
                return loadAndRegisterProvider(proxyProviders, proxy);
            },
            plugins(plugins = []){
                return pluginLoader()
                    .addList(plugins)
                    .load(loadFromBundle);
            }
        };

        if (!loadFromBundle) {
            logger.warn('All modules will be loaded from sources');
        }

        return Promise.all(
            Object.keys(providers).map( providerType => {

                if (typeof registration[providerType] === 'function') {

                    logger.debug(`Start to load and register the '${providerType}' providers`);

                    const providersToLoad = Array.isArray(providers[providerType]) ? providers[providerType] : [providers[providerType]];

                    return registration[providerType](providersToLoad)
                        .then( loaded => {
                            logger.debug(`'${providerType}' providers are loaded and registered`);
                            return { [providerType] : loaded };
                        });
                } else {
                    logger.warn(`Ignoring the '${providerType}' providers loading, no registration method found`);
                }
            })
        )
        .then( results => results.reduce( (acc, value) => Object.assign(acc, value), {} ) )
        .catch( err => {
            logger.error(`Error in test runner providers and plugins loading : ${err.message}`);

            throw err;
        });
    };

});
