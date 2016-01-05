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
/**
 * @author Sam <sam@taotesting.com>
 */
define([], function(){
    'use strict';

    return {
        name : 'minimalTestRunner',
        init : function init(){

        this
            .on('next', function(){

                var state = this.getState();
                var newPos = state.pos + 1;

                //move pointer, compute the new state object
                if(newPos < state.definition.items.length){
                    state.pos = newPos;
                    this.setState(state);
                }else{
                    //end test
                    this.complete();
                }

            })
            .on('previous', function(){

                var state = this.getState();
                var newPos = state.pos - 1;

                //move pointer, compute the new state object
                if(newPos >= 0){
                    state.pos = newPos;
                    this.setState(state);
                }else{
                    //the first item ? do nothing
                    //or you can log a warning if you want to...
                }

            })
            .on('jump', function(pos){
                var state = this.getState();
                //check if the jump "pos" is valid
                var valid = (0 <= pos && pos < state.definition.items.length);
                if(valid){
                    state.pos = pos;
                    this.setState(state);
                }else{
                    //log warning ?
                }
            });

            this.ready();
        },
        renderContent : function renderContent($container){
            console.log(this);
            var state = this.getState();
            var definition = state.definition;
            var item = definition.items[state.pos];

            //load item data, prepare rendering
            $container.html(item.content);

            //notifiy test runner that the item ready
            this.contentReady();
        }
    };
});
