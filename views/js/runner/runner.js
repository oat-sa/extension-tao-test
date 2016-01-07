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
 * @author Sam <sam@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'core/eventifier',
    'core/promise',
    'taoTests/runner/providerRegistry'
], function ($, _, __, eventifier, Promise, providerRegistry){
    'use strict';

    /**
     * Builds an instance of the QTI test runner
     *
     * @param {String} providerName
     * @param {Object} config
     * @param {String|DOMElement|JQuery} config.contentContainer - the dom element that is going to holds the test content (item, rubick, etc)
     * @param {Array} [config.plugins] - the list of plugin instances to be initialized and bound to the test runner
     * @returns {runner|_L28.testRunnerFactory.runner}
     */
    function testRunnerFactory(providerName, config){

        var runner;
        var states = {
            'init':    false,
            'ready':   false,
            'render':  false,
            'finish':  false,
            'destroy': false
        };
        var context    = {};
        var provider   = testRunnerFactory.getProvider(providerName);
        var areaBroker = provider.getAreaBroker();

        /**
         * Delegate a function call to the selected provider
         *
         * @param {String} fnName
         * @param {Array} args - array of arguments to apply to the method
         * @private
         * @returns {undefined}
         */
        function delegate(fnName){
            if(_.isFunction(provider[fnName])){
                provider[fnName].apply(runner, [].slice.call(arguments, 1));
            }
        }

        //config defaults
        config = _.defaults(config || {}, {
            plugins : {}
        });

        /**
         * Defines the test runner
         * @type {runner}
         */
        runner = eventifier({

            /**
             * Initializes the runner
             * @param {Object} config
             */
            init : function init(){

                delegate('init');

                _.forEach(this.getPlugins(), function (plugin){
                    plugin(runner, areaBroker).init();
                });

                this.setState('init', true);
                this.trigger('init');

                return this;
            },

            render : function render(){


                delegate('render');

                _.forEach(this.getPlugins(), function (plugin){
                    if(_.isFunction(plugin.render)){
                        plugin.render();
                    }
                });

                this.setState('ready', true);
                this.trigger('ready')
                    .trigger('render');

                return this;
            },

            loadItem : function loadItem(){
                delegate('loadItem');

                this.trigger('loaditem');
                return this;
            },

            renderItem : function renderItem(){
                delegate('renderItem');

                this.trigger('renderitem');
                return this;
            },

            finish : function finish(){
                delegate('finish');

                _.forEach(this.getPlugins(), function (plugin){
                    if(_.isFunction(plugin.finish)){
                        plugin.finish();
                    }
                });

                this.setState('finish', true);
                this.trigger('finish');

                return this;
            },

            destroy : function destroy(){

                context = {};

                delegate('destroy');

                _.forEach(this.getPlugins(), function (plugin){
                    if(_.isFunction(plugin.destroy)){
                        plugin.destroy();
                    }
                });

                this.setState('destroy', true);
                this.trigger('destroy');

                return this;
            },

            getPlugins : function getPlugins(){
                return config.plugins;
            },

            getPlugin : function getPlugin(name){
                return config.plugins[name];
            },

            /**
             * Check a runner state
             *
             * @param {String} name - the state name
             * @returns {Boolean} if active, false if not set
             */
            getState : function getState(name){
                return !!states[name];
            },

            /**
             * Define a runner state
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

            getContext : function getContext(){
                return context;
            },

            setContext : function setContext(newContext){
                if(_.isPlainObject(context)){
                    context = newContext;
                }
            }
        });

        runner.on('move', function move(type){
            this.trigger.apply(this, [type].concat([].slice.call(arguments, 1)));
        });

        return runner;
    }

    //bind the provider registration capabilities to the testRunnerFactory
    return providerRegistry(testRunnerFactory, function validateProvider(provider){

        //mandatory methods
        if(!_.isFunction(provider.getAreaBroker)){
            throw new TypeError('The runner provider MUST have a method that returns an areaBroker');
        }
       return true;
    });
});
