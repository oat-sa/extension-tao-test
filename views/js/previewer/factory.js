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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Hanna Dzmitryieva <hanna@taotesting.com>
 */
define([
    'lodash',
    'context',
    'module',
    'core/providerLoader',
    'core/providerRegistry',
    'core/logger'
], function (
    _,
    context,
    module,
    providerLoaderFactory,
    providerRegistry
) {
    'use strict';

    /**
     * Loads and display the test previewer
     * @param {String} type - The type of previewer
     * @param {String} uri - The URI of the test to load
     * @param {Object} [config] - Some config entries
     * @param {String} [config.fullPage] - Force the previewer to occupy the full window.
     * @param {String} [config.readOnly] - Do not allow to modify the previewed test.
     * @param {Object} [config.previewers] - Optionally load static adapters. By default take them from the module's config.
     * @returns {Promise}
     */
    function previewerFactory(type, uri, config) {
        config = Object.assign({}, module.config(), config);
        return providerLoaderFactory()
            .addList(config.previewers)
            .load(context.bundle)
            .then(function (providers) {
                providers.forEach(function (provider) {
                    previewerFactory.registerProvider(provider.name, provider);
                });
            })
            .then(function () {
                return previewerFactory.getProvider(type);
            })
            .then(function (provider) {
                return provider.init(uri, config);
            });
    }

    return providerRegistry(previewerFactory, function validateProvider(provider) {
        if (typeof provider.init !== 'function') {
            throw new TypeError('The previewer provider MUST have a init() method');
        }
        return true;
    });
});
