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
    'lib/moment-timezone.min'
], function (_, Promise, moment, uuid){
    'use strict';

    var timeZone = moment.tz.guess();

    var slice = Array.prototype.slice;

    /**
     * Create the overseer intance
     * @param {runner} runner - a instance of a test runner
     * @returns {probeOverseer} the new probe overseer
     * @throws TypeError if something goes wrong
     */
    return function probeOverseerFactory(runner){

        // the created instance
        var overseer;

        // the list of registered probes
        var probes = [];

        //temp queue
        var queue = [];

        //immutable queue which will not be flushed
        var immutableQueue = [];

        /**
         * @type {Storage} to store the collected events
         */
        var queueStorage;

        /**
         * @type {Promise} Promises chain to avoid write collisions
         */
        var writing = Promise.resolve();

        //is the overseer started
        var started = false;

        /**
         * Get the storage instance
         * @returns {Promise} that resolves with the storage
         */
        var getStorage = function getStorage(){
            if(queueStorage){
                return Promise.resolve(queueStorage);
            }
            return runner.getTestStore().getStore('test-probe').then(function(newStorage){
                queueStorage = newStorage;
                return Promise.resolve(queueStorage);
            });
        };

        /**
         * Unset the storage instance
         */
        var resetStorage = function resetStorage() {
            queueStorage = null;
        };

        /**
         * Register the collection event of a probe against a runner
         * @param {Object} probe - a valid probe
         */
        function collectEvent(probe){

            var eventNs = '.probe-' + probe.name;

            //event handler registered to collect data
            var probeHandler = function probeHandler(){
                var now = moment();
                var data = {
                    id   : uuid(12, 16),
                    type : probe.name,
                    timestamp : now.format('x') / 1000,
                    timezone  : now.tz(timeZone).format('Z')
                };
                if(typeof probe.capture === 'function'){
                    data.context = probe.capture.apply(probe, [runner].concat(slice.call(arguments)));
                }
                overseer.push(data);
            };

            //fallback
            if(probe.latency){
                return collectLatencyEvent(probe);
            }

            _.forEach(probe.events, function(eventName){
                var listen = eventName.indexOf('.') > 0 ? eventName : eventName + eventNs;
                runner.on(listen, _.partial(probeHandler, eventName));
            });
        }

        function collectLatencyEvent(probe){

            var eventNs = '.probe-' + probe.name;

            //start event handler registered to collect data
            var startHandler = function startHandler(){
                var now = moment();
                var data = {
                    id: uuid(12, 16),
                    marker: 'start',
                    type : probe.name,
                    timestamp : now.format('x') / 1000,
                    timezone  : now.tz(timeZone).format('Z')
                };

                if(typeof probe.capture === 'function'){
                    data.context = probe.capture.apply(probe, [runner].concat(slice.call(arguments)));
                }
                overseer.push(data);
            };

            //stop event handler registered to collect data
            var stopHandler = function stopHandler(){
                var now = moment();
                var last;
                var data = {
                    type : probe.name,
                    timestamp : now.format('x') / 1000,
                    timezone  : now.tz(timeZone).format('Z')
                };
                var args = slice.call(arguments);

                last = _.findLast(immutableQueue, { type : probe.name, marker : 'start' });
                if(last && !_.findLast(immutableQueue, { type : probe.name, marker : 'end', id : last.id })){
                    data.id = last.id;
                    data.marker = 'end';
                    if(typeof probe.capture === 'function'){
                        data.context = probe.capture.apply(probe, [runner].concat(args));
                    }
                    overseer.push(data);
                }
            };

            //fallback
            if(!probe.latency){
                return collectEvent(probe);
            }

            _.forEach(probe.startEvents, function(eventName){
                var listen = eventName.indexOf('.') > 0 ? eventName : eventName + eventNs;
                runner.on(listen, _.partial(startHandler, eventName));
            });
            _.forEach(probe.stopEvents, function(eventName){
                var listen = eventName.indexOf('.') > 0 ? eventName : eventName + eventNs;
                runner.on(listen, _.partial(stopHandler, eventName));
            });
        }

        //argument validation
        if(!_.isPlainObject(runner) || !_.isFunction(runner.init) || !_.isFunction(runner.on)){
            throw new TypeError('Please set a test runner');
        }

        /**
         * @typedef {probeOverseer}
         */
        overseer = {

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

                    //if already started we register the events on addition
                    if(started){
                        collectLatencyEvent(probe);
                    }
                } else {
                    if(_.isString(probe.events) && !_.isEmpty(probe.events)){
                        probe.events = [probe.events];
                    }
                    if(!_.isArray(probe.events) || probe.events.length === 0){
                        throw new TypeError('A probe must define events');
                    }

                    //if already started we register the events on addition
                    if(started){
                        collectEvent(probe);
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
                return getStorage().then(function(storage){
                    return storage.getItem('queue');
                });
            },

            /**
             * Get the list of defined probes
             * @returns {Object[]} the probes collection
             */
            getProbes : function getProbes(){
                return probes;
            },

            /**
             * Push a time entry to the queue
             * @param {Object} entry - the time entry
             */
            push : function push(entry){
                getStorage().then(function(storage){
                    //ensure the queue is pushed to the store consistently and atomically
                    writing = writing.then(function(){
                        queue.push(entry);
                        immutableQueue.push(entry);
                        return storage.setItem('queue', queue);
                    });
                });
            },

            /**
             * Flush the queue and get the entries
             * @returns {Promise} with the data in parameter
             */
            flush: function flush(){
                return new Promise(function(resolve){
                    getStorage().then(function(storage){
                        writing = writing.then(function () {
                            return storage.getItem('queue').then(function(flushed){
                                queue = [];
                                return storage.setItem('queue', queue).then(function(){
                                    resolve(flushed);
                                });
                            });
                        });
                    });
                });
            },

            /**
             * Start the probes
             * @returns {Promise} once started
             */
            start : function start(){
                return getStorage().then(function(storage){
                    return storage.getItem('queue').then(function(savedQueue){
                        if(_.isArray(savedQueue)){
                            queue = savedQueue;
                            immutableQueue = savedQueue;
                        }
                        _.forEach(probes, collectEvent);
                        started = true;
                    });
                });
            },


            /**
             * Stop the probes
             * Be carefull, stop will also clear the store and the queue
             * @returns {Promise} once stopped
             */
            stop  : function stop(){
                started = false;
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
                immutableQueue = [];
                return getStorage().then(function(storage){
                    return storage.removeItem('queue').then(resetStorage);
                });
            }
        };
        return overseer;
    };
});
