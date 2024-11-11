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
    'module',
    'uri',
    'layout/actions',
    'layout/actions/binder',
    'layout/section',
    'form/translation',
    'services/translation',
    'ui/feedback',
    'core/logger',
    'taoTests/previewer/factory'
], function (
    __,
    module,
    uri,
    actionManager,
    binder,
    section,
    translationFormFactory,
    translationService,
    feedback,
    loggerFactory,
    previewerFactory
) {
    'use strict';

    const logger = loggerFactory('taoTests/controller/action');

    binder.register('translateTest', function (actionContext) {
        section.current().updateContentBlock('<div class="main-container flex-container-full"></div>');
        const $container = $('.main-container', section.selected.panel);
        const { rootClassUri, id: resourceUri } = actionContext;
        translationFormFactory($container, { rootClassUri, resourceUri, allowDeletion: true })
            .on('edit', (id, language) => {
                return actionManager.exec('test-authoring', {
                    id,
                    language,
                    rootClassUri,
                    originResourceUri: resourceUri,
                    translation: true,
                    actionParams: ['originResourceUri', 'language', 'translation']
                });
            })
            .on('delete', function onDelete(id, language) {
                return translationService.deleteTranslation(resourceUri, language).then(() => {
                    feedback().success(__('Translation deleted'));
                    return this.refresh();
                });
            })
            .on('error', error => {
                logger.error(error);
                feedback().error(__('An error occurred while processing your request.'));
            });
    });

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
