<?php

/**
* Builds and save xml of a test using posted data
* @package Widgets.etesting.authoringTest
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/

class TAOTsaveContent
{
	function TAOTsaveContent()
	{
		$session = core_kernel_classes_Session::singleton();
		$session->model->loadModel('http://www.tao.lu/Ontologies/TAOItem.rdf');
	}
	
	function getOutput($ressource)
	{
		error_reporting(E_ALL ^ E_NOTICE);
		
		
		$xul="";
		$instance = $ressource["instance"];
		$property = $ressource["property"];
		$output="";
		
		$test = new core_kernel_classes_Resource($instance);
		
		$label = $test->getLabel();
		$comment = $test->comment;
		
		$completeid = $_SERVER['DOCUMENT_ROOT']	;	
		
		(isset($_SESSION["datalg"])) ? $lang = $_SESSION["datalg"] : $lang = $GLOBALS['lang'];
		
		$script = $instance;
	
		error_reporting(E_ALL ^ E_NOTICE);
		$params ='';
		if(isset($ressource["tao:showprogressbar"])){
			if ($ressource["tao:showprogressbar"]=="on") {$params.='<progressmeter id="test_progressmeter" left="'.$ressource["progressbarleft"].'" top="'.$ressource["progressbartop"].'" mode="determined" value="0"/>';}
		}
		if(isset($ressource["tao:showlistbox"])){
			if ($ressource["tao:showlistbox"]=="on") {$params.='<listbox id="testItems_listbox" left="10" top="10" height="400" width="100"/>';}
		}
		$labelandcomment ='';
if (isset($ressource["tao:showLabel"])) 
	{$labelandcomment .='
<label id="testLabel_label" left="10" top="10" class="Label" value="&lt;b&gt;&lt;u&gt;#{XPATH(/tao:TEST/rdfs:LABEL)}#&lt;/u&gt;&lt;/b&gt;" style=\'\'/>
';
	}
if (isset($ressource["tao:showComment"])) 
		{
$labelandcomment .='
								<label id="testComment_label" left="25" top="30" class="Comment" value="#{XPATH(/tao:TEST/rdfs:COMMENT)}#"/>';
		}
		
		if 
					(
					(!(isset($ressource["urlleft"])))
					or
					($ressource["urlleft"]=="")
					) 
					{$ressource["urlleft"]="http://www.tao.lu/middleware/itempics/default/left.swf";}
				
				if 
					(
					(!(isset($ressource["urlright"])))
					or
					($ressource["urlright"]=="")
					) 
					{$ressource["urlright"]="http://www.tao.lu/middleware/itempics/default/right.swf";}

		//default top position for back, next and progress bar
		if ((!(isset($ressource["navtop"])))or($ressource["navtop"]=="")) {$ressource["navtop"]="400";}

		//default left position for back, next and progress bar
		if ((!(isset($ressource["navleft"]))) or ($ressource["navleft"]=="")) {$ressource["navleft"]="200";}
		$ressource["navleftnext"]=$ressource["navleft"];
		$ressource["navleftprogressbar"]=$ressource["navleftnext"]+60;
		
		$backbutton='';
		if (($ressource["TAO:HASSEQUENCEMODE"]!="MAXFISHER") and ($ressource["deactivateback"]!="on"))
		{
			$ressource["navleft"] = $ressource["navleft"]-70;
			$backbutton='<button id="prevItem_button" left="'.$ressource["navleft"].'" url="'.$ressource["urlleft"].'" top="'.$ressource["navtop"].'" label="Back" image="item_previous.jpg" disabled="true" oncommand="tao_test.prevItem"/>';
		}
		
		$xul.='<tao:TESTPRESENTATION><xul><stylesheet id="test_stylesheet" src="./test.css"/><box id="testContainer_box">'.$labelandcomment.'<box id="itemContainer_box" left="100" top="65"/>
		'.$backbutton.'
		<button id="nextItem_button" left="'.$ressource["navleftnext"].'" url="'.$ressource["urlright"].'" top="'.$ressource["navtop"].'" label="Next" image="item_next.jpg" disabled="true" oncommand="tao_test.nextItem"/>'.$params.'
        		<box id="testLanguages_box" left="10" top="475">
        		    <button id="language_FR_button" left="0" top="0" label="FR" image="flag_FR.jpg" disabled="false" oncommand="tao_test.setLang(FR)"/>
        		    <button id="language_DE_button" left="60" top="0" label="DE" image="flag_DE.jpg" disabled="false" oncommand="tao_test.setLang(DE)"/>
        		</box></box></xul></tao:TESTPRESENTATION>';
		

/**
Halt criteria selection
*/
$haltcriteriaoutput="";

if  ($ressource["TAO:HALTCRITERIA"]=="DELTASCORE") {$haltcriteriaoutput='<tao:HALTCRITERIA max="'.$ressource["max"].'" value="DELTASCORE">
<tao:PARAM name="threshold" value="'.$ressource["deltascorethreshold"].'"/>
</tao:HALTCRITERIA>
';};
if  ($ressource["TAO:HALTCRITERIA"]=="DELTASE") {$haltcriteriaoutput='<tao:HALTCRITERIA value="DELTASE">
<tao:PARAM name="threshold" value="'.$ressource["deltascorethreshold"].'"/>
</tao:HALTCRITERIA>
';};


$scoringmethodsparams='Qmin="'.$ressource["qmin"].'" Qmax="'.$ressource["qmax"].'" Qiter="'.$ressource["qiter"].'"';

if ($ressource["TAO:HASSCORINGMETHOD"]=="CLASSICALRATIO")
	{$ressource["TAO:HASSCORINGMETHOD"]="CLASSICAL RATIO";}
if ($ressource["TAO:HASSCORINGMETHOD"]=="EXPECTEDAPOSTERIORI")
	{$ressource["TAO:HASSCORINGMETHOD"]="EXPECTED A POSTERIORI";}

$delay="";
if (isset($ressource["delay"]) and ($ressource["delay"]!="")) {$delay='delay="'.$ressource["delay"].'" delaypolicy="sequential" firstselection="montecarlo"';}
$tresholds ="<tao:LAUNCH plugin=\"CLLPlugin\">";
$tresholds.="<cll:threshold>".$ressource["tresh1"]."</cll:threshold>";
$tresholds.="<cll:threshold>".$ressource["tresh2"]."</cll:threshold>";
$tresholds.="<cll:threshold>".$ressource["tresh3"]."</cll:threshold>";
$tresholds .="</tao:LAUNCH>";
		$xml=
			
"<?xml version='1.0' encoding='UTF-8' ?>
<tao:TEST xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID=\"".$script."\" xmlns:tao='http://www.tao.lu/tao.rdfs#' xmlns:rdfs='http://www.w3.org/TR/1999/PR-rdf-schema-19990303#'><rdfs:LABEL lang=\"".$lang."\">".$label."</rdfs:LABEL>
	<rdfs:COMMENT lang=\"".$lang."\">".$comment."</rdfs:COMMENT>".$xul."	<tao:PASSWORD>".$ressource["tao:password"]."</tao:PASSWORD><tao:DURATION>".$ressource["tao:duration"]."</tao:DURATION>	<tao:HASSEQUENCEMODE ".$delay." >".$ressource["TAO:HASSEQUENCEMODE"]."</tao:HASSEQUENCEMODE><tao:PREVIEW>FALSE</tao:PREVIEW><tao:REVIEW>FALSE</tao:REVIEW><tao:HOLDABLE>TRUE</tao:HOLDABLE><tao:RESPONSEPATTERN>dichotomic</tao:RESPONSEPATTERN><tao:HASSCORINGMETHOD ".$scoringmethodsparams.">".$ressource["TAO:HASSCORINGMETHOD"]."</tao:HASSCORINGMETHOD><tao:TESTLISTENERS></tao:TESTLISTENERS><tao:CUMULMODEL>".$ressource["TAO:CUMULMODEL"]."</tao:CUMULMODEL>".$haltcriteriaoutput.$tresholds;
		$cancel=false;
		if (isset($ressource["TAO:CITEM"]))
		{
		$x=array();
		$undefined=array();
		//$i=1;
		foreach ($ressource["TAO:CITEM"] as $a=>$b)
			{	//$i=$i+1;
				if 
					(
					(isset($b["Sequence"])) and ($b["Sequence"]!="") and ($b["Sequence"]!="0")
					)
				{
				$index=$b["Sequence"];
				$b["key"]=$a;
				$x[$index]=$b;
				} 
				
				else 
				{
				$b["key"]=$a;
				$undefined[]=$b;
				}
				
				
			}
		foreach ($undefined as $a=>$b)
			{
			$index++;
			$b["Sequence"]="";
			$x[]=$b;
			}
		
		
			$nbelements = sizeof($x);
			$i=1;
			while ($i<=$nbelements){
				$b=$x[$i];
				$i=$i+1;
			
				$runtime = '';
				try{
				$itemResource =  new core_kernel_classes_Resource($b["key"]);
				$itemModel = $itemResource->getUniquePropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel'));
				if($itemModel instanceof core_kernel_classes_Resource){
					$runtime = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile'));
				}
				}
				catch(Exception $e){
					print $e;
					exit;
				}
				if ((isset($b["DIFFICULTY"])) and ($b["DIFFICULTY"]!=""))
				{
					$xml.='<tao:CITEM weight="'.$b["WEIGHT"].'" DIFFICULTY="'.$b["DIFFICULTY"].'" DISCRIMINATION="'.$b["DISCRIMINATION"].'" GUESSING="'.$b["GUESSING"].'" model="'.$b["model"].'" Sequence="'.$b["Sequence"].'" ';//>'.$b["key"].'</tao:CITEM>';
				}
				else
				{
					$xml.='<tao:CITEM weight="'.$b["WEIGHT"].'" Sequence="'.$b["Sequence"].'"';//'>'.$b["key"].'</tao:CITEM>';
				}
				if($runtime != ''){
					$xml.= " itemModel='$runtime' ";
				}
					$xml.=' >'.$b["key"].'</tao:CITEM>';
				}
		
			}	
			$xml.="</tao:TEST>";
			return $xml;
	}
	   
	
}
?>