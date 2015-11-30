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
 * @author Sam <sam@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'taoTests/runner/runner',
    'taoTests/runner/gui',
    'json!taoTests/test/runner/sample/minimalisticTest',
    'taoTests/test/runner/sample/minimalisticProvider',
    'taoTests/test/runner/sample/plugin/plugins'
], function($, _, runner, gui, minimalisticTest, minimalisticProvider, plugins){
    'use strict';

    QUnit.module('runner', {
        setup : function(){
            runner.registerProvider(minimalisticProvider.name, minimalisticProvider);
        }
    });

    QUnit.test('init', function(assert){
        
        var pluginResponseSubmitter = _.find(plugins, {name : 'responseSubmitter'});
        var pluginNextButton = _.find(plugins, {name : 'nextButton'});
        var $content = $('#test-content');
        var $content = $('#action-bar');
        var instance = runner(minimalisticProvider.name, {
            content : $content,
            plugins : [
                pluginNextButton.config({
                    container : gui('#test-driver-container').getToolbar().getPosition(2),
                    label : __('next')
                }),
                pluginResponseSubmitter.config({
                    url : 'some/end/point/url'
                })
            ]
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

});
