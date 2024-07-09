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
 * Copyright (c) 2014 - 2020 (original work) Open Assessment Technologies SA
 *
 */
define([
    'i18n',
    'layout/actions/binder',
    'uri',
    'ui/feedback',
    'core/logger',
    'taoTests/previewer/factory',
    'module'
], function (__, binder, uri, feedback, loggerFactory, previewerFactory, module) {
    'use strict';

    const logger = loggerFactory('taoTests/controller/action');

    binder.register('testPreview', function testPreview(actionContext) {
        const config = module.config();
        const previewerConfig = Object.fromEntries(
            Object.entries({
                readOnly: false,
                fullPage: true,
                pluginsOptions: config.pluginsOptions
            }).filter(([key, value]) => value !== undefined)
        );

        const getProvider = id => {
            if (!id || !config.providers) {
                return config.provider;
            }
            const previewerId = parseInt(`${id}`.split('-').pop(), 10) || 0;
            if (!config.providers[previewerId]) {
                return config.provider;
            }
            return config.providers[previewerId].id;
        };

        previewerFactory(getProvider(this.id) || 'qtiTest', uri.decode(actionContext.uri), previewerConfig).catch(
            err => {
                logger.error(err);
                feedback().error(__('Test Preview is not installed, please contact to your administrator.'));
            }
        );
    });
});
