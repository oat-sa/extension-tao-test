export default {
    deleteTest: '[data-context="resource"][data-action="removeNode"]',
    deleteClass: '[data-context="resource"][data-action="removeNode"]',
    moveClass: '[id="item-move-to"][data-context="resource"][data-action="moveTo"]',
    moveConfirmSelector: 'button[data-control="ok"]',
    addTest: '[data-context="resource"][data-action="instanciate"]',
    testForm: 'form[action="/taoTests/Tests/editTest"]',
    testClassForm: 'form[action="/taoTests/Tests/editClassLabel"]',
    deleteConfirm: '[data-control="ok"]',
    root: '[data-uri="http://www.tao.lu/Ontologies/TAOTest.rdf#Test"]'
};
