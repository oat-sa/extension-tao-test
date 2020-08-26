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
 * Copyright (c) 2014 - 2020 (original work) Open Assessment Techniologies SA
 *
 */
define([
    'lodash',
    'context',
    'layout/actions/binder',
    'core/providerLoader',
    'uri',
    'ui/feedback',
    'core/logger'
], function(_, context, binder, providerLoaderFactory, uri, feedback, loggerFactory){
    'use strict';

    const logger = loggerFactory('taoTests/controller/action');

    let previewerFactory = null;
    providerLoaderFactory()
        .addList({
            previwers: {
                id: 'qtiTests',
                module: 'taoQtiTestPreviewer/previewer/adapter/test/qtiTest',
                bundle: 'taoQtiTestPreviewer/loader/qtiPreviewer.min',
                category: 'previwers'
            }
        })
        .load(context.bundle)
        .then(function (providers) {
            previewerFactory = providers[0];
        })
        .catch(err => {
            logger.error(err);
        });


    binder.register('testPreview', function testPreview(actionContext) {
        if (previewerFactory) {
            previewerFactory.init(uri.decode(actionContext.uri), {
                readOnly: false,
                fullPage: true
            });
        } else {
            feedback().error('Test Preview is not installed, please contact your administrator.');
        }
    });

});
