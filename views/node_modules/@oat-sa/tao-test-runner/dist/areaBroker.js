define(['lodash', 'ui/areaBroker'], function (_, areaBroker$1) { 'use strict';

    _ = _ && _.hasOwnProperty('default') ? _['default'] : _;
    areaBroker$1 = areaBroker$1 && areaBroker$1.hasOwnProperty('default') ? areaBroker$1['default'] : areaBroker$1;

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
     * Copyright (c) 2016-2019 (original work) Open Assessment Technlogies SA
     *
     */
    var requireAreas = ['content', //where the content is renderer, for example an item
    'toolbox', //the place to add arbitrary tools, like a zoom, a comment box, etc.
    'navigation', //the navigation controls like next, previous, skip
    'control', //the control center of the test, progress, timers, etc.
    'header', //the area that could contains the test titles
    'panel' //a panel to add more advanced GUI (item review, navigation pane, etc.)
    ];
    /**
     * Creates an area broker with the required areas for the test runner.
     *
     * @see ui/areaBroker
     *
     * @param {jQueryElement|HTMLElement|String} $container - the main container
     * @param {Object} mapping - keys are the area names, values are jQueryElement
     * @returns {broker} the broker
     * @throws {TypeError} without a valid container
     */

    var areaBroker = _.partial(areaBroker$1, requireAreas);

    return areaBroker;

});
