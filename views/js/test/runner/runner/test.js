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
    'core/promise',
    'core/eventifier',
    'taoTests/runner/runner',
    'taoTests/runner/plugin'
], function($, _, Promise, eventifier, runnerFactory, pluginFactory){
    'use strict';

    var mockProvider = {
        init : _.noop,
        loadAreaBroker  : _.noop
    };


    QUnit.module('factory', {
        setup: function(){
            runnerFactory.registerProvider('mock', mockProvider);
        },
        teardown: function() {
            runnerFactory.clearProviders();
        }
    });

    QUnit.test('module', 5, function(assert){
        assert.equal(typeof runnerFactory, 'function', "The runner module exposes a function");
        assert.equal(typeof runnerFactory(), 'object', "The runner factory produces an object");
        assert.notStrictEqual(runnerFactory(), runnerFactory(), "The runner factory provides a different object on each call");
        assert.equal(typeof runnerFactory.registerProvider, 'function', "The runner module exposes a function registerProvider()");
        assert.equal(typeof runnerFactory.getProvider, 'function', "The runner module exposes a function getProvider()");
    });

    var testReviewApi = [

        {name : 'init', title : 'init'},
        {name : 'render', title : 'render'},
        {name : 'finish', title : 'finish'},
        {name : 'flush', title : 'flush'},
        {name : 'destroy', title : 'destroy'},
        {name : 'loadItem', title : 'loadItem'},
        {name : 'renderItem', title : 'renderItem'},
        {name : 'unloadItem', title : 'unloadItem'},
        {name : 'disableItem', title : 'disableItem'},
        {name : 'enableItem', title : 'enableItem'},

        {name : 'getPlugins', title : 'getPlugins'},
        {name : 'getPlugin', title : 'getPlugin'},
        {name : 'getConfig', title : 'getConfig'},
        {name : 'getState', title : 'getState'},
        {name : 'setState', title : 'setState'},
        {name : 'getItemState', title : 'getItemState'},
        {name : 'setItemState', title : 'setItemState'},
        {name : 'getTestData', title : 'getTestData'},
        {name : 'setTestData', title : 'setTestData'},
        {name : 'getTestContext', title : 'getTestContext'},
        {name : 'setTestContext', title : 'setTestContext'},
        {name : 'getAreaBroker', title : 'getAreaBroker'},
        {name : 'getProxy', title : 'getProxy'},
        {name : 'getProbeOverseer', title : 'getProbeOverseer'},

        {name : 'next', title : 'next'},
        {name : 'previous', title : 'previous'},
        {name : 'jump', title : 'jump'},
        {name : 'skip', title : 'skip'},
        {name : 'exit', title : 'exit'},
        {name : 'pause', title : 'pause'},
        {name : 'resume', title : 'resume'},
        {name : 'timeout', title : 'timeout'},

        {name : 'trigger', title : 'trigger'},
        {name : 'before', title : 'before'},
        {name : 'on', title : 'on'},
        {name : 'after', title : 'after'}
    ];

    QUnit
        .cases(testReviewApi)
        .test('api', function(data, assert){
            var instance = runnerFactory();
            assert.equal(typeof instance[data.name], 'function', 'The runner instance exposes a "' + data.title + '" function');
        });


    QUnit.module('provider', {
        setup: function(){
            runnerFactory.clearProviders();
        }
    });

    QUnit.asyncTest('init', function(assert){
       QUnit.expect(1);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function(){
               assert.equal(this.bar, 'baz', 'The provider is executed on the runner context');
               QUnit.start();
            }
        });

        var runner = runnerFactory('foo');
        runner.bar = 'baz';
        runner.init();
    });


    QUnit.asyncTest('get config', function(assert){
       QUnit.expect(1);

        var config = {
            'moo' : 'norz'
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function(){
                var myConfig = this.getConfig();
                assert.deepEqual(myConfig, config, 'The retrieved config is the right one');
                QUnit.start();
            }
        });

        var runner = runnerFactory('foo', {}, config);
        runner.init();
    });

    QUnit.asyncTest('render after async init', function(assert){
       QUnit.expect(4);

        var resolved = false;

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function(){
                var self = this;
                var p = new Promise(function(resolve){
                    setTimeout(function(){
                        resolved = true;
                        resolve();
                    }, 50);
                });
                assert.equal(resolved, false, 'Init is not yet resolved');
                return p;
            },
            render : function(){
               assert.equal(resolved, true, 'Render is called only when init is resolved');
            }
        });

        var runner = runnerFactory('foo');

        assert.equal(resolved, false, 'Init is not yet resolved');
        runner
           .on('ready', function(){
               assert.equal(resolved, true, 'Ready is triggered only when init is resolved');
               QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('states', function(assert){
       QUnit.expect(31);

        runnerFactory.registerProvider('foo', mockProvider);
        var runner = runnerFactory('foo');

        assert.throws(function(){
            runner.setState({ custom : true });
        }, TypeError, 'A state must have a name');

        runner
            .setState('custom', true)
            .on('init', function(){

                assert.equal(this.getState('custom'), true, 'The runner has the custom state');
                assert.equal(this.getState('init'), true, 'The runner is initialized');
                assert.equal(this.getState('ready'), false, 'The runner is not rendered');
                assert.equal(this.getState('finish'), false, 'The runner is not  finished');
                assert.equal(this.getState('flush'), false, 'The runner is not flushed');
                assert.equal(this.getState('destroy'), false, 'The runner is not destroyed');
            })
            .on('ready', function(){
                assert.equal(this.getState('init'), true, 'The runner is initialized');
                assert.equal(this.getState('ready'), true, 'The runner is rendered');
                assert.equal(this.getState('finish'), false, 'The runner is not finished');
                assert.equal(this.getState('flush'), false, 'The runner is not flushed');
                assert.equal(this.getState('destroy'), false, 'The runner is not destroyed');

                this.finish();
            })
            .on('finish', function(){
                assert.equal(this.getState('init'), true, 'The runner is initialized');
                assert.equal(this.getState('ready'), true, 'The runner is rendered');
                assert.equal(this.getState('finish'), true, 'The runner is finished');
                assert.equal(this.getState('flush'), false, 'The runner is not flushed');
                assert.equal(this.getState('destroy'), false, 'The runner is not destroyed');

                this.flush();
            })
            .on('flush', function(){
                assert.equal(this.getState('init'), true, 'The runner is initialized');
                assert.equal(this.getState('ready'), true, 'The runner is rendered');
                assert.equal(this.getState('finish'), true, 'The runner is finished');
                assert.equal(this.getState('flush'), true, 'The runner is flushed');
                assert.equal(this.getState('destroy'), false, 'The runner is not destroyed');

                this.destroy();
            })
            .on('destroy', function(){

                assert.equal(this.getState('init'), true, 'The runner is initialized');
                assert.equal(this.getState('ready'), true, 'The runner is rendered');
                assert.equal(this.getState('finish'), true, 'The runner is finished');
                assert.equal(this.getState('flush'), true, 'The runner is flushed');
                assert.equal(this.getState('destroy'), true, 'The runner is destroyed');
                QUnit.start();
            })
            .init();

        assert.throws(function() {
            runner.getItemState('', '');
        }, 'getItemState needs an itemRef');

        assert.throws(function() {
            runner.getItemState('123', '');
        }, 'getItemState needs a state name');

        assert.throws(function() {
            runner.setItemState('', '');
        }, 'setItemState needs an itemRef');

        assert.throws(function() {
            runner.setItemState('123', '');
        }, 'setItemState needs a state name');
    });

    QUnit.asyncTest('load and render item', function(assert){
       QUnit.expect(2);

        var items = {
            'aaa' : 'AAA',
            'zzz' : 'ZZZ'
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : _.noop,
            loadItem : function(itemRef){
               return items[itemRef];
            },
            renderItem : function(itemRef, itemData){
               assert.equal(itemRef, 'zzz', 'The rendered item is correct');
               assert.equal(itemData, 'ZZZ', 'The rendered item is correct');
               QUnit.start();
            }
        });

        var runner = runnerFactory('foo');
        runner
            .on('ready', function(){
                this.loadItem('zzz');
            })
            .init();
    });

    QUnit.asyncTest('load async and render item', function(assert){
       QUnit.expect(4);

       var resolved = false;
        var items = {
            'aaa' : 'AAA',
            'zzz' : 'ZZZ'
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : _.noop,
            loadItem : function(itemRef){
               var p = new Promise(function(resolve){
                    setTimeout(function(){
                        resolved = true;
                        resolve(items[itemRef]);
                    }, 50);
                });
                assert.equal(resolved, false, 'Item loading is not yet resolved');
               return p;
            },
            renderItem : function(itemRef, itemData){

               assert.equal(resolved, true, 'Item loading is resolved');
               assert.equal(itemRef, 'zzz', 'The rendered item is correct');
               assert.equal(itemData, 'ZZZ', 'The rendered item is correct');
               QUnit.start();
            }
        });

        var runner = runnerFactory('foo');
        runner
            .on('ready', function(){

                this.loadItem('zzz');
            })
            .init();
    });

    QUnit.asyncTest('unload item async', function(assert){
       QUnit.expect(4);

        var items = {
            'aaa' : 'AAA',
            'zzz' : 'ZZZ'
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : _.noop,
            unloadItem : function(itemRef){
                assert.equal(itemRef, 'zzz', 'The provider is called with the correct reference');
                assert.equal(items[itemRef], 'ZZZ', 'The item is not yet unloaded');

                return new Promise(function(resolve){
                    setTimeout(function(){
                        items[itemRef] = null;
                        resolve();
                    }, 50);
                });
            }
        });

        var runner = runnerFactory('foo');
        runner
            .on('ready', function(){
                this.unloadItem('zzz');
            })
            .on('unloaditem', function(itemRef){
                assert.equal(itemRef, 'zzz', 'The provider is called with the correct reference');
                assert.equal(items[itemRef], null, 'The item is now unloaded');
                QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('item state', function(assert){
        var items = {
            'aaa' : 'AAA',
            'zzz' : 'ZZZ'
        };

        QUnit.expect(17);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : _.noop,
            loadItem : function(itemRef){
                return items[itemRef];
            }
        });

        runnerFactory('foo')
            .on('init', function(){

                assert.throws(function(){
                    this.getItemState();
                }, TypeError, 'The item state should have an itemRef');

                assert.throws(function(){
                    this.getItemState('zzz');
                }, TypeError, 'The item state should have an itemRef and a name');

                assert.throws(function(){
                    this.setItemState();
                }, TypeError, 'The item state should have an itemRef');

                assert.throws(function(){
                    this.setItemState('zzz');
                }, TypeError, 'The item state should have an itemRef and a name');

                assert.equal(this.getItemState('zzz', 'loaded'), false, 'The item is not loaded');
                assert.equal(this.getItemState('zzz', 'ready'), false, 'The item is not ready');
                assert.equal(this.getItemState('zzz', 'foo'), false, 'The item is not foo');
            })
            .on('ready', function(){
                this.loadItem('zzz');
            })
            .on('loaditem', function(itemRef, itemData){

                assert.equal(itemRef, 'zzz', 'The loaded item is correct');
                assert.equal(itemData, 'ZZZ', 'The loaded item data is correct');
                assert.equal(this.getItemState('zzz', 'loaded'), true, 'The item is loaded');
                assert.equal(this.getItemState('zzz', 'ready'), false, 'The item is not ready');

                this.setItemState('zzz', 'foo', true);
                assert.equal(this.getItemState('zzz', 'foo'), true, 'The item is foo');
            })
            .on('renderitem', function(itemRef, itemData){

                assert.equal(itemRef, 'zzz', 'The rendered item is correct');
                assert.equal(itemData, 'ZZZ', 'The rendered item data is correct');
                assert.equal(this.getItemState('zzz', 'loaded'), true, 'The item is loaded');
                assert.equal(this.getItemState('zzz', 'ready'), true, 'The item is ready');
                assert.equal(this.getItemState('zzz', 'foo'), true, 'The item is foo');

                QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('disable items', function(assert){
       QUnit.expect(6);

        var items = {
            'aaa' : 'AAA',
            'zzz' : 'ZZZ'
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : _.noop,
            loadItem : function(itemRef){
               return items[itemRef];
            },
            renderItem : function(itemRef){
                var self = this;
                this.disableItem(itemRef);
                setTimeout(function(){
                    self.enableItem(itemRef);
                }, 50);
            }
        });

        var runner = runnerFactory('foo');
        runner
            .on('ready', function(){
                this.loadItem('zzz');
            })
            .on('loaditem', function(itemRef){
                assert.equal(itemRef, 'zzz', 'The provider is called with the correct reference');
                assert.equal(this.getItemState('zzz', 'disabled'), false, 'The item is not disabled');
            })
            .on('disableitem', function(itemRef){
                assert.equal(itemRef, 'zzz', 'The provider is called with the correct reference');
                assert.equal(this.getItemState('zzz', 'disabled'), true, 'The item is now disabled');
            })
            .on('enableitem', function(itemRef){
                assert.equal(itemRef, 'zzz', 'The provider is called with the correct reference');
                assert.equal(this.getItemState('zzz', 'disabled'), false, 'The item is not disabled anymore');

                QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('init error', function(assert){
       QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function(){
                return new Promise(function(resolve, reject){
                    reject(new Error('test'));
                });
            }
        });

        var runner = runnerFactory('foo');
        runner
            .on('error', function(err){
                assert.ok(err instanceof Error, 'The parameter is an error');
                assert.equal(err.message, 'test', 'The error message is correct');
                QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('context and data', function(assert){
       QUnit.expect(10);

        var testData = {
            items : {
                'lemmy' : 'kilmister',
                'david' : 'bowie'
            }
        };

        var testMap = {
            jumps: [],
            parts: [],
            map: {}
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function(){

                this.setTestData(testData);
                this.setTestContext({ best : testData.items.lemmy });
                this.setTestMap(testMap);
            }
        });

        var runner = runnerFactory('foo');
        runner
            .on('init', function(){

                var context = this.getTestContext();
                var data    = this.getTestData();
                var map    = this.getTestMap();

                assert.equal(typeof context, 'object', 'The test context is an object');
                assert.equal(typeof data, 'object', 'The test data is an object');
                assert.equal(typeof map, 'object', 'The test map is an object');
                assert.deepEqual(data, testData, 'The test data is correct');
                assert.equal(context.best, 'kilmister', 'The context gives you the best');
                assert.equal(map, testMap, 'The map is correct');

                this.destroy();
            })
            .on('destroy', function(){
                var context = this.getTestContext();
                var data    = this.getTestData();

                assert.equal(typeof context, 'object', 'The test context is an object');
                assert.equal(typeof data, 'object', 'The test data is an object');
                assert.deepEqual(data, testData, 'The test data is correct');
                assert.equal(typeof context.best, 'undefined', 'The context is now empty');

                QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('move next', function(assert){
       QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this.on('init', function(){
                    assert.ok(true, 'we can listen for init in providers init');
                    this.next();
                })
                .on('move', function(type){
                    assert.equal(type, 'next', 'The sub event is correct');
                    QUnit.start();
                });
            }
        });

        runnerFactory('foo')
            .init();
    });

    QUnit.asyncTest('move previous', function(assert){
       QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this.on('init', function(){
                    assert.ok(true, 'we can listen for init in providers init');
                    this.previous();
                })
                .on('move', function(type){
                    assert.equal(type, 'previous', 'The sub event is correct');
                    QUnit.start();
                });
            }
        });

        runnerFactory('foo')
            .init();
    });

    QUnit.asyncTest('jump', function(assert){
       QUnit.expect(4);

        var expectedScope = "section";
        var expectedPosition = 3;

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this.on('init', function(){
                    assert.ok(true, 'we can listen for init in providers init');
                    this.jump(expectedPosition, expectedScope);
                })
                .on('move', function(type, scope, position){
                    assert.equal(type, 'jump', 'The sub event is correct');
                    assert.equal(scope, expectedScope, 'The scope is correct');
                    assert.equal(position, expectedPosition, 'The position is correct');

                    QUnit.start();
                });
            }
        });

        runnerFactory('foo')
            .init();
    });

    QUnit.asyncTest('skip', function(assert){
       QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : _.noop
        });

        runnerFactory('foo')
            .on('ready', function(){
                assert.ok(true, 'The runner is ready');
                this.skip('section');
            })
            .on('move', function(){
                assert.ok(false, 'Skip is not a move');
            })
            .on('skip', function(scope){
                assert.equal(scope, 'section', 'The scope is correct');
                QUnit.start();
            })
            .init();
    });

    QUnit.asyncTest('timeout', function(assert){
        QUnit.expect(4);

        var expectedScope = 'assessmentSection';
        var expectedRef = 'assessmentSection-1';

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this.on('init', function(){
                        assert.ok(true, 'we can listen for init in providers init');
                        this.timeout(expectedScope, expectedRef)
                    })
                    .on('timeout', function(scope, ref){
                        assert.ok(true, 'The timeout event has been triggered');

                        assert.equal(scope, expectedScope, 'The timeout scope is provided');
                        assert.equal(ref, expectedRef, 'The timeout ref is provided');

                        QUnit.start();
                    });
            }
        });

        runnerFactory('foo')
            .init();
    });


    QUnit.asyncTest('exit', function(assert) {
        QUnit.expect(3);

        var expectedReason = 'the reason why';

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this
                    .on('init', function(){
                        assert.ok(true, 'we can listen for init in providers init');
                        this.exit(expectedReason);
                    })
                    .on('exit', function(why){
                        assert.ok(true, 'The exit event has been triggered');

                        assert.equal(why, expectedReason, 'The exit reason is provided');

                        QUnit.start();
                    });
            }
        });

        runnerFactory('foo')
            .init();
    });


    QUnit.asyncTest('pause and resume', function(assert) {
        QUnit.expect(5);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this
                    .on('init', function(){
                        assert.ok(true, 'we can listen for init in providers init');

                        this.pause();
                    })
                    .on('pause', function(){
                        assert.ok(true, 'The pause event has been triggered');
                        assert.ok(this.getState('pause'), 'The runner is paused');

                        this.resume();
                    })
                    .on('resume', function(){
                        assert.ok(true, 'The resume event has been triggered');
                        assert.ok(!this.getState('resume'), 'The runner is resumed');

                        QUnit.start();
                    });
            }
        });

        runnerFactory('foo').init();
    });

    QUnit.asyncTest('proxy', function(assert) {
        QUnit.expect(6);

        var expectedProxy = eventifier({
            init: function(){},
            destroy: function() {
                assert.ok(true, 'The proxy.destroy method has been called');
                return Promise.resolve();
            }
        });

        var expectedError = "an error";

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            loadProxy: function() {
                assert.ok(true, 'The loadProxy method has been called');
                return expectedProxy;
            },
            init : function init(){
                this
                    .on('init', function(){
                        assert.ok(true, 'we can listen for init in providers init');
                        this.destroy();
                    })
                    .on('error', function(error) {
                        assert.ok(true, 'The error event has been triggered');
                        assert.equal(error, expectedError, 'The right error is provided');
                    })
                    .on('destroy', function() {
                        assert.ok(true, 'The runner is destroying');
                        QUnit.start();
                    })
                    .getProxy().trigger('error', expectedError);
            }
        });

        runnerFactory('foo')
            .init();
    });

    QUnit.test('probeOverseer', function(assert) {
        QUnit.expect(2);

        var expectedProbeOverseer = {
            init: function(){},
            destroy: function() {}
        };

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            loadProbeOverseer: function() {
                assert.ok(true, 'The loadProbeOverseer method has been called');
                return expectedProbeOverseer;
            },
            init : function init(){}
        });

        var probeOverseer = runnerFactory('foo').getProbeOverseer();

        assert.equal(probeOverseer, expectedProbeOverseer, 'The right probeOverseer has been provided');
    });

    QUnit.test('no loadProxy', function(assert) {
        QUnit.expect(1);

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

            }
        });

        assert.throws(function() {
           runnerFactory('foo').getProxy();
        }, 'An exception is thrown when the loadAreaBroker() is missing');
    });

    QUnit.test('no loadAreaBroker', function(assert) {
        QUnit.expect(1);

        assert.throws(function() {
            runnerFactory.registerProvider('foo', {
                init : function init(){

                }
            });
        }, 'An exception is thrown when the loadAreaBroker() is missing');
    });


    QUnit.module('plugins', {
        setup: function(){
            runnerFactory.clearProviders();
        }
    });


    QUnit.asyncTest('initialize', function(assert){
       QUnit.expect(6);

        var boo = pluginFactory({
            name : 'boo',
            init : function init(){
                assert.ok(true, 'the plugin is initializing');
            }
        });

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){

                this.on('plugin-init.boo', function(plugin){
                    assert.equal(plugin, this.getPlugin('boo'), 'The event has a plugin in parameter');
                    assert.equal(typeof plugin, 'object', 'The event has a plugin in parameter');
                    assert.ok(plugin.getState('init'), 'The plugin is initialized');
                    QUnit.start();
                });
            }
        });

        runnerFactory('foo', {
            boo: boo
        })
        .on('ready', function(){
            assert.equal(typeof this.getPlugin('moo'), 'undefined', 'The moo plugin does not exist');
            assert.equal(typeof this.getPlugin('boo'), 'object', 'The boo plugin exists');
        })
        .init();
    });


    QUnit.asyncTest('persistent state', function(assert) {
        QUnit.expect(9);
        QUnit.stop(2);

        var states = {};

        var expectedName = 'pause';
        var expectedValue = true;

        runnerFactory.registerProvider('foo', {
            loadAreaBroker : function(){
                return {};
            },
            init : function init(){},
            getPersistentState : function(name) {
                assert.equal(name, expectedName, 'The getPersistentState() method has been delegated to the provider with the right name');
                return states[name];
            },
            setPersistentState : function(name, value) {
                assert.equal(name, expectedName, 'The setPersistentState() method has been delegated to the provider with the right name');
                assert.equal(value, expectedValue, 'The setPersistentState() method has been delegated to the provider with the right value');
                states[name] = value;
            }
        });

        runnerFactory('foo')
            .on('ready', function(){
                var self = this;
                this.setPersistentState(expectedName, 1).then(function() {
                    assert.ok(true, 'The setPersistentState() method has returned a promise that has been resolved');

                    assert.equal(self.getPersistentState(expectedName), expectedValue, 'The getPersistentState() method has returned the expected value');

                    runnerFactory('foo')
                        .on('ready', function(){
                            assert.equal(this.getPersistentState(expectedName), expectedValue, 'The state has persisted between runner instances');

                            QUnit.start();
                        })
                        .init();
                });

                this.setPersistentState().catch(function() {
                    assert.ok(true, 'The setPersistentState() method has returned a promise that has been rejected due to missing name');

                    QUnit.start();
                });

                this.setPersistentState('').catch(function() {
                    assert.ok(true, 'The setPersistentState() method has returned a promise that has been rejected due to empty name');

                    QUnit.start();
                });
            })
            .init();
    });
});
