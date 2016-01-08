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
    'core/logger',
    'taoTests/runner/providerRegistry'
], function ($, _, __, eventifier, Promise, logger, providerRegistry){
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
    function testRunnerFactory(providerName, pluginFactories, config){

        /**
         * @type {Object} The test runner instance
         */
        var runner;

        /**
         * @type {Object} the test definition data
         */
        var testData       = {};

        /**
         * @type {Object} contextual test data (the state of the test)
         */
        var testContext    = {};

        /**
         * @type {Object} the registered plugins
         */
        var plugins        = {};

        /**
         * @type {Object} the test of the runner
         */
        var states = {
            'init':    false,
            'ready':   false,
            'render':  false,
            'finish':  false,
            'destroy': false
        };

        /**
         * The selected test runner provider
         */
        var provider       = testRunnerFactory.getProvider(providerName);

        /**
         * The provider gives us a reference to an areaBroker (so our provider can attach elements to the GUI)
         */
        var areaBroker     = provider.getAreaBroker();

        /**
         * Run a method of the provider (by delegation)
         *
         * @param {String} method - the method to run
         * @param {...} args - rest parameters given to the provider method
         * @returns {Promise} so provider can do async stuffs
         */
        function providerRun(method){
            var args = [].slice.call(arguments, 1);
            return new Promise(function(resolve){
                if(!_.isFunction(provider[method])){
                   resolve();
                }
                resolve(provider[method].apply(runner, args));
            });
        }

        /**
         * Run a method in all plugins
         *
         * @param {String} method - the method to run
         * @returns {Promise} once that resolve when all plugins are done
         */
        function pluginRun(method){
            var execStack = [];

            _.forEach(runner.getPlugins(), function (plugin){
                if(_.isFunction(plugin[method])){
                    execStack.push(plugin[method]());
                }
            });

            return Promise.all(execStack);
        }


        config = config || {};

        /**
         * Defines the test runner
         *
         * @type {runner}
         */
        runner = eventifier({

            /**
             * Initializes the runner
             */
            init : function init(){
                var self = this;

                //instantiate the plugins first
                _.forEach(pluginFactories, function(pluginFactory, pluginName){
                    plugins[pluginName] = pluginFactory(runner, areaBroker);
                });

                providerRun('init').then(function(){
                    pluginRun('init').then(function(){
                        self.setState('init', true)
                            .trigger('init')
                            .render();
                    });
                });
                return this;
            },

            render : function render(){
                var self = this;

                providerRun('render').then(function(){
                    pluginRun('render').then(function(){
                        self.setState('ready', true)
                            .trigger('render')
                            .trigger('ready');
                    });
                });
                return this;
            },

            loadItem : function loadItem(itemRef){
                var self = this;

                providerRun('loadItem', itemRef).then(function(itemData){
                    self.trigger('loaditem', itemRef)
                        .renderItem(itemData);
                });
                return this;
            },

            renderItem : function renderItem(itemData){
                var self = this;

                providerRun('renderItem', itemData).then(function(){
                    self.trigger('renderitem', itemData);
                });
                return this;
            },

            unloadItem : function loadItem(itemRef){
                var self = this;

                providerRun('unloadItem', itemRef).then(function(itemData){
                    self.trigger('unloaditem', itemRef);
                });
                return this;
            },

            finish : function finish(){
                var self = this;

                providerRun('finish').then(function(){
                    pluginRun('finish').then(function(){
                        self.setState('finish', true)
                            .trigger('finish');
                    });
                });
                return this;
            },

            destroy : function destroy(){

                testContext = {};

                providerRun('destroy');

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
                return plugins;
            },

            getPlugin : function getPlugin(name){
                return plugins[name];
            },

            getConfig : function getConfig(){
                return config;
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

            getTestContext : function getTestContext(){
                return testContext;
            },

            setTestContext : function setTestContext(context){
                if(_.isPlainObject(context)){
                    testContext = context;
                }
            },

            getTestData : function getTestData(){
                return testData;
            },

            setTestData : function setTestData(data){
                testData  = data;
            },

            //aliases / actions
            next : function next(){
                this.trigger('move', 'next');
                return this;
            },

            previous : function previous(){
                this.trigger('move', 'previous');
                return this;
            },

            jump : function jump(to){
                this.trigger('move', 'jump', to);
                return this;
            },

            skip : function skip(to){
                this.trigger('sip');
                return this;
            },

            exit : function exit(way, reason){

                this.trigger('exit', way, reason);
                return this;
            },

            pause : function pause(){
                this.trigger('pause');
                return this;
            },
            resume : function resume(){
                this.trigger('resume');
                return this;
            }

        }, logger('testRunner'));

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
