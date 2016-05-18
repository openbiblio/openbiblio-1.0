<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/Validations.php"));

/**
 * creates HTML <input ....> statements for most types
 * if type is unrecognized, a generic type="text" will be provided
 * if no 'id' is specified in $attrs, 'id' will be same as 'name' ##
 * @author Micah Stetson
 * @author Fred LaPlante
 */
$patterns = array();
function inputfield($type, $name, $value="", $attrs=NULL, $data=NULL) {
	global $patterns;
	// establish input validation patterns for later use
	if (empty($patterns)) {
	    $db = new Validations($dbConst);
	    $valids = array();
		$set = $db->getAll('description');
//print_r($set);echo "<br />\n";
		while ($row = $set->fetch_assoc()) {
		    $patterns[$row['code']] = $row['pattern'];
		}
	}

	$s = "";
	if (isset($_SESSION['postVars'])) {
		$postVars = $_SESSION['postVars'];
	} else {
		$postVars = array();
	}
	if (isset($_SESSION['pageErrors'])) {
		$pageErrors = $_SESSION['pageErrors'];
	} else {
		$pageErrors = array();
	}
	if (!$attrs) {
		$attrs = array();
	}
	if (!isset($attrs['id'])) {
		$attrs['id'] = $name;
	}

	switch ($type) {
	// FIXME radio
	case 'select':
		$s .= '<select name="'.H($name).'" ';
		if ($attrs) {
			foreach ($attrs as $k => $v) {
				$s .= H($k).'="'.H($v).'" ';
			}
		}
		$s .= ">\n";
		if ($data) {
			foreach ($data as $val => $desc) {
				$s .= '<option value="'.H($val).'" ';
				if ($value == $val) {
					$s .= ' selected="selected"';
				}
				$s .= ">".H($desc)."</option>\n";
			}
		}
		$s .= "</select>\n";
		break;
	case 'textarea':
		$s .= '<textarea name="'.H($name).'" ';
		foreach ($attrs as $k => $v) {
			$s .= H($k).'="'.H($v).'" ';
		}
		$s .= ">".H($data)."</textarea>";
		break;
	case 'checkbox':
		$s .= '<input type="checkbox" name="'.H($name).'" ';
		$s .= 'value="'.H($value).'" ';
		if ($value == $data) {
			$s .= 'checked="checked" ';
		}
		foreach ($attrs as $k => $v) {
			$s .= H($k).'="'.H($v).'" ';
		}
		$s .= "/>";
		break;
	case 'number': $attrs['pattern'] = '\d*'; inputHandler($type, $name, $attrs); break;
	case 'date': $attrs['pattern'] = $patterns['date']; $s .= inputHandler($type, $name, $value, $attrs); break;
	case 'year': $attrs['pattern'] = $patterns['year']; $s .= inputHandler($type, $name, $value, $attrs); break;
	case 'tel': $attrs['pattern'] = $patterns['tel']; $s .= inputHandler($type, $name, $value, $attrs); break;
	case 'zip': $attrs['pattern'] = $patterns['zip']; $s .= inputHandler($type, $name, $value, $attrs); break;
	case 'url': $attrs['pattern'] = $patterns['url']; $s .= inputHandler($type, $name, $value, $attrs); break;
	case 'email': $attrs['pattern'] = $patterns['email']; $s .= inputHandler($type, $name, $value, $attrs); break;
	default: $s .= inputHandler($type, $name, $value, $attrs); break;
	}
	#### place error messages to right of effected field -- Fred
	if (isset($pageErrors[$name])) {
		$s .= '<span class="error">'.H($pageErrors[$name]).'</span><br />';
	}
	return $s;
}
function inputHandler($type, $name, $value, $attrs) {
	$s .= '<input type="'.H($type).'" name="'.H($name).'" ';
	if ($value != "") {
		$s .= 'value="'.H($value).'" ';
	}
	foreach ($attrs as $k => $v) {
		if ($k == 'required') {
			//$s .= 'required="required" aria-required="true" ';
			$s .= 'required aria-required="true" ';
		} else {
			$s .= H($k).'="'.H($v).'" ';
		}
	}
	$s .= "/>";

	if (in_array('required', $attrs)) {
		$s .= '<span class="reqd">*</span>';
	}
	return $s;
}


/*********************************************************************************
 * DEPRECATED, use inputfield.  Draws input html tag of type text.
 * @param string $fieldName name of input field
 * @param string $size size of text box
 * @param string $max max input length of text box
 * @param array_reference &$postVars reference to array containing all input values
 * @param array_reference &$pageErrors reference to array containing all input errors
 * @return void
 * @access public
 *********************************************************************************
 */
function printInputText($fieldName,$size,$max,&$postVars,&$pageErrors,$visibility = "visible"){
	$_SESSION['postVars'] = $postVars;
	$_SESSION['pageErrors'] = $pageErrors;
	$attrs = array('size'=>$size,
		'maxlength'=>$max,
		'style'=>"visibility: $visibility");
	echo inputfield('text', $fieldName, '', $attrs);
}
