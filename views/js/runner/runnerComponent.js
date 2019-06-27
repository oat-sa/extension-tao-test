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
     * Wraps a test runner into a component
     * @param {jQuery|HTMLElement|String} container - The container in which renders the component
     * @param {Object} config - The component configuration options
     * @param {String} config.serviceCallId - The identifier of the test session
     * @param {Object} config.providers - The component conf
     * @param {Object} config.options - The
     * @param {Boolean} [config.replace] - When the component is appended to its container, clears the place before
     * @param {Number|String} [config.width] - The width in pixels, or 'auto' to use the container's width
     * @param {Number|String} [config.height] - The height in pixels, or 'auto' to use the container's height
     * @param {Function} [template] - An optional template for the component
     * @returns {runnerComponent}
     */
    return function runnerComponentFactory(container, config = {}, template = runnerComponentTpl) {
        let runner = null;

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
                return this.config[name];
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
                    if(!results || !results.runner || !results.plugins) {
                        throw new Error(`The loaded providers doesn't contain the runner provider nor the plugins`);
                    }
                    this.loadedProviders = results;

                    this.render(container);
                    this.hide();
                })
                .catch( err => this.trigger('error', err));
        })
        .on('render', function() {

            const runnerConfig = Object.assign(_.omit(this.config, ['providers']), {
                renderTo: this.getElement()
            });

            runner = runnerFactory(this.loadedProviders.runner.id, this.loadedProviders.plugins, runnerConfig)
                .on('error', err => this.trigger('error', err) )
                .on('ready', () => {
                    _.defer( () => {
                        this
                            .setState('ready')
                            .trigger('ready', runner)
                            .show();
                    });
                })
                .after('destroy', () => runner.removeAllListeners() )
                .init();
        })
        .on('destroy', function () {
            var destroying = runner && runner.destroy();
            runner = null;
            return destroying;
        })
        .after('destroy', function () {
            this.removeAllListeners();
        });

        return runnerComponent.init(config);
    };
});
