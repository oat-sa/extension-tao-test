/*
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
 * Copyright (c) 2016 (original work) Open Assessment Technlogies SA
 *
 */

/**
 * The probeOverseer let's you define probes that will listen for events and record logs
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/promise',
    'moment',
    'lib/uuid',
    'lib/localforage'
], function (_, Promise, moment, uuid, localforage){
    'use strict';

    /**
     * Create the overseer intance
     * @param {String} testIdentifier - a unique id for a test execution
     * @param {runner} runner - a insance of a test runner
     * @returns {probeOverseer} the new probe overseer
     * @throws TypeError if something goes wrong
     */
    return function probeOverseerFactory(testIdentifier, runner){

        var store;
        var writing;
        var queue = [];
        var probes = [];

        if(_.isEmpty(testIdentifier)){
            throw new TypeError('Please set a test identifier');
        }
        if(!_.isPlainObject(runner) || !_.isFunction(runner.init) || !_.isFunction(runner.on)){
            throw new TypeError('Please set a test runner');
        }

        //create a unique instance in the offline storage
        store = localforage.createInstance({
            name: 'test-probe-' + testIdentifier
        });

        /**
         * @typedef {probeOverseer}
         */
        return {

            /**
             * Add a new probe
             * @param {Object} probe
             * @param {String} probe.name - the probe name
             * @param {Boolean} [probe.latency = false] - simple or latency mode
             * @param {String[]} [probe.events] - the list of events to listen (simple mode)
             * @param {String[]} [probe.startEvents] - the list of events to mark the start (lantency mode)
             * @param {String[]} [probe.stopEvents] - the list of events to mark the end (latency mode)
             * @param {Function} [probe.capture] - lambda fn to define the data context, it receive the test runner and the event parameters
             * @returns {probeOverseer} chains
             * @throws TypeError if the probe is not well formatted
             */
            add: function add(probe){

                // probe structure strict validation

                if(!_.isPlainObject(probe)){
                    throw new TypeError('A probe is a plain object');
                }
                if(!_.isString(probe.name) || _.isEmpty(probe.name)){
                    throw new TypeError('A probe must have a name');
                }
                if(_.where(probes, {name : probe.name }).length > 0){
                    throw new TypeError('A probe with this name is already regsitered');
                }

                if(probe.latency){
                    if(_.isString(probe.startEvents) && !_.isEmpty(probe.startEvents)){
                        probe.startEvents = [probe.startEvents];
                    }
                    if(_.isString(probe.stopEvents) && !_.isEmpty(probe.stopEvents)){
                        probe.stopEvents = [probe.stopEvents];
                    }
                    if(!probe.startEvents.length || !probe.stopEvents.length){
                        throw new TypeError('Latency based probes must have startEvents and stopEvents defined');
                    }
                } else {
                    if(_.isString(probe.events) && !_.isEmpty(probe.events)){
                        probe.events = [probe.events];
                    }
                    if(!_.isArray(probe.events) || probe.events.length === 0){
                        throw new TypeError('A probe must define events');
                    }
                }

                probes.push(probe);

                return this;
            },


            /**
             * Get the time entries queue
             * @returns {Promise} with the data in parameterj
             */
            getQueue : function getQueue(){
                return store.getItem('queue');
            },

            /**
             * Get the list of defined probes
             * @returns {Object[]} the probes collection
             */
            getProbes : function getProbes(){
                return probes;
            },

            /**
             * Push an time entry to the queue
             * @param {Object} entry - the time entry
             */
            push : function push(entry){
                queue.push(entry);

                //ensure the queue is pushed to the store consistently and atomically
                if(writing){
                    writing.then(function(){
                        return store.setItem('queue', queue);
                    });
                } else {
                    writing = store.setItem('queue', queue);
                }
            },

            /**
             * Flush the queue and get the entries
             * @returns {Promise} with the data in parameter
             */
            flush: function flush(){

                return new Promise(function(resolve){
                    store.getItem('queue').then(function(flushed){
                        queue = [];
                        store.setItem('queue', queue);
                        resolve(flushed);
                    });
                });
            },

            /**
             * Start the probes
             */
            start : function start(){
                var self = this;
                _.forEach(probes, function(probe){
                    var probeHandler,
                        startHandler,
                        stopHandler;

                    var eventNs = '.probe-' + probe.name;

                    if(probe.latency){

                        startHandler = function startHandler(){
                            var now = moment();
                            var data = {
                                id: uuid(8, 16),
                                marker: 'start',
                                type : probe.name,
                                timestamp : now.format('x') / 1000
                            };

                            if(typeof probe.capture === 'function'){
                                data.context = probe.capture(runner);
                            }
                            self.push(data);
                        };

                        stopHandler = function stopHandler(){
                            var now = moment();
                            var last;
                            var data = {
                                type : probe.name,
                                timestamp : now.format('x') / 1000
                            };
                            self.getQueue().then(function(queue){
                                last = _.findLast(queue, { type : probe.name, marker : 'start' });
                                if(last && !_.findLast(queue, { type : probe.name, marker : 'stop', id : last.id })){
                                    data.id = last.id;
                                    data.marker = 'end';
                                    if(typeof probe.capture === 'function'){
                                        data.context = probe.capture(runner);
                                    }
                                    self.push(data);
                                }
                            });
                        };
                        _.forEach(probe.startEvents, function(eventName){
                            runner.on(eventName + eventNs, startHandler);
                        });
                        _.forEach(probe.stopEvents, function(eventName){
                            runner.on(eventName + eventNs, stopHandler);
                        });
                    } else {

                        probeHandler = function probeHandler(){
                            var now = moment();
                            var last;
                            var data = {
                                id   : uuid(8, 16),
                                type : probe.name,
                                timestamp : now.format('x') / 1000
                            };
                            if(typeof probe.capture === 'function'){
                                data.context = probe.capture(runner);
                            }
                            self.push(data);
                        };

                        _.forEach(probe.events, function(eventName){
                            runner.on(eventName + eventNs, probeHandler);
                        });
                    }
                });
            },


            /**
             * Stop the probes
             * Be carefull, stop will also clear the store and the queue
             */
            stop  : function stop(){
                _.forEach(probes, function(probe){
                    var eventNs = '.probe-' + probe.name;
                    var removeHandler = function removeHandler(eventName){
                        runner.off(eventName + eventNs);
                    };

                    _.forEach(probe.startEvents, removeHandler);
                    _.forEach(probe.stopEvents, removeHandler);
                    _.forEach(probe.events, removeHandler);
                });

                queue = [];
                store.clear();
            }
        };
    };
});
