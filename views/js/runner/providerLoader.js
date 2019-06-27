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
     *
     */
    return function loadTestRunnerProviders(providers = {}, loadFromBundle = false) {

        const loadAndRegisterProvider = (providersToLoad = [], target) => {
            if(!target || typeof target.registerProvider !== 'function'){
                throw new TypeError('Trying to register providers on a target that is not a provider API');
            }
            return providerLoader()
                    .addList(providersToLoad)
                    .load(loadFromBundle)
                    .then( loadedProviders => loadedProviders.map( provider => target.registerProvider(provider.name, provider) ) );
        };

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

                    logger.debug(`Start to load and registrer the '${providerType}' providers`);

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
