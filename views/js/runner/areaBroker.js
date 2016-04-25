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
 * Copyright (c) 2016 (original work) Open Assessment Technlogies SA
 *
 */

/**
 * The area broker is the object you need to define the Test Runner GUI areas.
 * You need to define some required areas so then everybody can attach elements to thos areas.
 * Each area is a jquery element.
 *
 * ! The mapping should be made prior to getting the areas !
 *
 * @example
 * var broker = areaBroker($container);
 * broker.defineAreas({
 *    content : $('.content', $container),
 *    //...
 * });
 * //then
 * var $content = broker.getArea('content');
 * var $content = broker.getContentArea();
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'core/areaBroker'
], function (_, areaBroker) {
    'use strict';

    var requireAreas = [
        'content',      //where the content is renderer, for example an item
        'toolbox',      //the place to add arbitrary tools, like a zoom, a comment box, etc.
        'navigation',   //the navigation controls like next, previous, skip
        'control',      //the control center of the test, progress, timers, etc.
        'header',       //the area that could contains the test titles
        'panel'         //a panel to add more advanced GUI (item review, navigation pane, etc.)
    ];

    /**
     * Creates an area broker with the required areas for the test runner.
     *
     * @see core/areaBroker
     *
     * @param {jQueryElement|HTMLElement|String} $container - the main container
     * @param {Object} mapping - keys are the area names, values are jQueryElement
     * @returns {broker} the broker
     * @throws {TypeError} without a valid container
     */
    return _.partial(areaBroker, requireAreas);

});
