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

    var _defaults = {
        contentContainer : ''
    };
    
    var _eventTriggerEnable = false;
    var _eventTrigger = [];
    
    /**
     * Log the event trigger, useful for debugging or profiling
     * 
     * @param {Array} events - the event name + the event params
     * @private
     * @returns {undefined}
     */
    function _logEventTrigger(events){
        if(_eventTriggerEnable){
            _eventTrigger.push(events);
        }
    }
    
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
        
        var _provider = testRunnerFactory.getProvider(providerName);
        var _state = {};
        var $contentContainer;
        
        config = _.defaults(config || {}, _defaults);
        
        $contentContainer = $(config.contentContainer);
        
        /**
         * Delegate a function call to the selected provider
         * 
         * @param {String} fnName
         * @param {Array} args - array of arguments to apply to the method 
         * @private
         * @returns {undefined}
         */
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
            init : function init(){
                
                if(config.plugins){
                    _.forEach(config.plugins, function (plugin){
                        //todo : manage plugin loading in an async context (Promise, callback, events ?)
                        plugin.init(runner);
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
                //@todo : check if we can remove the service Api
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
                delegate('renderContent', [$contentContainer, _state]);
                return this;
            },
            
            /**
             * Infor that the content is rendered and ready for user interaction
             * @returns {runner}
             */
            contentReady : function contentReady(){
                this.trigger('contentready', $contentContainer);
                return this;
            }
            
        }).on('move', function move(type, otherArgs_){
            this.trigger.apply(this, [].slice.call(arguments));
        });
        
        var trigger = runner.trigger;//get the trigger function to overwrite it later
        runner.trigger = function superTrigger(){
            var args = [].slice.call(arguments);
            //implementation note : trigger is a delegated function so the applied context does not matter
            _logEventTrigger(args);
            trigger.apply(null, args);
        };
            
        return runner;
    }
    
    /**
     * Activate event logging
     */
    testRunnerFactory.startEventLog = function(){
        _eventTriggerEnable = true;
    };
    
    /**
     * Deactivate event logging
     */
    testRunnerFactory.stopEventLog = function(){
        _eventTriggerEnable = false;
    };
    
    /**
     * Get cumulated event log
     */
    testRunnerFactory.getEventLog = function(){
        return _eventTrigger;
    };
    
    /**
     * Empty the event log
     */
    testRunnerFactory.clearEventLog = function(){
        _eventTrigger = [];
    };
    
    
    //bind the provider registration capabilities to the testRunnerFactory
    return providerRegistry(testRunnerFactory);
});
