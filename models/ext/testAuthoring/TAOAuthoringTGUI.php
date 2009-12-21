<?php
/**
* Generate a form to edit a test
* @package Widgets.etesting.authoringTest
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
require_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/generis_ConstantsOfGui.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/generis_utils.php");	
include_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/lg/".strtoupper($GLOBALS['lang']).".php");	   

class TAOAuthoringTGUI {
	
	protected $instance;
	protected $localXmlFile;
	
	function TAOAuthoringTGUI($localXmlFile, $instance){
		$this->localXmlFile = $localXmlFile;
		$this->instance = $instance;
		
		$session = core_kernel_classes_Session::singleton();
		$session->model->loadModel('http://www.tao.lu/Ontologies/TAOItem.rdf');
	}
	
	/**
	 * load xml with an http request
	 * @return thee xml data
	 */
	private function loadXml(){
		if(!empty($this->localXmlFile)){
			session_write_close();
			$curlHandler = curl_init();
			$url = $this->localXmlFile;
			if(!preg_match("/&$/", $url)){
				$url .= '&';
			}
			$url .= 'session_id=' . session_id();
			curl_setopt($curlHandler, CURLOPT_URL, $url);
			curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curlHandler, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/'); 
			$output = curl_exec($curlHandler);
			curl_close($curlHandler);  
		}
		return $output;
	} 

	private function loadItems(){
		$myInstance = new core_kernel_classes_Resource($this->instance);
		return $myInstance->getPropertyValues(new core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems"));
	}

	function parseXML($xml)
	{
	$xml = str_replace("&nbsp;"," ",$xml);
	$xml = str_replace("&quot;","'",$xml);
	$xml=str_replace("&#180;","'",$xml);
	$xml=trim($xml);

	$xml_parser=xml_parser_create("UTF-8");
	
	$tests=array();
	$struct=array();	
	
	xml_parse_into_struct($xml_parser, $xml, $values, $tags);
	
	$struct["deactivateback"]="CHECKED";
	 foreach ($tags as $key=>$val)
		 {
			if ($key == "BUTTON")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if (!((strpos($values[$theIndex]["attributes"]["ID"],"prevItem_button"))===false))
						 {
								$struct["navleft"]=$values[$theIndex]["attributes"]["LEFT"];
								$struct["navtop"]=$values[$theIndex]["attributes"]["TOP"];
								$struct["urlleft"]=$values[$theIndex]["attributes"]["URL"];
								$struct["deactivateback"]="";
						 }

						 if (!((strpos($values[$theIndex]["attributes"]["ID"],"nextItem_button"))===false))
						 {
								
								$struct["urlright"]=$values[$theIndex]["attributes"]["URL"];
								$struct["navleft"]=$values[$theIndex]["attributes"]["LEFT"];
								$struct["navtop"]=$values[$theIndex]["attributes"]["TOP"];
								
						 }
					 }
				 }

			if ($key == "TAO:PASSWORD")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:PASSWORD"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:DURATION")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:DURATION"] = $values[$theIndex]["value"];
					 }
				 }
			if ($key == "TAO:HALTCRITERIA")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if ($values[$theIndex]["type"]=="open")
						 {$struct["TAO:HALTCRITERIA"] = $values[$theIndex]["attributes"]["VALUE"];
						$struct["MAX"] = $values[$theIndex]["attributes"]["MAX"];
						}
										 
					 }
				 }
			if ($key == "TAO:PARAM")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:PARAM"] = $values[$theIndex]["attributes"]["VALUE"];;
					 }
				 }
				if ($key == "CLL:THRESHOLD")
				 { $i=1;
					foreach ($val as $x=>$theIndex)
					 {	
						$name = "tresh".$i;
						$struct[$name] = $values[$theIndex]["value"];;
						$i++;
					 }
					 
				 }
			if ($key == "PROGRESSMETER")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						
						 $struct["progressbarleft"] = $values[$theIndex]["attributes"]["LEFT"];
						 $struct["progressbartop"] = $values[$theIndex]["attributes"]["TOP"];
										 
					 }
				 }
			if ($key == "TAO:HASSEQUENCEMODE")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:HASSEQUENCEMODE"] = $values[$theIndex]["value"];
						$struct["DELAY"] = $values[$theIndex]["attributes"]["DELAY"];
					 }
				 }

			if ($key == "TAO:LABELCOORDS")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:LABELCOORDS"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:COMMENTCOORDS")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:COMMENTCOORDS"] = $values[$theIndex]["value"];
					 }
				 }
			if ($key == "TAO:ITEMCOORDS")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:ITEMCOORDS"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:LISTPREVIEWREVIEW")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:LISTPREVIEWREVIEW"] = $values[$theIndex]["value"];
					 }
				 }

			if ($key == "TAO:PROGRESSBARCOORDS")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:PROGRESSBARCOORDS"] = $values[$theIndex]["value"];
					 }
				 }
			if ($key == "TAO:PREVIEW")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:PREVIEW"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:REVIEW")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:REVIEW"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:HOLDABLE")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:HOLDABLE"] = $values[$theIndex]["value"];
					 }
				 }
			if ($key == "TAO:RESPONSEPATTERN")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:RESPONSEPATTERN"] = $values[$theIndex]["value"];
					 }
				 }
			if ($key == "TAO:HASSCORINGMETHOD")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:HASSCORINGMETHOD"] = $values[$theIndex]["value"];
						$struct["qmin"] = $values[$theIndex]["attributes"]["QMIN"];
						$struct["qmax"] = $values[$theIndex]["attributes"]["QMAX"];
						$struct["qiter"] = $values[$theIndex]["attributes"]["QITER"];
					 }
				 }
			
			if ($key == "TAO:TESTLISTENERS")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if (isset($values[$theIndex]["value"]))
						 {
						$struct["TAO:TESTLISTENERS"] = $values[$theIndex]["value"];
						 }
					 }
				 }
			if ($key == "TAO:CUMULMODEL")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:CUMULMODEL"] = $values[$theIndex]["value"];
					 }
				}
			
			if ($key == "TAO:CITEM")
				 { 
					
				foreach ($val as $x=>$theIndex)
					 {	
						
						$weight=$values[$theIndex]["attributes"]["WEIGHT"];
						$difficulty = $values[$theIndex]["attributes"]["DIFFICULTY"];
						$discrimination = $values[$theIndex]["attributes"]["DISCRIMINATION"];
						$guessing = $values[$theIndex]["attributes"]["GUESSING"];
						$model = $values[$theIndex]["attributes"]["MODEL"];
						$sequence = $values[$theIndex]["attributes"]["SEQUENCE"];
						
						$id = $values[$theIndex]["value"];
						$struct["inquiries"][]=array("WEIGHT" => $weight, "DIFFICULTY" => $difficulty,"DISCRIMINATION" => $discrimination,"GUESSING" => $guessing,"model" => $model,"Sequence"=> $sequence,"value" => $id);
						
					 }
				 }
			if ($key == "PROGRESSMETER")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if ($values[$theIndex]["attributes"]["ID"]=="test_progressmeter")
						 {
						$struct["TAO:SHOWPROGRESSBAR"] = "CHECKED";
						 }
					 }
				 }
			if ($key == "LISTBOX")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if ($values[$theIndex]["attributes"]["ID"]=="testItems_listbox")
						 {
						$struct["TAO:SHOWLISTBOX"] = "CHECKED";
						 }
					 }
				 }
			if ($key == "LABEL")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						
						if ($values[$theIndex]["attributes"]["ID"]=="testLabel_label")
						 {
						 $struct["showLabel"] = "CHECKED";
						 
						 
						 }

						 if ($values[$theIndex]["attributes"]["ID"]=="testComment_label")
						 {
						 $struct["showComment"] = "CHECKED";
						 
						 
						 }
					 }
				 }
		 }
		xml_parser_free($xml_parser);
		return $struct;
	}

	function getOutput()
	{
	
		$output='';
		
		$instance = $this->instance;
		$property = 'http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent';
		$xml = $this->loadXml();
		$items = $this->loadItems();
		
		error_reporting(E_ALL ^ E_NOTICE);//prevent enotice if xml node was not found
		$struct = $this->parseXML($xml);
		
		$output.='<html><head>';
		$output.='<script type="text/javascript">var _editor_url="/generis/core/view/HTMLArea-3.0-rc1/";</script>';
		$output.='<script type="text/javascript" src="/generis/core/view/HTMLArea-3.0-rc1/htmlarea.js"></script>';
		$output.='<script type="text/javascript" src="/tao/views/js/jquery-1.3.2.min.js" ></script> ';
		$output.='<link rel="stylesheet" type="text/css" href="/generis/core/view/HTMLArea-3.0-rc1/htmlarea.css" />';
		$output.='<link rel="stylesheet" type="text/css" href="/generis/core/view/CSS/generis_default.css" />';
		$output.='<style type="text/css">
					input[type=button],input[type=submit]{cursor:pointer; padding:4px; font-weight:bold;} 
					table.generisTable tr td,table.generisTable tr th, div.Title{color:#000;} </style>';
		$output.='<script type="text/javascript">';
		$output.="function preview(uri){";
		$output.="var data = $('#testForm').serialize();";
		$output.="data.preview = true;";
		$output.="$.ajax({
			url: 'preview.php',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(response){
				if(response.saved){
					window.open('/taoTests/Tests/preview?uri='+uri, 'tao', 'width=800,height=600,menubar=no,toolbar=no');
				}
			}
		});";
		$output.="}";
		$output.='</script>';
		$output.='</head><body>';
		$output.='<FORM id="testForm" enctype="multipart/form-data" action=./index.php name=newressource method=post>';
		$output.='<input type=hidden name=MAX_FILE_SIZE value=2000000>';
		$output.=TABLEHEADER;
		$output.='<input type="hidden" name="instance" value="'.$instance.'">';
		$output.='<input type=hidden name=AuthoringT['.$instance.']['.$property.']>';

		$output.="<tr><td valign=top><table valign=top border=0 cellpadding=1 cellspacing=1>";
		$output.='<tr><td colspan=4><Hr></td></tr>';
		$output.='<tr><td colspan=3><div class="Title">'.AUTHORINGTEST.' Test</div></td></tr>';
		$output.='<tr><td colspan=4><Hr></td></tr>';
		$output.='<tr height="25"><td><div class="AUTHINFOS"><b>'.TESTPARAMS.'</b></div></td></tr>
		<tr><td><div class="AUTHINFOS">'.DURATION.' : </div></td><td><input type=text name=testcontent[tao:duration] value='.$struct["TAO:DURATION"].'></tr>
		<tr><td><div class="AUTHINFOS">'.PASSWORD.' :</td><td><input type=text name=testcontent[tao:password] value='.$struct["TAO:PASSWORD"].'></div></tr>
		<tr><td><div class="AUTHINFOS">'.SHOWLISTBOX.' </td><td><input type=CHECKBOX name=testcontent[tao:showlistbox] '.$struct["TAO:SHOWLISTBOX"].'></div></tr>
		<tr><td><div class="AUTHINFOS">'.SHOWPROGRESSBAR.' </td><td><input type=CHECKBOX name=testcontent[tao:showprogressbar] '.$struct["TAO:SHOWPROGRESSBAR"].'></div></tr>
		<tr><td><div class="AUTHINFOS">'.SHOWLABEL.'</td><td><input '.$struct["showLabel"].' type=checkbox name=testcontent[tao:showLabel]></div></tr>
		<tr><td><div class="AUTHINFOS">'.SHOWCOMMENT.'</td><td><input '.$struct["showComment"].' type=checkbox name=testcontent[tao:showComment]></div></tr>
			';
		//set default value
		if ($struct["TAO:HASSEQUENCEMODE"]=="") {$struct["TAO:HASSEQUENCEMODE"]="SEQUENTIAL";}
		if ($struct["TAO:HASSCORINGMETHOD"]=="") {$struct["TAO:HASSCORINGMETHOD"]="CLASSICAL RATIO";}
		if ($struct["TAO:CUMULMODEL"]=="") {$struct["TAO:CUMULMODEL"]="CLASSICAL";}
		$output.='<tr><td><div class="AUTHINFOS">'.SEQMODE.'</div></td><td colspan=2>
			<SELECT NAME="testcontent[TAO:HASSEQUENCEMODE]">
				<option value='.$struct["TAO:HASSEQUENCEMODE"].' SELECTED>'.$struct["TAO:HASSEQUENCEMODE"].'
				<option value=SEQUENTIAL>SEQUENTIAL
				<option value=RANDOM>RANDOM
				<option value=MAXFISHER>MAXFISHER
				</select>
				</td><td>Delay :<input size=2 type=text name=testcontent[delay] value='.$struct["DELAY"].'></td>';
			$output.='</tr>';
		
		$output.='<tr><td><div class="AUTHINFOS">'.SCORING.'</div></td><td colspan=2>
			<SELECT NAME="testcontent[TAO:HASSCORINGMETHOD]">
				<option value='.str_replace(" ","",$struct["TAO:HASSCORINGMETHOD"]).' SELECTED>'.$struct["TAO:HASSCORINGMETHOD"].'
				
				<option value=CLASSICALRATIO>CLASSICALRATIO
				<option value=MAXIMUMLIKELIHOOD>MAXIMUMLIKELIHOOD
				<option value=MAXIMUMAPOSTERIORI>MAXIMUMAPOSTERIORI
				<option value=EXPECTEDAPOSTERIORI>EXPECTEDAPOSTERIORI
				</select>
				</td>';
		$output.='</tr>';
			
		$output.='<tr><td><div class="AUTHINFOS">'.CUMUL.'</div></td><td colspan=2>
			<SELECT NAME="testcontent[TAO:CUMULMODEL]">
				<option value='.$struct["TAO:CUMULMODEL"].' SELECTED>'.$struct["TAO:CUMULMODEL"].'
			
				<option value=CLASSICAL>CLASSICAL
				<option value=LIKELIHOOD>LIKELIHOOD
				<option value=LOG-LIKELIHOOD>LOG-LIKELIHOOD
				
				</select>
				</td></tr>';
		
		$output.='<tr><td><div class="AUTHINFOS">'.HALTCRITERIA.'</div></td><td colspan=2>
			<SELECT NAME="testcontent[TAO:HALTCRITERIA]">
				<option value='.$struct["TAO:HALTCRITERIA"].' SELECTED>'.$struct["TAO:HALTCRITERIA"].'
				<option value="DELTASCORE" >DELTASCORE
				<option value="DELTASE" >DELTASE
					<option value="" >
				</select>
				</td><td>Treshold :<input size=2 type=text name=testcontent[deltascorethreshold] value='.$struct["TAO:PARAM"] .'></td><td>Max :<input size=2 type=text name=testcontent[max] value='.$struct["MAX"].'></td>';
		$output.='</tr>';
			
			
		$output.= '
				<tr><td><div class="AUTHINFOS">'.DEACTIVATEBACK.'</div></td><td><input type=checkbox '.$struct["deactivateback"].' name=testcontent[deactivateback] /></div></td></tr>

				<tr><td><div class="AUTHINFOS">'.NAVTOP.'</div></td><td><input size=2 type=text name=testcontent[navtop] value='.$struct["navtop"] .'></div></td></tr>
				<tr><td><div class="AUTHINFOS">'.NAVLEFT.'</div></td><td><input size=2 type=text name=testcontent[navleft] value='.$struct["navleft"] .'></div></td></tr>
				<tr><td><div class="AUTHINFOS">'.PROGRESSBARTOP.'</div></td><td><input size=2 type=text name=testcontent[progressbartop] value='.$struct["progressbartop"] .'></div></td></tr>
				<tr><td><div class="AUTHINFOS">'.PROGRESSBARLEFT.'</div></td><td><input size=2 type=text name=testcontent[progressbarleft] value='.$struct["progressbarleft"] .'></div></td></tr>
				<tr><td><div class="AUTHINFOS">'.URLLEFT.'</td><td><input size=50 type=text name=testcontent[urlleft] value='.$struct["urlleft"] .'></div></td></tr>
				<tr><td><div class="AUTHINFOS">'.URLRIGHT.'</td><td><input size=50 type=text name=testcontent[urlright] value='.$struct["urlright"] .'></div></td></tr>	
				
				';
		$output.= '<tr><td><div class="AUTHINFOS">'.QMIN.'</td><td><input size=2 type=text name=testcontent[qmin] value='.$struct["qmin"] .'></div></td></tr>';
		$output.= '<tr><td><div class="AUTHINFOS">'.QMAX.'</td><td><input size=2 type=text name=testcontent[qmax] value='.$struct["qmax"] .'></div></td></tr>';
		$output.= '<tr><td><div class="AUTHINFOS">'.QITER.'</td><td><input size=2 type=text name=testcontent[qiter] value='.$struct["qiter"] .'></div></td></tr>';
		$output.= '<tr><td><div class="AUTHINFOS">Treshold 1</td><td><input size=10 type=text name=testcontent[tresh1] value='.$struct["tresh1"] .'></div></td></tr>';
		$output.= '<tr><td><div class="AUTHINFOS">Treshold 2</td><td><input size=10 type=text name=testcontent[tresh2] value='.$struct["tresh2"] .'></div></td></tr>';
		$output.= '<tr><td><div class="AUTHINFOS">Treshold 3</td><td><input size=10 type=text name=testcontent[tresh3] value='.$struct["tresh3"] .'></div></td></tr>';
		$output.='<tr><td colspan=4><Hr></td></tr>';
		$output.='<tr><td colspan=3><div class="Title">'.ITEMS.'&nbsp;&nbsp;&nbsp;<input type=submit name=SHOWCLEAR value='.SHOWUNCLEAR.'></div></td></tr>';
		$output.='<tr><td colspan=4><Hr></td></tr>';
		$output.=TABLEFOOTER;
		$output.=TABLEHEADER;
		$output.='<tr><td><div class="AUTHINFOS"></div></td><td><div class="AUTHINFOS">'.WEIGHT.'</div></td><td><div class="AUTHINFOS">'.DIFFICULTY.'</div></td><td><div class="AUTHINFOS">'.DISCRIMINATION.'</div></td><td><div class="AUTHINFOS">'.GUESSING.'</div></td><td><div class="AUTHINFOS">'.MODEL.'</div></td><td><div class="AUTHINFOS">'.SEQUENCE.'</div></tr>';

		if (isset($struct["inquiries"])) {
			foreach ($struct["inquiries"] as $key=>$val){
				if  (is_int(array_search ($val["value"],$items))) {
					$output.='<tr>';
					$item = new core_kernel_classes_Resource($val["value"]);
					$output.='<td>'.$item->getLabel().'</td>';
					$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val["value"].'][WEIGHT] value='.$val["WEIGHT"].'></td>';
					$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val["value"].'][DIFFICULTY] value='.$val["DIFFICULTY"].'></td>';
					$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val["value"].'][DISCRIMINATION] value='.$val["DISCRIMINATION"].'></td>	';
					$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val["value"].'][GUESSING] value='.$val["GUESSING"].'></td>';
					$output.='<td>
							<SELECT NAME="testcontent[TAO:CITEM]['.$val["value"].'][model]">
								<option value='.$val["model"].' SELECTED>'.$val["model"].'
								<option value=BIRNBAUMODEL>BIRNBAUMODEL
								<option value=RASCHMODEL>RASCHMODEL
								<option value=GUESSINGMODEL>GUESSINGMODEL
								<option value=discrete>discrete
								</select>
								</td>
					<td><input type=text size=3 MAXLENGTH=3 name=testcontent[TAO:CITEM]['.$val["value"].'][Sequence] value='.$val["Sequence"].'></td>
					</tr>';
					$key = array_search ($val["value"],$items);	
					unset ($items[$key]);
				}
			}
		}
		if (isset($items)){
			foreach ($items as $key=>$val){
				$item = new core_kernel_classes_Resource($val);
				$output.='<tr>';
				$output.='<td>'.$item->getLabel().'</td>';
				$output.='<td><input type=text  size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val.'][WEIGHT]></td>';
				$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val.'][DIFFICULTY]></td>';
				$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val.'][DISCRIMINATION]></td>	';
				$output.='<td><input type=text size=3 MAXLENGTH=5 name=testcontent[TAO:CITEM]['.$val.'][GUESSING]></td>';
				$output.='<td>
						<SELECT NAME="testcontent[TAO:CITEM]['.$val.'][model]">
							<option>
							<option value=BIRNBAUMODEL>BIRNBAUMODEL
							<option value=RASCHMODEL>RASCHMODEL
							<option value=GUESSINGMODEL>GUESSINGMODEL
							
							</select>
							</td>
				<td><input type=text  size=3 MAXLENGTH=3 name=testcontent[TAO:CITEM]['.$val.'][Sequence]></td>
				</tr>';
			}
		}
		$output.="<input type=hidden name=testcontent[instance] value=$instance>";
		$output.="<input type=hidden name=testcontent[property] value=$property>";
		$output.="<tr><td colspan=4>
		<input type='submit' name='saveTContent' value='".APPLY."'>
		<input type='button' onclick='preview(\"".urlencode($instance)."\")' value='".PREVIEW."' />
		</td></tr>";
		$output.='</table></td></tr>';
		$output.=TABLEFOOTER;
		$output.='</form>';
		$output.='</body><html>';

		return $output;
	}
}
?>