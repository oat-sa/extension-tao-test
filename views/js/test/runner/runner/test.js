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
    'taoTests/runner/runner',
    'taoTests/runner/plugin'
], function($, _, runnerFactory, pluginFactory){
    'use strict';

    var mockProvider = {
        init : _.noop
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
        {name : 'ready', title : 'ready'},
        {name : 'load', title : 'load'},
        {name : 'terminate', title : 'terminate'},
        {name : 'next', title : 'next'},
        {name : 'previous', title : 'previous'},
        {name : 'exit', title : 'exit'},
        {name : 'skip', title : 'skip'},
        {name : 'jump', title : 'jump'},
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
            init : function init(){
               assert.equal(this.bar, 'baz', 'The provider is executed on the runner context');
               QUnit.start();
            }
        });

        var runner = runnerFactory('foo');
        runner.bar = 'baz';
        runner.init();
    });

    QUnit.asyncTest('state access', function(assert){
       QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            init : function init(){
               var currentState = this.getState();
               assert.equal(typeof currentState, 'object', 'The provider has access to the state');
               assert.equal(currentState.moo, 'boo', 'The state is correct');
               QUnit.start();
            }
        });

        runnerFactory('foo')
            .setState({'moo' : 'boo'})
            .init();
    });

    QUnit.module('events', {
        setup: function(){
            runnerFactory.clearProviders();
        }
    });

    QUnit.asyncTest('move next', function(assert){
       QUnit.expect(2);

        runnerFactory.registerProvider('foo', {
            init : function init(){

                this.on('init', function(){
                    assert.ok(true, 'we can listen for init in providers init');
                })
                .on('move', function(type){
                    assert.equal(type, 'next', 'The sub event is correct');
                    QUnit.start();
                });
            }
        });

        runnerFactory('foo')
            .init()
            .next();
    });


    QUnit.module('plugins', {
        setup: function(){
            runnerFactory.clearProviders();
        }
    });


    QUnit.asyncTest('initialize', function(assert){
       QUnit.expect(2);

        var boo = pluginFactory({
            name : 'boo',
            init : function init(){
                assert.ok(true, 'the plugin is initializing');
            }
        });

        runnerFactory.registerProvider('foo', {
            init : function init(){

                this.on('init.boo', function(){
                    assert.ok(true, 'the boo plugin is initialized');
                    QUnit.start();
                });
            }
        });

        runnerFactory('foo', {
            plugins: [boo]
        })
        .init()
        .next();
    });
/*

    QUnit.test('next/previous', function(assert){

        var $content = $('#test-content');
        var instance = runner(minimalisticProvider.name, {
            contentContainer : $content
        });
        instance
        .setState({
            pos : 0,
            definition : minimalisticTest
        })
        .init()
        .renderContent();

        assert.equal($content.html(), minimalisticTest.items[0].content, 'item 1 rendered');

        instance.next();
        assert.equal($content.html(), minimalisticTest.items[1].content, 'item 2 rendered');

        instance.next();
        assert.equal($content.html(), minimalisticTest.items[2].content, 'item 3 rendered');

        instance.next();
        assert.equal($content.html(), minimalisticTest.items[2].content, 'stayed on the last item');

        instance.previous();
        assert.equal($content.html(), minimalisticTest.items[1].content, 'back to item 2');

        instance.previous();
        assert.equal($content.html(), minimalisticTest.items[0].content, 'back to item 1');

        instance.previous();
        assert.equal($content.html(), minimalisticTest.items[0].content, 'stayed on item 1');
    });

    QUnit.test('jump', function(assert){
        var $content = $('#test-content');
        var instance = runner(minimalisticProvider.name, {
            contentContainer : $content
        }).setState({
            pos : 0,
            definition : minimalisticTest
        }).init().renderContent();

        assert.equal($content.html(), minimalisticTest.items[0].content, 'item 1 rendered');

        instance.jump(2);
        assert.equal($content.html(), minimalisticTest.items[2].content, 'item 3 rendered');

        instance.previous();
        assert.equal($content.html(), minimalisticTest.items[1].content, 'item 2 rendered');

        instance.next();
        assert.equal($content.html(), minimalisticTest.items[2].content, 'item 3 rendered');

        instance.jump(0);
        assert.equal($content.html(), minimalisticTest.items[0].content, 'item 2 rendered');
    });
*/
});
