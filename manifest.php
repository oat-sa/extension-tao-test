<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
return array(
	'name' => 'taoTests',
	'description' => 'TAO Tests extension',
	'additional' => array(
		'version' => '2.0',
		'author' => 'CRP Henri Tudor',
		'dependances' => array('wfEngine', 'taoItems'),
		'models' => array('http://www.tao.lu/Ontologies/TAOTest.rdf',
			'http://www.tao.lu/Ontologies/taoFuncACL.rdf'),
		'install' => array(
			'rdf' => array(
					array('ns' => 'http://www.tao.lu/Ontologies/TAOTest.rdf', 'file' => dirname(__FILE__). '/models/ontology/taotest.rdf'),
					array('ns' => 'http://www.tao.lu/Ontologies/taoFuncACL.rdf', 'file' => dirname(__FILE__). '/models/ontology/funcacl.rdf')
			)
		),
		'classLoaderPackages' => array(
			dirname(__FILE__).'/actions/',
			dirname(__FILE__).'/helpers/'
		 )
	)
);
?>