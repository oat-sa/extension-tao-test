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
    'taoTests/runner/runner',
    'json!taoTests/test/runner/sample/minimalisticTest',
    'taoTests/test/runner/sample/minimalisticProvider'
], function($, _, runner, minimalisticTest, minimalisticProvider){
    'use strict';

    QUnit.module('runner', {
        setup : function(){
            runner.registerProvider(minimalisticProvider.name, minimalisticProvider);
        }
    });

    QUnit.test('module', 5, function(assert){
        assert.equal(typeof runner, 'function', "The runner module exposes a function");
        assert.equal(typeof runner(), 'object', "The runner factory produces an object");
        assert.notStrictEqual(runner(), runner(), "The runner factory provides a different object on each call");
        assert.equal(typeof runner.registerProvider, 'function', "The runner module exposes a function registerProvider()");
        assert.equal(typeof runner.getProvider, 'function', "The runner module exposes a function getProvider()");
    });


    var testReviewApi = [
        {name : 'init', title : 'init'},
        {name : 'ready', title : 'ready'},
        {name : 'load', title : 'load'},
        {name : 'terminate', title : 'terminate'},
        {name : 'endAttempt', title : 'endAttempt'},
        {name : 'next', title : 'next'},
        {name : 'previous', title : 'previous'},
        {name : 'exit', title : 'exit'},
        {name : 'skip', title : 'skip'},
        {name : 'jump', title : 'jump'},
        {name : 'registerAction', title : 'registerAction'},
        {name : 'execute', title : 'execute'},
        {name : 'request', title : 'request'},
        {name : 'beforeRequest', title : 'beforeRequest'},
        {name : 'processRequest', title : 'processRequest'},
        {name : 'afterRequest', title : 'afterRequest'},
        {name : 'is', title : 'is'},
        {name : 'trigger', title : 'trigger'},
        {name : 'on', title : 'on'}
    ];

    QUnit
        .cases(testReviewApi)
        .test('instance API', function(data, assert){
            var instance = runner();
            assert.equal(typeof instance[data.name], 'function', 'The runner instance exposes a "' + data.title + '" function');
        });

    QUnit.test('next/previous', function(assert){
        var $content = $('#test-content');
        var instance = runner(minimalisticProvider.name, {
            content : $content
        }).setState({
            pos : 0,
            definition : minimalisticTest
        }).init().renderContent();
        
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
            content : $content
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

});
