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
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'lodash',
    'core/eventifier'
], function (_, eventifier){
    'use strict';
    
    /**
     * Meta factory for plugins
     * 
     * @param {Object} _provider - the list of implemented methods
     * @param {Object} _defaults - default configuration to be assigned
     * @returns {Function} - the generated plugin factory
     */
    function pluginFactory(_provider, _defaults){

        _defaults = _defaults || {};
        
        /**
         * Delegate a function call to the provider
         * 
         * @param {Object} context - the context the function will apply to
         * @param {String} fnName - the function name
         * @param {Array} args - the array of arguments to be applied to the function
         * @returns {undefined}
         */
        function delegate(context, fnName, args){
            if(_provider){
                if(_.isFunction(_provider[fnName])){
                    _provider[fnName].apply(context, _.isArray(args) ? args : []);
                }
            }
        }
        
        /**
         * The created plugin factory
         * 
         * @param {Object} config
         * @returns {Object} the plugin instance
         */
        return function instanciatePlugin(config){

            var _states = {};
            var config = _.defaults(config || {}, _defaults);

            var plugin = eventifier({
                /**
                 * Initializes the runner
                 * @param {Object} rootComponent - the component the plugin is to be plugged into
                 */
                init : function init(rootComponent){
                    _states = {};
                    delegate(this, 'init', [rootComponent, config]);
                    this.rootComponent = rootComponent;
                    this.state('init', true);
                    this.trigger('init');
                    return this;
                },
                /**
                 * Destroys the plugin
                 * @returns {plugin}
                 */
                destroy : function destroy(){
                    delegate(this, 'destroy');
                    this.testRunner = null;
                    this.config = null;
                    this.state('init', false);
                    this.trigger('destroy');
                    return this;
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
                state : function(name, active){
                    if(_.isString(name)){
                        if(active === undefined){
                            //get state
                            return !!_states[name];
                        }else{
                            //set state
                            _states[name] = !!active;
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
                    delegate(this, 'show');
                    this.state('visible', true);
                    this.trigger('show');
                    return this;
                },
                /**
                 * Hides the component related to this plugin
                 * @returns {plugin}
                 */
                hide : function hide(){
                    delegate(this, 'hide');
                    this.state('visible', false);
                    this.trigger('hide');
                    return this;
                },
                /**
                 * Enables the plugin
                 * @returns {plugin}
                 */
                enable : function enable(){
                    delegate(this, 'enable');
                    this.state('enabled', true);
                    this.trigger('enable');
                    return this;
                },
                /**
                 * Disables the plugin
                 * @returns {plugin}
                 */
                disable : function disable(){
                    delegate(this, 'disable');
                    this.state('enabled', false);
                    this.trigger('disable');
                    return this;
                }
            });
            
            return plugin;
        };
    }

    return pluginFactory;
});