<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */


/**
 * Draws input html tag of type text.
 * @param string $tag input field tag
 * @param int $occur input field occurance if field is being entered as repeatable
 * @return void
 * @access public
 */
function printUsmarcText($tag,$subfieldCd,&$marcTags,&$marcSubflds,$showTagDesc){
	$arrayIndex = sprintf("%03d",$tag).$subfieldCd;

	if (($showTagDesc)
		&& (isset($marcTags[$tag]))
		&& (isset($marcSubflds[$arrayIndex]))){
		echo $marcTags[$tag]->getDescription();
		echo " (".$marcSubflds[$arrayIndex]->getDescription().")";
	} elseif (isset($marcSubflds[$arrayIndex])){
		echo $marcSubflds[$arrayIndex]->getDescription();
	}
}

/**
 * accepts a NNN$A formatted MARC tag and returns its components as an object.
 * ex. '245$a' ==> '{"tag":"245","suf":"a"}'
 */
function makeTagObj($grp) {
//echo"makeTagGrp: grp==>";print_r($grp);echo"<br />\n";
	foreach ($grp as $tag) {
		$parts = explode('$',$tag);
		$rslt .= '{"tag":"'.$parts[0].'","suf":"'.$parts[1].'"},';
	}
	return substr($rslt,0,-1);  ## remove trailing comma
}



