<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

// Generally not used directly, but everything else is built on it.
// This is so that additional formatters and predicates can be
// dealt with in one place.
function TEMPLATE($s, $v, $default_formatter='html') {
	$t = new JsonTemplate($s, array('default_formatter'=>$default_formatter));
	return $t->expand($v);
}
function HTML($s, $v=NULL) {
	global $LOC;
	return TEMPLATE($LOC->translate($s), $v);
}
function URL($s, $v=NULL) {
	return TEMPLATE($s, $v, 'url-param-value');
}
function T($s, $v=NULL) {
	global $LOC;
	return TEMPLATE($LOC->translateOne($s), $v);
}

