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
 * Copyright (c) 2017 (original work) Open Assessment Technlogies SA
 *
 */

/**
 * Holds the test runner data.
 *
 * @example
 * var holder = holder();
 * holder.get('testMap');
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'core/collections'
], function (collections) {
    'use strict';

    /**
     * @type {String[]} the list of default objects to create
     */
    var defaultObjects = ['testData', 'testContext', 'testMap'];

    /**
     * Creates a new data holder,
     * with default entries.
     *
     * @returns {Map} the holder
     */
    return function dataHolderFactory(){
        var map = new collections.Map();
        defaultObjects.forEach(function(entry){
            map.set(entry, {});
        });

        return map;
    };
});
