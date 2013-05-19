<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/*********************************************************************************
 * cleans up a potential json string to meet JSON spec
 * then call the decoder.
 *********************************************************************************
 */
// http://www.php.net/manual/en/function.json-decode.php#95782
function json_decode_nice($json, $assoc = FALSE){
  $json = str_replace(array("\n","\r"),"",$json);
  $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
  $json = preg_replace('/(,)\s*}$/','}',$json);
  return json_decode($json,$assoc);
}

/*********************************************************************************
 * gets groups of MARC tags from the tagGroup file and presents them to PHP
 * as an associative array of display types.
 * Output may be decoded or left in original JSOn form.
 *********************************************************************************
 */
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

/*********************************************************************************
 * get a single group of MARC tags from the tagGroup file
 * as specified by the user.
 *********************************************************************************
 */
function getSrchTags($which) {
	$grps = getTagGroups(true);
	return $grps[$which];
}


?>
