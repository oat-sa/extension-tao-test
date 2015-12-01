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
    'taoTests/runner/plugin'
], function ($, _, runner, pluginFactory){
    'use strict';

    QUnit.module('plugin');

    QUnit.test('module', 3, function (assert){
        assert.equal(typeof pluginFactory, 'function', "The plugin module exposes a function");
        assert.equal(typeof pluginFactory(), 'function', "The plugin factory produces a function");
        assert.notStrictEqual(pluginFactory(), pluginFactory(), "The plugin factory provides a different object on each call");
    });

    QUnit.test('create a plugin', 13, function (assert){

        var samplePluginDefaults = {
            a : false,
            b : 10
        };

        var myPlugin = function myPlugin(config){

            var samplePluginImpl = {};

            var myPluginFactory = pluginFactory(samplePluginImpl, samplePluginDefaults);

            return myPluginFactory(config);
        };

        assert.equal(typeof myPlugin(), 'object', "My plugin factory produce a plugin instance object");
        assert.notStrictEqual(myPlugin(), myPlugin(), "My plugin factory provides different object on each call");

        var _config2 = {
            a : true,
            b : 999
        };

        var instance1 = myPlugin();
        assert.equal(typeof instance1.init, 'function', 'The plugin instance has also the default function init');
        assert.equal(typeof instance1.destroy, 'function', 'The plugin instance has also the default function destroy');
        assert.equal(typeof instance1.show, 'function', 'The plugin instance has also the default function show');
        assert.equal(typeof instance1.hide, 'function', 'The plugin instance has also the default function hide');
        assert.equal(typeof instance1.enable, 'function', 'The plugin instance has also the default function enable');
        assert.equal(typeof instance1.disable, 'function', 'The plugin instance has also the default function disable');
        assert.equal(typeof instance1.state, 'function', 'The plugin instance has also the default function state');

        // check default config
        var config1 = instance1.getConfig();
        assert.equal(config1.a, samplePluginDefaults.a, 'instance1 inherits the default config');
        assert.equal(config1.b, samplePluginDefaults.b, 'instance1 inherit the default config');

        // check overwritten config
        var instance2 = myPlugin(_config2);
        var config2 = instance2.getConfig();
        assert.equal(config2.a, _config2.a, 'instance2 has new config value');
        assert.equal(config2.b, _config2.b, 'instance2 has new config value');

    });

    QUnit.test('call plugin methods', 9, function (assert){

        var myPlugin = function myPlugin(config){

            var samplePluginDefaults = {
                a : false,
                b : 10
            };

            var samplePluginImpl = {
                init : function (cfg){
                    assert.ok(true, 'called init');
                    assert.equal(cfg.a, samplePluginDefaults.a, 'instance1 inherits the default config');
                    assert.equal(cfg.b, samplePluginDefaults.b, 'instance1 inherit the default config');
                },
                destroy : function (){
                    assert.ok(true, 'called destory');
                },
                show : function (){
                    assert.ok(true, 'called show');
                },
                hide : function (){
                    assert.ok(true, 'called hide');
                },
                enable : function (){
                    assert.ok(true, 'called enable');
                },
                disable : function (){
                    assert.ok(true, 'called disable');
                }
            };

            var myPluginFactory = pluginFactory(samplePluginImpl, samplePluginDefaults);

            return myPluginFactory(config);
        };

        assert.equal(typeof myPlugin(), 'object', "My plugin factory produce a plugin instance object");

        var instance1 = myPlugin();
        instance1.init();
        instance1.hide();
        instance1.show();
        instance1.disable();
        instance1.enable();
        instance1.destroy();
    });
    
    QUnit.test('state', 13, function (assert){

        var myPlugin = function myPlugin(config){
            return pluginFactory()(config);
        };
        
        assert.equal(typeof myPlugin(), 'object', "My plugin factory produce a plugin instance object");

        var instance1 = myPlugin();
        
        //custom state : active
        assert.strictEqual(instance1.state('active'), false, 'no state set by default');
        instance1.state('active', true);
        assert.strictEqual(instance1.state('active'), true, 'active state set');
        instance1.state('active', false);
        assert.strictEqual(instance1.state('active'), false, 'no state set by default');
        
        //built-in state init:
        assert.strictEqual(instance1.state('init'), false, 'init state = false by default');
        instance1.init();
        assert.strictEqual(instance1.state('init'), true, 'init state set');
        
        //built-in visible state
        assert.strictEqual(instance1.state('visible'), false, 'visible state = false by default');
        instance1.show();
        assert.strictEqual(instance1.state('visible'), true, 'visible state set');
        instance1.hide();
        assert.strictEqual(instance1.state('visible'), false, 'visible turns to false');
        
        //built-in enabled state
        assert.strictEqual(instance1.state('enabled'), false, 'enabled state = false by default');
        instance1.enable();
        assert.strictEqual(instance1.state('enabled'), true, 'enabled state set');
        instance1.disable();
        assert.strictEqual(instance1.state('enabled'), false, 'enabled turns to false');
        
        //built-in init state
        instance1.destroy();
        assert.strictEqual(instance1.state('init'), false, 'destoyed state set');
    });

});
