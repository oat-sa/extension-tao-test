define(['lodash', 'context', 'core/pluginLoader', 'core/providerLoader', 'ui/component', 'taoTests/runner/runner', 'handlebars'], function (_, context, pluginLoaderFactory, providerLoaderFactory, component, runnerFactory, Handlebars) { 'use strict';

    _ = _ && _.hasOwnProperty('default') ? _['default'] : _;
    context = context && context.hasOwnProperty('default') ? context['default'] : context;
    pluginLoaderFactory = pluginLoaderFactory && pluginLoaderFactory.hasOwnProperty('default') ? pluginLoaderFactory['default'] : pluginLoaderFactory;
    providerLoaderFactory = providerLoaderFactory && providerLoaderFactory.hasOwnProperty('default') ? providerLoaderFactory['default'] : providerLoaderFactory;
    component = component && component.hasOwnProperty('default') ? component['default'] : component;
    runnerFactory = runnerFactory && runnerFactory.hasOwnProperty('default') ? runnerFactory['default'] : runnerFactory;
    Handlebars = Handlebars && Handlebars.hasOwnProperty('default') ? Handlebars['default'] : Handlebars;

    var Template = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
      this.compilerInfo = [4,'>= 1.0.0'];
    helpers = this.merge(helpers, Handlebars.helpers);  


      return "<div class=\"runner-component\"></div>\n";
      });
    function runnerComponentTpl(data, options, asString) {
      var html = Template(data, options);
      return (asString || true) ? html : $(html);
    }

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
     * List of options required by the runner
     * @type {String[]}
     */

    var requiredOptions = ['provider'];
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
      return loader().addList(modules).load(context.bundle);
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
     * @param {jQuery|HTMLElement|String} container - The container in which renders the component
     * @param {Object}   config - The testRunner options
     * @param {String}   config.provider - The provider to use
     * @param {Object[]} [config.plugins] - A collection of plugins to load
     * @param {Object[]} [config.providers] - A collection of providers to load
     * @param {Boolean} [config.replace] - When the component is appended to its container, clears the place before
     * @param {Number|String} [config.width] - The width in pixels, or 'auto' to use the container's width
     * @param {Number|String} [config.height] - The height in pixels, or 'auto' to use the container's height
     * @param {Function} [template] - An optional template for the component
     * @returns {runnerComponent}
     */


    function runnerComponentFactory(container, config, template) {
      var runner = null;
      var runnerComponent;
      var runnerComponentApi = {
        /**
         * Gets the option's value
         * @param {String} name - the option key
         * @returns {*}
         */
        getOption: function getOption(name) {
          return this.config[name];
        },

        /**
         * Gets the test runner
         * @returns {runner}
         */
        getRunner: function getRunner() {
          return runner;
        }
      }; // ensure the required config has been provided

      config = _.omit(_.defaults(config || {}, defaults), ['renderTo']);

      _.forEach(requiredOptions, function (name) {
        if (typeof config[name] === 'undefined') {
          throw new TypeError("Missing required option ".concat(name));
        }
      });
      /**
       * @typedef {runner} runnerComponent
       */


      runnerComponent = component(runnerComponentApi).setTemplate(template || runnerComponentTpl).on('init', function () {
        var self = this;
        var plugins = [];
        var initPromises = [];

        if (self.getOption('providers')) {
          initPromises.push(loadModules(providerLoaderFactory, self.getOption('providers')).then(registerProviders));
        }

        if (self.getOption('plugins')) {
          initPromises.push(loadModules(pluginLoaderFactory, self.getOption('plugins')).then(function (loadedPlugins) {
            plugins = loadedPlugins;
          }));
        }

        Promise.all(initPromises).then(function () {
          self.on('render.runnerComponent', function () {
            var runnerConfig = _.assign(_.omit(self.config, ['plugins', 'providers']), {
              renderTo: self.getElement()
            });

            self.off('render.runnerComponent');
            runner = runnerFactory(runnerConfig.provider, plugins, runnerConfig).on('error', function (err) {
              self.trigger('error', err);
            }).on('ready', function () {
              _.defer(function () {
                self.show().setState('ready').trigger('ready', runner);
              });
            }).after('destroy', function () {
              this.removeAllListeners();
            }).init();
          }).render(container).hide();
        });
      }).on('destroy', function () {
        var destroying = runner && runner.destroy();
        runner = null;
        return destroying;
      }).after('destroy', function () {
        this.removeAllListeners();
      });
      return runnerComponent.init(config);
    }

    return runnerComponentFactory;

});
