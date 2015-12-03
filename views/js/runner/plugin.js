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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Sam <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/eventifier'
], function (_, eventifier){
    'use strict';

    /**
     * Meta factory for plugins
     *
     * @param {Object} provider - the list of implemented methods
     * @param {String} provider.name - the plugin name
     * @param {Function} provider.init - the plugin initialization method
     * @param {Function} [provider.destroy] - plugin destroy behavior
     * @param {Function} [provider.show] - plugin show behavior
     * @param {Function} [provider.hide] - plugin hide behavior
     * @param {Function} [provider.enable] - plugin enable behavior
     * @param {Function} [provider.disable] - plugin disable behavior
     * @param {Object} defaults - default configuration to be assigned
     * @returns {Function} - the generated plugin factory
     */
    function pluginFactory(provider, defaults){
        var pluginName;

        if(!_.isPlainObject(provider) || !_.isString(provider.name) || _.isEmpty(provider.name) || !_.isFunction(provider.init)){
            throw new TypeError('A plugin should be defined at least by a name property and an init method');
        }

        //TODO verify the name isn't already in use
        pluginName = provider.name;

        defaults = defaults || {};


        /**
         * The configured plugin factory
         *
         * @param {Object} config
         * @returns {Object} the plugin instance
         */
        return function instanciatePlugin(runner, config){
            var plugin;
            var states = {};

            /**
            * Delegate a function call to the provider
            *
            * @param {Object} context - the context the function will apply to
            * @param {String} fnName - the function name
            * @param {Array} args - the array of arguments to be applied to the function
            * @returns {undefined}
            */
            var delegate = function delegate(fnName){
                if(_.isFunction(provider[fnName])){
                    return provider[fnName].apply(plugin, [].slice.call(arguments, 1));
                }
            };


            config = _.defaults(config || {}, defaults);

            plugin = eventifier({

                /**
                 * Initializes the runner plugin
                 */
                init : function init(){

                    states = {};

                    delegate('init', runner, config);

                    this.state('init', true);
                    this.trigger('init');

                    return this;
                },

                /**
                 * Destroys the plugin
                 * @returns {plugin}
                 */
                destroy : function destroy(){

                    delegate('destroy');

                    config = {};
                    states = {};

                    this.state('init', false);
                    this.trigger('destroy');

                    return this;
                },

                /**
                 * Get the test runner
                 * @returns {testRunner} the plugins's testRunner
                 */
                getTestRunner : function getTestRunner(){
                    return runner;
                },

                /**
                 * Get the config
                 * @returns {Object} config
                 */
                getConfig : function getConfig(){
                    return config;
                },

                /**
                 * Get the config
                 * @returns {Object} config
                 */
                setConfig : function setConfig(name, value){
                    if(_.isPlainObject(name)){
                        config = _.defaults(name, config);
                    }else{
                        config[name] = value;
                    }
                    return this;
                },

                /**
                 * Get or set a state to the plugin
                 * If the second argument is provided, it will set the state to true or false
                 * Otherwise, it will return true if the state is set, or false otherwise.
                 *
                 * @param {String} name - the state name
                 * @param {Boolean} [active] - if undefined,
                 * @returns
                 */
                state : function(name, active){
                    if(_.isString(name)){
                        if(active === undefined){
                            //get state
                            return !!states[name];
                        }else{
                            //set state
                            states[name] = !!active;
                        }
                    }else{
                        throw new TypeError('the state name must be a string');
                    }
                },

                /**
                 * Shows the component related to this plugin
                 * @returns {plugin}
                 */
                show : function show(){
                    delegate('show');
                    this.state('visible', true);
                    this.trigger('show');
                    return this;
                },

                /**
                 * Hides the component related to this plugin
                 * @returns {plugin}
                 */
                hide : function hide(){
                    delegate('hide');
                    this.state('visible', false);
                    this.trigger('hide');
                    return this;
                },

                /**
                 * Enables the plugin
                 * @returns {plugin}
                 */
                enable : function enable(){
                    delegate('enable');
                    this.state('enabled', true);
                    this.trigger('enable');
                    return this;
                },

                /**
                 * Disables the plugin
                 * @returns {plugin}
                 */
                disable : function disable(){
                    delegate('disable');
                    this.state('enabled', false);
                    this.trigger('disable');

                    return this;
                }
            });

            //get the trigger function to overwrite it later
            var trigger = plugin.trigger;
            plugin.trigger = function superTrigger(){
                var rootComponent = this.getTestRunner();
                var args = [].slice.call(arguments);
                //implementation note : trigger is a delegated function so the applied context does not matter
                trigger.apply(null, args);
                if(rootComponent && rootComponent.trigger){
                    //forward the triggered event to the root component too with the plugin name as suffix
                    args[0] += '.'+pluginName;
                    rootComponent.trigger.apply(null, args);
                }
            };

            return plugin;
        };
    }

    return pluginFactory;
});
