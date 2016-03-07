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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define(['lodash', 'taoTests/runner/securityToken'], function(_, securityTokenFactory) {
    'use strict';

    QUnit.module('securityToken');

    QUnit.test('module', function(assert) {
        QUnit.expect(3);

        assert.equal(typeof securityTokenFactory, 'function', "The securityToken module exposes a function");
        assert.equal(typeof securityTokenFactory(), 'object', "The securityToken factory produces an object");
        assert.notStrictEqual(securityTokenFactory(), securityTokenFactory(), "The securityToken factory provides a different object on each call");
    });

    var proxyApi = [
        { name : 'getToken', title : 'getToken' },
        { name : 'setToken', title : 'setToken' }
    ];

    QUnit
        .cases(proxyApi)
        .test('instance API ', function(data, assert) {
            QUnit.expect(1);

            var instance = securityTokenFactory();
            assert.equal(typeof instance[data.name], 'function', 'The securityToken instance exposes a "' + data.name + '" function');
        });


    QUnit.test('setters', function(assert) {
        QUnit.expect(3);

        var securityToken = securityTokenFactory();
        var expectedToken ="e56fg1a3b9de2237f";

        assert.equal(securityToken.getToken(), undefined, 'There is no registered token in a fresh instance');

        assert.equal(securityToken.setToken(expectedToken), securityToken, 'The setToken method return the chain instance');

        assert.equal(securityToken.getToken(), expectedToken, 'The getToken method returns the right token');

    });
});
