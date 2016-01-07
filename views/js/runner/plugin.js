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
    'lodash'
], function (_){
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
        return function instanciatePlugin(runner, areaBroker, config){
            var plugin;

            var states = {};

            /**
             * Delegate a function call to the provider
             *
             * @param {String} fnName - the function name
             * @param {...} args - additional args are given to the provider
             * @returns {*} up to the provider
             */
            var delegate = function delegate(fnName){
                if(_.isFunction(provider[fnName])){
                    return provider[fnName].apply(plugin, [].slice.call(arguments, 1));
                }
            };


            config = _.defaults(config || {}, defaults);

            plugin = {

                /**
                 * Initializes the runner plugin
                 */
                init : function init(){

                    states = {};

                    delegate('init');

                    this.setState('init', true);

                    this.trigger('init');

                    return this;
                },

                render : function render(){

                    delegate('render');

                    this.setState('ready', true);

                    this.trigger('render')
                        .trigger('ready');

                    return this;
                },


                finish : function finish(){

                    delegate('finish');

                    this.setState('finish', true);

                    this.trigger('finish');

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

                    this.setState('init', false);

                    this.trigger('destroy');

                    return this;
                },

                /**
                * Triggers the events on the test runner using the pluginName as namespace
                * @param {String} name - the event name
                * @param {...} args - additional args are given to the event
                */
                trigger : function trigger(name){
                    var args = [].slice.call(arguments, 1);
                    runner.trigger.apply(runner, [name + '.' + pluginName, plugin].concat(args));
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
                 * Set a config entry
                 * @param {String|Object} name - the entry name or an object to merge
                 * @param {*} [value] - the config value if name is an entry
                 * @returns {plugin} chains
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
                 * Get a state of the plugin
                 *
                 * @param {String} name - the state name
                 * @returns {Boolean} if active, false if not set
                 */
                getState : function getState(name){
                    return !!states[name];
                },

                /**
                 * Set a state to the plugin
                 *
                 * @param {String} name - the state name
                 * @param {Boolean} active - is the state active
                 * @returns {plugin} chains
                 * @throws {TypeError} if the state name is not a valid string
                 */
                setState : function setState(name, active){
                    if(!_.isString(name) || _.isEmpty(name)){
                        throw new TypeError('The state must have a name');
                    }
                    states[name] = !!active;

                    return this;
                },


                /**
                 * Shows the component related to this plugin
                 * @returns {plugin} chains
                 */
                show : function show(){

                    delegate('show');

                    this.setState('visible', true);

                    this.trigger('show');

                    return this;
                },

                /**
                 * Hides the component related to this plugin
                 * @returns {plugin} chains
                 */
                hide : function hide(){

                    delegate('hide');

                    this.setState('visible', false);

                    this.trigger('hide');

                    return this;
                },

                /**
                 * Enables the plugin
                 * @returns {plugin} chains
                 */
                enable : function enable(){

                    delegate('enable');

                    this.setState('enabled', true);

                    this.trigger('enable');

                    return this;
                },

                /**
                 * Disables the plugin
                 * @returns {plugin} chains
                 */
                disable : function disable(){

                    delegate('disable');

                    this.setState('enabled', false);

                    this.trigger('disable');

                    return this;
                }
            };

            return plugin;
        };
    }

    return pluginFactory;
});
