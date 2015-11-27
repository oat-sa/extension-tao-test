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

define([] , function(){
    
    var providerPromise = {
        name : 'testDriverWithPromise',
        init : function(){
            return new Promise(function(resolve, reject){

            });
        },
        next : function(testRunner){
            return new Promise(function(resolve, reject){

                var newPos = testRunner.getState().pos + 1;

                //move pointer, compute the new state object
                if(newPos <= testContextSample.items.length){
                    var newState = {item : testContextSample.items[newPos], pos : newPos};
                    testRunner.setState(newState);
                    resolve();//implicit testRunner.loadItem();

                }else{
                    //end test
                    reject();
                    //implicit testRunner.endTest();
                }
            });
        }

    };

    var providerState = {
        interacting : {
            init : function(){
                //init item
            },
            exit : function(){
                //stop time
            }
        }
    };
});