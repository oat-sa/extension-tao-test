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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
define([
    'jquery',
    'i18n',
    'ui/filter',
    'ui/feedback'
], function ($, __, filterFactory, feedback) {
    'use strict';

    return {
        /**
         * Enhances a hidden form field, rendering a text input with filter, autocomplete and dropdown
         * @param {Object} options
         * @param {jQuery} options.$filterContainer
         * @param {jQuery} options.$inputElement
         * @param {taskQueueButton} options.taskButton - button which submits the form
         * @param {Function} options.dataProvider - provider function which returns a Promise
         * @param {String} options.inputPlaceholder
         * @param {String} options.inputLabel
         * @returns {filter} component which manages the form input
         */
        createSelectorInput({
                                $filterContainer,
                                $inputElement,
                                dataProvider,
                                inputPlaceholder = __('Select the item destination class'),
                                inputLabel = __('Select Item Destination')
                            }) {
            return filterFactory($filterContainer, {
                placeholder: inputPlaceholder,
                label: inputLabel,
                width: '64%',
                quietMillis: 1000
            })
                .on('change', function(selection) {
                    $inputElement.val(selection);
                })
                .on('request', function(params) {
                    dataProvider
                        .list(params.data)
                        .then(function(data) {
                            params.success(data);
                        })
                        .catch(function(err) {
                            params.error(err);
                            feedback().error(err);
                        });
                })
                .render('<%- text %>');
        },

        /**
         * Set up the wizard form for publishing a TAO Local delivery
         * @param {jQuery} $form
         * @param {Object} providers - contains function(s) for fetching data
         */
        setupTaoLocalForm($form, providers) {
            const $filterContainer = $('.item-select-container', $form);
            const $inputElement = $('#itemClassDestination', $form);

            // Enhanced selector input for tests:
            this.createSelectorInput({
                $filterContainer,
                $inputElement,
                dataProvider: {
                    list: providers.listTests
                }
            });
        }
    };
});
