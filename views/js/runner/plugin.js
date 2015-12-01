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
    'jquery',
    'lodash',
    'i18n',
    'core/eventifier',
    'taoTests/runner/providerRegistry'
], function ($, _, __, eventifier, providerRegistry){
    'use strict';

    function pluginFactory(_provider, _defaults){

        _defaults = _defaults || {};

        function delegate(context, fnName, args){
            if(_provider){
                if(_.isFunction(_provider[fnName])){
                    _provider[fnName].apply(context, _.isArray(args) ? args : []);
                }
            }
        }

        return function instanciatePlugin(config){

            var _states = {};
            var config = _.defaults(config || {}, _defaults);

            var plugin = eventifier({
                /**
                 * Initializes the runner
                 * @param {Object} config
                 */
                init : function init(){
                    _states = {};
                    delegate(this, 'init', [config]);
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
                /**
                 * Checks if the plugin has a particular state
                 * @param {String} state
                 * @returns {Boolean}
                 */
                is : function is(state){
                    return !!_states[state];
                },
                /**
                 * Checks if the plugin has a particular state
                 * @param {String} state
                 * @returns {plugin}
                 */
                toggleState : function toggleState(state, active){
                    _states[state] = !!active;
                    return this;
                },
                /**
                 * Shows the component related to this plugin
                 * @returns {plugin}
                 */
                show : function show(){
                    delegate(this, 'show');
                    this.trigger('show');
                    return this;
                },
                /**
                 * Hides the component related to this plugin
                 * @returns {plugin}
                 */
                hide : function hide(){
                    delegate(this, 'hide');
                    this.trigger('hide');
                    return this;
                },
                /**
                 * Enables the plugin
                 * @returns {plugin}
                 */
                enable : function enable(){
                    delegate(this, 'enable');
                    this.trigger('enable');
                    return this;
                },
                /**
                 * Disables the plugin
                 * @returns {plugin}
                 */
                disable : function disable(){
                    delegate(this, 'disable');
                    this.trigger('disable');
                    return this;
                }
            });
            
            return plugin;
        };
    }

    return pluginFactory;
});