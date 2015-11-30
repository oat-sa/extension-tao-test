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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */
define(['lodash', 'jquery'], function(_, $){

    return [
        {
            name : 'itemValidator',
            desc : 'prevent invalid item from moving forward', //scaffolding item
            init : function(testRunner){
                testRunner.before('next previous submitresponse', function(testRunner){
                    return isItemValid(testRunner.getState().item);
                });
            }
        },
        {
            name : 'itemTimer',
            desc : 'timer item', //may be another one for section level or test level 
            init : function(testRunner){
                var timer = {};
                testRunner.on('itemready', function(testRunner){
                    var resumedTime;
                    timer.set(resumedTime).start();
                }).on('pause', function(testRunner){
                    timer.pause();
                }).on('resume', function(testRunner){
                    timer.resume();
                });

                timer.on('timeout', function(){
                    testRunner.gui.alert('expired', function(){
                        testRunner.next();
                    });
                });
            }
        },
        {
            name : 'someButton',
            desc : 'a "qti tool"', //mark for review, allow comment
            init : function(testRunner){
                var self = this;

                //reserve a location in the GUI
                this.$myButton = testRunner.gui.reservePosition('toolbar-bottom-left', 3);
                this.$myButton.append('<my template>');
                //init some js

                this.$myButton.on('click', function(){
                    //do stuff, show popup, call server
                });

                this.$myLeftBar = testRunner.gui.reservePosition('toolbar-left', 1);
                //init some js

                testRunner.before('itemready', function(testRunner){

                    self.enable();

                    var someCondition = true;
                    if(someCondition){
                        self.disable();
                    }

                });
            },
            destroy : function(testRunner){
                this.$myButton.remove();
                this.$myLeftBar.remove();
            }
        },
        {
            name : 'nextButton',
            desc : 'the move forward button', //same for move backward, end test etc.
            init : function(testRunner){

                var self = this;
                var $myContainer = testRunner.gui.reservePosition('toolbar-bottom-right', 2);
                $myContainer.append('<button>');
                $myContainer.click(function(){
                    testRunner.submitResponse().done(function(){
                        testRunner.trigger('next');
                    }).fail(function(){
                        log('error');
                        testRunner.trigger('next');
                    });
                });

                testRunner.on('itemready', function(testRunner){
                    var isLast = false;
                    if(isLast){
                        $myContainer.hide();
                    }
                });
            }
        },
        {
            name : 'responseSubmitter',
            desc : 'submit response', //same for move backward, end test etc.
            init : function(testRunner){
                //listen item response change

                testRunner.before('next', function(){
                    done()
                });
            }
        },
        {
            name : 'progressBar1',
            desc : 'progress bar', //same for move backward, end test etc.
            init : function(testRunner){
                var $myContainer = testRunner.gui.reservePosition('toolbar-top-center', 2);
                $myContainer.append('<button>');
                $myContainer.click(function(){
                    testRunner.trigger('next');
                });

                testRunner.on('itemready', function(testRunner){
                    var isLast = false;
                    if(isLast){
                        $myContainer.hide();
                    }
                });
            }
        },
        {
            name : 'title',
            desc : 'show item title', //same for move backward, end test etc.
            init : function(testRunner){
                var $myContainer = testRunner.gui.reservePosition('toolbar-top-center', 1);

                function updateTitle(){
                    var title = testRunner.getState().item.title;
                    $myContainer.html(title);
                }

                updateTitle();
                testRunner.on('itemready', updateTitle);
            }
        }
    ];
    
});