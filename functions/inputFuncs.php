<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

## ############################################################## ##
## if no 'id' is specified in $attrs, 'id' will be same as 'name' ##
## ############################################################## ##
function inputfield($type, $name, $value="", $attrs=NULL, $data=NULL) {
	$s = "";
	if (isset($_SESSION['postVars'])) {
		$postVars = $_SESSION['postVars'];
	} else {
		$postVars = array();
	}
//	if (isset($postVars[$name])) {  // FIXME - is this right, or useful, messes up <select> - Fred
		//$value = $postVars[$name];
//		$data = $postVars[$name];
//	}
	if (isset($_SESSION['pageErrors'])) {
		$pageErrors = $_SESSION['pageErrors'];
	} else {
		$pageErrors = array();
	}
	if (!$attrs) {
		$attrs = array();
	}
	if (!isset($attrs['onChange'])) {
		$attrs['onChange'] = 'modified=true';
	}
	if (!isset($attrs['id'])) {
		$attrs['id'] = $name;
	}

	switch ($type) {
	// FIXME radio
	case 'select':
		$s .= '<select name="'.H($name).'" ';
		foreach ($attrs as $k => $v) {
			$s .= H($k).'="'.H($v).'" ';
		}
		$s .= ">\n";
		foreach ($data as $val => $desc) {
			$s .= '<option value="'.H($val).'" ';
			if ($value == $val) {
				$s .= ' selected="selected"';
			}
			$s .= ">".H($desc)."</option>\n";
		}
		$s .= "</select>\n";
		break;
	case 'textarea':
		$s .= '<textarea name="'.H($name).'" ';
		foreach ($attrs as $k => $v) {
			$s .= H($k).'="'.H($v).'" ';
		}
		//$s .= ">".H($value)."</textarea>";
		$s .= ">".H($data)."</textarea>";
		break;
	case 'checkbox':
		$s .= '<input type="checkbox" name="'.H($name).'" ';
		//$s .= 'value="'.H($data).'" ';
		$s .= 'value="'.H($value).'" ';
		if ($value == $data) {
			$s .= 'checked="checked" ';
		}
		foreach ($attrs as $k => $v) {
			$s .= H($k).'="'.H($v).'" ';
		}
		$s .= "/>";
		break;
	default:
		$s .= '<input type="'.H($type).'" name="'.H($name).'" ';
		if ($value != "") {
			$s .= 'value="'.H($value).'" ';
		}
		foreach ($attrs as $k => $v) {
			$s .= H($k).'="'.H($v).'" ';
		}
		$s .= "/>";
		break;
	}
	#### place error messages to right of effected field -- Fred
	if (isset($pageErrors[$name])) {
		$s .= '<span class="error">'.H($pageErrors[$name]).'</span><br />';
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
