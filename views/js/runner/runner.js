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
    'core/promise',
    'taoTests/runner/providerRegistry'
], function ($, _, __, eventifier, Promise, providerRegistry){
    'use strict';

    /**
     * Some default values
     * @type {Object}
     * @private
     */
    var _defaults = {
        content : $()
    };

    function testRunnerFactory(providerName, config){
        
        var _provider = testRunnerFactory.getProvider(providerName);
        var _states;
        var _state = {};
        
        config = _.defaults(config || {}, _defaults);
        
        function delegate(fnName, args){
            if(_provider){
                if(_.isFunction(_provider[fnName])){
                    _provider[fnName].apply(runner, _.isArray(args) ? args: []);
                }
            }
        }
        
        /**
         * Defines the QTI test runner
         * @type {runner}
         */
        var runner = eventifier({
            /**
             * Initializes the runner
             * @param {Object} config
             */
            init : function init(config){

                this.config = _.omit(config || {}, function (value){
                    return undefined === value || null === value;
                });
                _states = {};

                if(this.config.plugins){
                    _.forEach(this.config.plugins, function (plugin){
                        // todo: load plugins, then fire the init event
                        plugin.init(runner, config);
                    });
                }
                
                delegate('init', this);
                this.trigger('init', this);
                return this;
            },
            /**
             * Sets the runner in the ready state
             * @param {ServiceApi} serviceApi
             */
            ready : function ready(serviceApi){
                this.serviceApi = serviceApi;
                this.trigger('ready', this);
                return this;
            },
            /**
             *
             */
            load : function load(){
                this.trigger('load', this);
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            terminate : function terminate(){
                this.trigger('terminate', this);
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            endAttempt : function endAttempt(){
                this.trigger('endattempt', this);
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            next : function next(){
                this.trigger('move', 'next');
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            previous : function previous(){
                this.trigger('move', 'previous');
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            complete : function complete(){
                this.trigger('complete');
                return this;
            },
            /**
             *
             * @param scope
             * @returns {runner}
             */
            exit : function exit(scope){
                this.trigger('exit', scope);
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            skip : function skip(){
                this.trigger('move', 'skip');
                return this;
            },
            /**
             *
             * @param itemId
             * @returns {runner}
             */
            jump : function jump(itemId){
                this.trigger('move', 'jump', itemId);
                return this;
            },
            /**
             *
             * @param action
             * @param handler
             * @returns {runner}
             */
            registerAction : function registerAction(action, handler){
                this.on(action, handler);
                return this;
            },
            /**
             *
             * @param command
             * @returns {runner}
             */
            execute : function execute(command){
                this.trigger.apply(this, arguments);
                return this;
            },
            /**
             *
             * @param command
             * @param params
             * @param callback
             * @returns {runner}
             */
            request : function request(command, params, callback){
                var self = this;
                this.beforeRequest(function (){
                    $.ajax({
                        url : self.testContext[command + 'Url'] || command,
                        cache : false,
                        data : params,
                        async : true,
                        dataType : 'json',
                        success : function (testContext){
                            self.processRequest(testContext, callback);
                        }
                    });
                });
                return this;
            },
            /**
             *
             * @param process
             * @returns {runner}
             */
            beforeRequest : function beforeRequest(process){
                process();
                return this;
            },
            /**
             *
             * @param testContext
             * @param callback
             * @returns {runner}
             */
            processRequest : function processRequest(testContext, callback){
                callback();
                this.afterRequest();
                return this;
            },
            /**
             *
             * @returns {runner}
             */
            afterRequest : function afterRequest(){
                return this;
            },
            /**
             * Checks if the runner has a particular state
             * @param {String} state
             * @returns {Boolean}
             */
            is : function is(state){
                return !!_states[state];
            },
            /**
             * Set the current state object
             * @param {Object} state
             * @returns {runner}
             */
            setState : function(state){
                _state = state;
                return this;
            },
            /**
             * Return the current state object
             * @returns {Object}
             */
            getState : function(){
                return _state;
            },
            /**
             * Render the content of the test given the current test state
             * @returns {runner}
             */
            renderContent : function renderContent(){
                delegate('renderContent', [config.content, _state]);
                return this;
            },
            /**
             * Infor that the content is rendered and ready for user interaction
             * @returns {runner}
             */
            contentReady : function contentReady(){
                this.trigger('contentready', config.content);
                return this;
            }
        }).on('move', function move(type, otherArgs_){
            this.trigger.apply(this, [].slice.call(arguments));
        });
        
        return runner;
    }
    
    /**
     * Builds an instance of the QTI test runner
     * @param {Object} config
     * @returns {runner}
     */
    return providerRegistry(testRunnerFactory);
});
