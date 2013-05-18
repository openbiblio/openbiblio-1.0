<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */


/*********************************************************************************
 * Draws input html tag of type text.
 * @param string $tag input field tag
 * @param int $occur input field occurance if field is being entered as repeatable
 * @return void
 * @access public
 *********************************************************************************
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

// http://www.php.net/manual/en/function.json-decode.php#95782
function json_decode_nice($json, $assoc = FALSE){
  $json = str_replace(array("\n","\r"),"",$json);
  $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
  $json = preg_replace('/(,)\s*}$/','}',$json);
  return json_decode($json,$assoc);
}
function getTagGroups($decode=true) {
	$json = file_get_contents('../shared/tagGroup.json');
	//echo"json===>";var_dump($json);echo"<br />";
	if ($decode) {
		//echo "decoding!!<br />";
		$data = json_decode_nice($json,true);
		if ($data === null) {
			echo "json_decode failed";
	    switch (json_last_error()) {
	      case JSON_ERROR_DEPTH:
	        echo ' - Maximum stack depth exceeded';
	      	break;
	      case JSON_ERROR_STATE_MISMATCH:
	        echo ' - Underflow or the modes mismatch';
	      	break;
	      case JSON_ERROR_CTRL_CHAR:
	        echo ' - Unexpected control character found';
	      	break;
	      case JSON_ERROR_SYNTAX:
	        echo ' - Syntax error, malformed JSON';
	      	break;
	      case JSON_ERROR_UTF8:
	        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
	      	break;
	      default:
	        echo ' - Unidentified error';
	      	break;
	    }
			echo ".<br />";
		}
	} else {
		echo "raw<br />";
		$data = $json;
	}
	//echo"data===>";var_dump($data);echo"<br />";
	return $data;
}
function getSrchTags($which) {
	$grps = getTagGroups(true);
	return $grps[$which];
}
function makeTagObj($grp) {
	foreach ($grp as $tag) {
		$parts = explode('$',$tag);
		$rslt .= '{"tag":"'.$parts[0].'","suf":"'.$parts[1].'"},';
	}
	return substr($rslt,0,-1);
}


