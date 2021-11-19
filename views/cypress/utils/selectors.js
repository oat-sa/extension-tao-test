export default {
    addTest: '[data-context="resource"][data-action="instanciate"]',
    addSubClassUrl: 'taoTests/Tests/addSubClass',

    deleteTest: '[data-context="resource"][data-action="removeNode"]',
    deleteClass: '[data-context="resource"][data-action="removeNode"]',
    deleteTestUrl: 'taoTests/Tests/delete',
    deleteConfirm: '[data-control="ok"]',

    editClassLabelUrl: 'taoTests/Tests/editClassLabel',
    editClassUrl: 'tao/PropertiesAuthoring/index',
    editItemUrl: 'taoTests/Tests/editTest',
    editTestUrl: 'taoTests/Tests/editTest',

    editClass: '#test-class-schema',

    moveClass: '[id="class-move-to"][data-context="class"][data-action="moveTo"]',
    moveConfirmSelector: 'button[data-control="ok"]',

    resourceGetAllUrl: 'tao/RestResource/getAll',
    root: '[data-uri="http://www.tao.lu/Ontologies/TAOTest.rdf#Test"]',

    testForm: 'form[action="/taoTests/Tests/editTest"]',
    testClassForm: 'form[action="/taoTests/Tests/editClassLabel"]',
    treeRenderUrl: 'taoTests/Tests',

    classOptions: '[action="/tao/PropertiesAuthoring/index"]',
    propertyEdit: 'div[class="form-group property-block regular-property property-edit-container-open"]'
};
