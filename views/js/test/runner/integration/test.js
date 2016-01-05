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
    'json!taoTests/test/runner/sample/minimalisticTest',
    'taoTests/test/runner/sample/minimalisticProvider',
    'taoTests/test/runner/sample/plugin/nextButton',
    'taoTests/test/runner/sample/plugin/responseSubmitter'
], function($, _, runner, minimalisticTest, minimalisticProvider, pluginNextButton, pluginResponseSubmitter){
    'use strict';

    QUnit.module('runner', {
        setup : function(){
            runner.registerProvider(minimalisticProvider.name, minimalisticProvider);
        }
    });

    QUnit.asyncTest('init', function(assert){

        var iteration = 1;
        var $content = $('#test-content');
        var $nextButton;
        var expectedResponse = {
                RESPONSE1 : 1,
                RESPONSE2 : ['A', 'B', 'C']
            };

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
            .on('ready', function(){


                //the test runner and the plugins are ready
                $nextButton = $('#navigation-bar .next');
                assert.equal($nextButton.length, 1, 'next button added');

            })
            .on('contentready', function(){

                switch(iteration){
                    case 1 :
                        assert.equal($content.html(), minimalisticTest.items[0].content, 'item 1 rendered');
                        $nextButton.click();
                        break;
                    case 2 :
                        assert.equal($content.html(), minimalisticTest.items[1].content, 'item 2 rendered');
                        $nextButton.click();
                        break;
                    case 3 :
                        assert.equal($content.html(), minimalisticTest.items[2].content, 'item 3 rendered');
                        $nextButton.click();
                        break;
                    case 4 :
                        assert.equal($content.html(), minimalisticTest.items[2].content, 'stays in item 3');
                        console.log('test complete', this.getState());
                        QUnit.start();
                        break;

                }
            })
            .on('submit.responseSubmitter', function(responses){

                switch(iteration){
                    case 1 :
                        assert.ok(true, 'response 1 submitted');
                        assert.deepEqual(expectedResponse, responses, 'response 1 ok');
                        break;
                    case 2 :
                        assert.ok(true, 'response 2 submitted');
                        assert.deepEqual(expectedResponse, responses, 'response 2 ok');
                        break;
                    case 3 :
                        assert.ok(true, 'response 3 submitted');
                        assert.deepEqual(expectedResponse, responses, 'response 3 ok');
                        break;
                }
            })
            .on('move', function(){
                //increase iteration number
                iteration ++;

                assert.equal($nextButton.length, 1, 'next button added');

                if(iteration === 3){

                }
            })
            .on('complete', function(){

            })
            .init()
            .renderContent();

    });

});
