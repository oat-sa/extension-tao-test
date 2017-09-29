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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA ;
 */

/**
 * Test the dataHolder
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'core/collections',
    'taoTests/runner/dataHolder',
], function (collections, dataHolderFactory){
    'use strict';

    QUnit.module('API');

    QUnit.test('module', function (assert){
        QUnit.expect(1);

        assert.equal(typeof dataHolderFactory, 'function', "The module exposes a function");
    });

    QUnit.test('factory', function (assert){
        var holder;
        QUnit.expect(2);

        holder =  dataHolderFactory();

        assert.equal(typeof holder, 'object', 'The factory creates an object');
        assert.ok(holder instanceof collections.Map, 'The holder is a common Map');
    });

    QUnit.test('defaults', function (assert){
        var holder;
        QUnit.expect(4);

        holder =  dataHolderFactory();

        assert.equal(typeof holder.get('testFoo'), 'undefined', 'testFoo is not a default');
        assert.equal(typeof holder.get('testData'), 'object', 'testData is a default');
        assert.equal(typeof holder.get('testContext'), 'object', 'testContext is a default');
        assert.equal(typeof holder.get('testMap'), 'object', 'testMap is a default');
    });
});
