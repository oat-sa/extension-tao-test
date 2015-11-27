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
define([], function(){

    var minimal = {
        name : 'minimalTestRunner',
        init : function init(){

            this.on('ready', function(){

                    //the plugins are ready

                    //the DOM and GUI are also ready

                    //render item to this.gui.itemContainer
                    this.renderContent();
                })
                .after('move', function(type){
                    //refresh page content after each refresh
                    this.renderContent();
                })
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
                        //log warning ?
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

        },
        renderContent : function renderContent($container){
            var state = this.getState();
            var definition = state.definition;
            var item = definition.items[state.pos];

            //load item data, prepare rendering
            $container.html(item.content);

            //notifiy test runner that the item ready
            this.contentReady();
        }
    };

    return minimal;
});