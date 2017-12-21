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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA ;
 */

/**
 * Test runner component
 *
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'context',
    'core/promise',
    'core/pluginLoader',
    'core/providerLoader',
    'ui/component',
    'taoTests/runner/runner',
    'tpl!taoTests/template/runnerComponent'
], function ($, _, context, Promise, pluginLoaderFactory, providerLoaderFactory, component, runnerFactory, runnerComponentTpl) {
    'use strict';

    /**
     * List of options required by the runner
     * @type {String[]}
     */
    var requiredOptions = [
        'provider'
    ];

    /**
     * Some defaults options
     * @type {Object}
     */
    var defaults = {};

    /**
     * Loads the modules dynamically
     * @param {Function} loader - the loader factory
     * @param {Object[]} modules - the collection of modules to load
     * @returns {Promise} resolves with the list of loaded modules
     */
    function loadModules(loader, modules) {
        return loader()
            .addList(modules)
            .load(context.bundle);
    }

    /**
     * Registers a list of loaded providers
     * @param providers
     */
    function registerProviders(providers) {
        _.forEach(providers, function (provider) {
            runnerFactory.registerProvider(provider.name, provider);
        });
        return providers;
    }

    /**
     * Wraps a test runner into a component
     * @param {Object}   config - The testRunner options
     * @param {String}   config.provider - The provider to use
     * @param {Object[]} [config.plugins] - A collection of plugins to load
     * @param {Object[]} [config.providers] - A collection of providers to load
     * @param {jQuery|HTMLElement|String} [config.renderTo] - An optional container in which renders the component
     * @param {Boolean} [config.replace] - When the component is appended to its container, clears the place before
     * @param {Number|String} [config.width] - The width in pixels, or 'auto' to use the container's width
     * @param {Number|String} [config.height] - The height in pixels, or 'auto' to use the container's height
     * @param {Function} [template] - an optional template for the component
     * @returns {runnerComponent}
     */
    return function runnerComponentFactory(config, template) {
        var runner, resolver;

        // the runner promise is built aside for architecture reason
        // otherwise the errors that may be raised from the init() method wouldn't be properly managed
        var runnerPromise = new Promise(function (resolve, reject) {
            resolver = {
                resolve: resolve,
                reject: reject
            };
        });

        var componentTemplate = template || runnerComponentTpl;

        var runnerComponentApi = {
            /**
             * Does the option exists ?
             * @param {String} name - the option key
             * @returns {Boolean}
             */
            hasOption: function hasOption(name) {
                return typeof this.config[name] !== 'undefined';
            },

            /**
             * Gets the option's value
             * @param {String} name - the option key
             * @returns {*}
             */
            getOption: function getOption(name) {
                return this.config[name];
            },

            /**
             * Gets the options values
             * @returns {Object}
             */
            getConfig: function getConfig() {
                return this.config;
            },

            /**
             * Gets the test runner
             * @returns {runner}
             */
            getRunner: function getRunner() {
                return runner;
            },

            /**
             * Apply an action when the runner is ready
             * @param {Function} [callback]
             * @returns {Promise}
             */
            whenReady: function whenReady(callback) {
                var when = runnerPromise;
                if (_.isFunction(callback)) {
                    when = when.then(callback);
                }
                return when;
            },

            /**
             * Loads an item
             * @param itemRef
             * @returns {Promise}
             */
            loadItem: function loadItem(itemRef) {
                return this.whenReady(function () {
                    return new Promise(function (resolve) {
                        runner.after('renderitem.runnerComponent', function () {
                            runner.off('renderitem.runnerComponent');
                            resolve();
                        });
                        runner.loadItem(itemRef);
                    });
                });
            }
        };

        /**
         * @typedef {runner} runnerComponent
         */
        var runnerComponent = component(runnerComponentApi, defaults)
            .setTemplate(componentTemplate)
            .on('init', function () {
                var self = this;
                _.forEach(requiredOptions, function (name) {
                    if (!self.hasOption(name)) {
                        throw new TypeError('Missing required option ' + name);
                    }
                });
            })
            .on('destroy', function () {
                if (runner) {
                    runner.destroy();
                }
            })
            .after('destroy', function () {
                this.removeAllListeners();
                runner = null;
            })
            .on('render', function () {
                var self = this;
                var plugins = [];
                var initPromises = [];

                self.hide();

                if (self.hasOption('providers')) {
                    initPromises.push(
                        loadModules(providerLoaderFactory, self.getOption('providers'))
                            .then(registerProviders)
                    );
                }

                if (self.hasOption('plugins')) {
                    initPromises.push(
                        loadModules(pluginLoaderFactory, self.getOption('plugins'))
                            .then(function (loadedPlugins) {
                                plugins = loadedPlugins;
                            })
                    );
                }

                Promise.all(initPromises).then(function () {
                    var runnerConfig = _.omit(self.config, ['plugins', 'providers']);
                    runnerConfig.renderTo = self.getElement();

                    runner = runnerFactory(runnerConfig.provider, plugins, runnerConfig)
                        .on('error', function (err) {
                            self.trigger('error', err);
                        })
                        .on('ready', function () {
                            _.defer(function () {
                                self.show();
                                self.setState('ready');
                                self.trigger('ready');

                                resolver.resolve(runner);
                            });
                        })
                        .after('destroy', function () {
                            this.removeAllListeners();
                        });

                    self.trigger('runner', runner);

                    runner.init();
                });
            });

        try {
            runnerComponent.init(config);
        } catch(err) {
            // ensure the runner promise is rejected as the init failed
            resolver.reject(err);
            throw err;
        }

        return runnerComponent;
    };
});
