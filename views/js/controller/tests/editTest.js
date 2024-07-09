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
 * Copyright (c) 2015-2024 (original work) Open Assessment Technologies SA;
 */

/**
 * Test edition controller
 *
 */
define(['jquery', 'ui/lock', 'module', 'layout/actions'], function ($, lock, module, actions) {
    'use strict';

    return {
        /**
         * Controller's entrypoint
         */
        start() {
            const config = module.config();
            const maxButtons = 10; // arbitrary value for the max number of buttons

            const getPreviewId = idx => `test-preview${idx ? `-${idx}` : ''}`;
            const previewActions = [];
            for (let i = 0; i < maxButtons; i++) {
                const action = actions.getBy(getPreviewId(i));
                if (!action) {
                    break;
                }
                previewActions.push(action);
            }
            previewActions.forEach(previewAction => {
                previewAction.state.disabled = !config.isPreviewEnabled;
            });
            actions.updateState();

            $('#lock-box').each(function () {
                lock($(this)).register();
            });
        }
    };
});
