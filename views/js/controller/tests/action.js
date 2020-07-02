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
    'layout/actions/binder',
    'taoQtiTestPreviewer/previewer/adapter/test/qtiTest',
    'uri'
], function(_, binder, previewerFactory, uri){
    'use strict';

    binder.register('testPreview', function testPreview(actionContext) {

        previewerFactory.init(uri.decode(actionContext.uri), {
            readOnly: false,
            fullPage: true
        });
    });

});
