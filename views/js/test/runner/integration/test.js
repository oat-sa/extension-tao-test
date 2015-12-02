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
    'taoTests/test/runner/sample/plugin/nextButton',
    'taoTests/test/runner/sample/plugin/responseSubmitter'
], function($, _, runner, gui, minimalisticTest, minimalisticProvider, pluginNextButton, pluginResponseSubmitter){
    'use strict';

    QUnit.module('runner', {
        setup : function(){
            runner.registerProvider(minimalisticProvider.name, minimalisticProvider);
        }
    });

    QUnit.asyncTest('init', 0, function(assert){

        var $content = $('#test-content');
        var instance = runner(minimalisticProvider.name, {
                contentContainer : $content,//also accepts a selector, e.g. #test-content
                plugins : [
                    pluginNextButton({
                        container : '#navigation-bar',
                        label : 'next'
                    }),
                    pluginResponseSubmitter({
                        url : 'some/end/point/url'
                    })
                ]
            })
            .setState({
                pos : 0,
                definition : minimalisticTest
            })
            .init()
            .renderContent()
            .on('submit.responseSubmitter', function(responses){
                QUnit.start();
                console.log('submit.responseSubmitter', responses);
            });

        return;
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
