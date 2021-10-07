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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA ;
 */

import urls from '../utils/urls';
import selectors from '../utils/selectors';

describe('Tests', () => {
    const className = 'Test E2E class';
    const classMovedName = 'Test E2E class Moved';
    const newPropertyName = 'I am a new property in testing, hi!';

    /**
     * Log in and wait for render
     * After @treeRender click root class
     */
    before(() => {
        cy.loginAsAdmin();
        cy.intercept('GET', `**/${ selectors.treeRenderUrl }/getOntologyData**`).as('treeRender');
        cy.intercept('POST', `**/${ selectors.editClassLabelUrl }`).as('editClassLabel');
        cy.visit(urls.tests);
        cy.wait('@treeRender');
        cy.get(`${selectors.root} a`)
            .first()
            .click();
        cy.wait('@editClassLabel');
    });

    /**
     * Tests
     */
    describe('Test Class creation and editing', () => {
        it('can create a new test class', function () {
            cy.addClassToRoot(
                selectors.root,
                selectors.testClassForm,
                className,
                selectors.editClassLabelUrl,
                selectors.treeRenderUrl,
                selectors.addSubClassUrl
            );
        });

        it('can edit and add new property for the class', function () {
            cy.addPropertyToClass(
                className,
                selectors.editClass,
                selectors.classOptions,
                newPropertyName,
                selectors.propertyEdit,
                selectors.editClassUrl
            );
        });
    });

    describe('Test creation and edition', () => {
        it('can create and rename a new test', function () {
            cy.selectNode(selectors.root, selectors.testClassForm, className)
                .addNode(selectors.testForm, selectors.addTest)
                .renameSelectedNode(selectors.testForm, selectors.editTestUrl, 'Test E2E test 1');
        });
    });

    describe('Moving and deleting', () => {
        it('can delete test', function () {
            cy.selectNode(selectors.root, selectors.testClassForm, className)
                .addNode(selectors.testForm, selectors.addTest)
                .renameSelectedNode(selectors.testForm, selectors.editTestUrl,'Test E2E test 2')
                .deleteNode(
                    selectors.root,
                    selectors.deleteTest,
                    selectors.editTestUrl,
                    'Test E2E test 2',
                );
        });

        it('can move test class', function () {
            cy.intercept('POST', `**/${ selectors.editClassLabelUrl }`).as('editClassLabel');

            cy.getSettled(`${selectors.root} a:nth(0)`)
            .click()
            .wait('@editClassLabel')
            .addClass(selectors.testClassForm, selectors.treeRenderUrl, selectors.addSubClassUrl)
            .renameSelectedClass(selectors.testClassForm, classMovedName);

            cy.wait('@treeRender');

            cy.moveClassFromRoot(
                selectors.root,
                selectors.moveClass,
                selectors.moveConfirmSelector,
                className,
                classMovedName,
                selectors.restResourceGetAll,
            );
        });

        it('can delete test class', function () {
            cy.deleteClassFromRoot(
                selectors.root,
                selectors.testClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                classMovedName,
                selectors.deleteTestUrl,
            );
        });

        it('can delete empty test class', function () {
            cy.intercept('POST', `**/${ selectors.editClassLabelUrl }`).as('editClassLabel')

            cy.getSettled(`${selectors.root} a:nth(0)`)
            .click()
            .wait('@editClassLabel')
            .addClass(selectors.testClassForm, selectors.treeRenderUrl, selectors.addSubClassUrl)
            .renameSelectedClass(selectors.testClassForm, className);

            cy.wait('@editClassLabel');

            cy.deleteClassFromRoot(
                selectors.root,
                selectors.testClassForm,
                selectors.deleteClass,
                selectors.deleteConfirm,
                className,
                selectors.deleteTestUrl,
                false
            );
        });
    });
});
