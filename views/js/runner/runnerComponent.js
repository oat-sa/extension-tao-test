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
 * Copyright (c) 2017-2019 (original work) Open Assessment Technologies SA ;
 */

/**
 * A component that loads and instantiate a test runner inside an element
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'ui/component',
    'taoTests/runner/runner',
    'taoTests/runner/providerLoader',
    'tpl!taoTests/template/runnerComponent'
], function ($, _, component, runnerFactory, providerLoader, runnerComponentTpl) {
    'use strict';

    /**
     * Validate required options from the configuration
     * @param {Object} config
     * @returns {Boolean} true if valid
     * @throws {TypeError} in case of validation failure
     */
    function validateTestRunnerConfiguration(config = {}){
        const requiredProperties = ['providers', 'options', 'serviceCallId'];
        if (typeof config !== 'object') {
            throw new TypeError(`The runner configuration must be an object, '${typeof config}' received`);
        }
        if (_.some(requiredProperties, property => (typeof config[property] === 'undefined'))) {

            throw new TypeError(`The runner configuration must contains at least the following properties : ${requiredProperties.join(',')}`);
        }
        return true;
    }

    /**
     * Get the selected provider if set or infer it from the providers list
     * @param {String} type - the type of provider (runner, communicator, proxy, etc.)
     * @param {Object} config
     * @returns {String} the selected provider for the given type
     */
    function getSelectedProvider(type = 'runner', config = {}) {

        if (config.provider && config.provider[type]) {
            return config.provider[type];
        }

        if (config.providers && config.providers[type]) {
            const typeProviders = config.providers[type];
            if (typeof typeProviders === 'object' && (typeProviders.id || typeProviders.name)) {
                return typeProviders.id || typeProviders.name;
            }
            if (Array.isArray(typeProviders) && typeProviders.length > 0) {
                return typeProviders[0].id || typeProviders[0].name;
            }
        }
        return false;
    }

    /**
     * Wraps a test runner into a component
     * @param {jQuery|HTMLElement|String} container - The container in which renders the component
     * @param {Object} config - The component configuration options
     * @param {String} config.serviceCallId - The identifier of the test session
     * @param {Object} config.providers
     * @param {Object} config.options
     * @param {Boolean} [config.loadFromBundle=false] - do we load the modules from the bundles
     * @param {Boolean} [config.replace] - When the component is appended to its container, clears the place before
     * @param {Number|String} [config.width] - The width in pixels, or 'auto' to use the container's width
     * @param {Number|String} [config.height] - The height in pixels, or 'auto' to use the container's height
     * @param {Function} [template] - An optional template for the component
     * @returns {runnerComponent}
     */
    return function runnerComponentFactory(container = null, config = {}, template = runnerComponentTpl) {
        let runner = null;
        let plugins = [];

        if (!container) {
            throw new TypeError('A container element must be defined to contain the runnerComponent');
        }

        validateTestRunnerConfiguration(config);

        /**
         * @typedef {runner} runnerComponent
         */
        const runnerComponent = component({

            /**
             * Gets the option's value
             * @param {String} name - the option key
             * @returns {*}
             */
            getOption(name) {
                return this.config.options[name];
            },

            /**
             * Gets the test runner
             * @returns {runner}
             */
            getRunner() {
                return runner;
            }
        })
        .setTemplate(template)
        .on('init', function () {

            //load the defined providers for the runner, the proxy, the communicator, the plugins, etc.
            return providerLoader(config.providers, config.loadFromBundle)
                .then( results => {
                    if(results && results.plugins) {
                        plugins = results.plugins;
                    }

                    this.render(container);
                    this.hide();
                })
                .catch( err => this.trigger('error', err));
        })
        .on('render', function() {

            const runnerConfig = Object.assign(_.omit(this.config, ['providers']), {
                renderTo: this.getElement()
            });
            const runnerProviderId = getSelectedProvider('runner', this.config);

            runner = runnerFactory(runnerProviderId, plugins, runnerConfig)
                .on('ready', () => {
                    _.defer( () => {
                        this
                            .setState('ready')
                            .trigger('ready', runner)
                            .show();
                    });
                })
                .spread(this, 'error')
                .init();
        })
        .on('destroy', function () {
            var destroying = runner && runner.destroy();
            runner = null;
            return destroying;
        })
        .after('destroy', function(){
            this.removeAllListeners();
        });

        return runnerComponent.init(config);
    };
});
