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
    'core/promise',
    'taoTests/runner/plugin'
], function ($, Promise, pluginFactory){
    'use strict';

    return pluginFactory({
        name : 'previousButton',
        init : function init(){
            var self = this;
            var config = this.getConfig();
            var testRunner = this.getTestRunner();

            this.$button = $('<button class="previous"> &lt;&lt; Previous </button>');

            this.$button.click(function(){
                testRunner.previous();
            });

            testRunner.on('renderitem', function(){
                var context = this.getTestContext();
                if(context.current === 0){
                    self.disable();
                } else {
                    self.enable();
                }
            }).on('pause', function(){
                self.disable();
            }).on('resume', function(){
                self.enable();
            });

            // register the button in the navigation area
            this.getAreaBroker().addNavigationElement(this.getName(), this.$button);
        },
        destroy : function (){
            this.$button.remove();
        },
        enable : function (){
            this.$button.removeProp('disabled');
        },
        disable : function (){
            this.$button.prop('disabled', true);
        }
    });
});
