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


describe('Tests', () => {
    const newClassName = 'Test E2E class';
    const newTestName = 'Test E2E test';

    const selectors = {
        deleteTest: '[data-context="resource"][data-action="removeNode"]',
        deleteClass: '[data-context="resource"][data-action="removeNode"]',
        addTest: '[data-context="resource"][data-action="instanciate"]',
        testForm: 'form[action="/taoTests/Tests/editTest"]',
        testClassForm: 'form[action="/taoTests/Tests/editClassLabel"]',
        deleteConfirm: '[data-control="ok"]',
        root: '[data-uri="http://www.tao.lu/Ontologies/TAOTest.rdf#Test"]',
        nodeWithName: name => `li[title="${name}"] a`,
    }

    const testsUrl = '/tao/Main/index?structure=tests&ext=taoTests&section=manage_tests';

    /**
     * Log in
     * Visit the page
     */
    beforeEach(() => {
        cy.loginAsAdmin();

        cy.visit(testsUrl);

        cy.get(selectors.root).then(root => {
            if (root.find(selectors.nodeWithName(newClassName)).length === 0) {
                cy.addClass(selectors.testClassForm);
                cy.renameSelected(selectors.testClassForm, newClassName);
            }
        });
    });

    /**
     * Delete newly created tests after each step
     */
    afterEach(() => {
        cy.get(selectors.root).then(root => {
            if (root.find(selectors.nodeWithName(newClassName)).length > 0) {
                cy.deleteClass(selectors.testClassForm, selectors.deleteClass, selectors.deleteConfirm, newClassName);
            }
        });
    });

    /**
     * Tests
     */
    describe('Test creation, editing and deletion', () => {
        it('can create and rename a new test', function () {
            cy.selectNode(selectors.testClassForm, newClassName);
            cy.addNode(selectors.testForm, selectors.addTest);
            cy.renameSelected(selectors.testForm, newTestName);
        });

        it('can delete test', function () {
            cy.selectNode(selectors.testClassForm, newClassName);
            cy.addNode(selectors.testForm, selectors.addTest);
            cy.renameSelected(selectors.testForm, newTestName);
            cy.deleteNode(selectors.deleteTest, newTestName);
        });
    });
});
