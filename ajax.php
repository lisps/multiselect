<?php
 /**
 * DokuWiki Plugin multiselect (Ajax Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
require_once(DOKU_INC.'inc/init.php');

#Variables  
$selectcount = intval($_POST["index"]);
$smileycount = intval($_POST["count"]);
$token       = trim($_POST["token"]);

$token= cleanText($token);

$Hajax = plugin_load('helper', 'ajaxedit');
$data=$Hajax->getWikiPage();


$range_delemiters = array();
//remove pagemod area - no changes here
$ranges  = preg_split('$<pagemod[\w\W]*?</pagemod>$',$data);
$count = preg_match_all('$<pagemod[\w\W]*?</pagemod>$',$data,$range_delemiters);

if($count) {
	$range_delemiters = $range_delemiters[0];
} else {
	$range_delemiters = array();
}

//will be set in loop to detect if change has already happened
$found_flag = false;

//will count the <multiselect - need for calculation
$found_counter = 0;

$temp = ''; //old selected icon
foreach($ranges as $range_index=>&$range_part){
	//find "our" multiselect
	$found=explode("<multiselect",$range_part);
	
	//selectcount for the specific range
	$selectcount_range = $selectcount-$found_counter;
	
	//overall found counter
	$found_counter += count($found)-1;

	if (!$found_flag && $selectcount < $found_counter) {
		$found_flag = true;
		//get smiley collection
		$stop=strpos($found[$selectcount_range+1],">");
		if ($stop !== FALSE) {
			$oldsmileys=substr($found[$selectcount_range+1],0,$stop);

			//move selected smiley to front
			$ret = preg_match_all('/[\w\[\]\(\)\{\}\|\?\+\-\*\^\$\\\.:!\/;,+#~&%]+|"[\w\[\]\(\)\{\}\|\?\+\-\*\^\$\\\.:!\/;,+#~&%\s]+"/u',trim($oldsmileys),$matches);
			$newsmileys=str_replace('"','',$matches[0]);
			//$newsmileys=explode(" ",trim($oldsmileys));

			if ($smileycount < count($newsmileys)) {
				$temp=$newsmileys[0];
				$newsmileys[0]=$newsmileys[$smileycount];
				$newsmileys[$smileycount]=$temp;
			}
			foreach($newsmileys as $key=>$sm){
				if(strpos($sm,' ') !== false){
					$newsmileys[$key] = '"'.$sm.'"';
				}
			}

			$newsmileys=implode(' ',$newsmileys);

			//create new pagesource        
			$found[$selectcount_range+1]=str_replace($oldsmileys," ".$newsmileys." ",$found[$selectcount_range+1]);
			$range_part=implode("<multiselect",$found) . (isset($range_delemiters[$range_index])?$range_delemiters[$range_index]:'');		
		}
	} else {
		$range_part .= isset($range_delemiters[$range_index])?$range_delemiters[$range_index]:'';
	}
}

$data = implode($ranges);
//Save data and create log
$summary = "Multiselect ".$selectcount." changed from \"".hsc($temp)."\" to \"".hsc($token)."\"";
$param['msg'] = sprintf($Hajax->getLang('changed_from_to'),'Multiselect',hsc($temp),hsc($token));
$Hajax->saveWikiPage($data,$summary,false,$param);
