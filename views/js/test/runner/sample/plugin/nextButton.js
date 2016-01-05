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
    'taoTests/runner/plugin'
], function ($, pluginFactory){
    'use strict';

    return pluginFactory({
        name : 'nextButton',
        init : function init(){

            var config = this.getConfig();
            var testRunner = this.getTestRunner();
            var $container = $(config.container);
            var $button = $('<button class="next">');
            this.$button = $button;

            //create button
            $container.append($button);
            $button.click(function(){
                testRunner.next();
            });

            //event handler
            testRunner.on('itemready', function(){
                var state = this.getState();
                var isLast = false;//can get this information from test runner's state var
                if(isLast){
                    $button.hide();
                }
            });
        },
        destroy : function (){
            this.$button.remove();
        },
        show : function (){
            this.$button.show();
        },
        hide : function (){
            this.$button.hide();
        },
        enable : function (){
            this.$button.removeClass('disable');
        },
        disable : function (){
            this.$button.addClass('disable');
        }
    });
});
