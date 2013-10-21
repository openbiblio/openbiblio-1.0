<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Early work on a set of templating primatives
 * @author Micah Stetson
 *
 * some ties into initial localization efforts.
 */
 
function JsonFormatter($val, $context, $args) {
	return json_encode($val);
}
function mkSearchUrl($type, $value) {
	global $tab;
	return URL('../shared/biblio_search.php?'
		.'searchType={type}&searchText={value}'
		.'&tab={tab}&exact=1', array(
			'type'=>$type,
			'value'=>$value,
			'tab'=>$tab,
		));
}
function AuthorSearchUrlFormatter($val, $context, $args) {
	return mkSearchUrl('author', $val);
}
function PublisherSearchUrlFormatter($val, $context, $args) {
	return mkSearchUrl('publisher', $val);
}
function SeriesSearchUrlFormatter($val, $context, $args) {
	return mkSearchUrl('series', $val);
}
function SubjectSearchUrlFormatter($val, $context, $args) {
	return mkSearchUrl('subject', $val);
}
# FIXME - This should examine context to set up proper
# sequence traversal. Maybe that kind of thing should really
# be done in JS.
function BiblioLinkUrlFormatter($val, $context, $args) {
	global $tab;
	return URL('../shared/biblio_view.php?bibid={bibid}&tab={tab}',
		array('bibid'=>$val, 'tab'=>$tab));
}
function MemberLinkUrlFormatter($val, $context, $args) {
	return URL('../circ/mbr_view.php?mbrid={@}', $val);
}
 
$_templateFormatters = array(
	'json' => 'JsonFormatter',
	'author-search-url' => 'AuthorSearchUrlFormatter',
	'publisher-search-url' => 'PublisherSearchUrlFormatter',
	'series-search-url' => 'SeriesSearchUrlFormatter',
	'subject-search-url' => 'SubjectSearchUrlFormatter',
	'biblio-link-url' => 'BiblioLinkUrlFormatter',
	'member-link-url' => 'MemberLinkUrlFormatter',
);

// Generally not used directly, but everything else is built on it.
// This is so that additional formatters and predicates can be
// dealt with in one place.
function TEMPLATE($s, $v, $default_formatter='html', $meta='{}') {
	global $_templateFormatters;
	$t = new JsonTemplate($s, array(
		'meta'=>$meta,
		'more_formatters'=>$_templateFormatters,
		'default_formatter'=>$default_formatter,
	));
	return $t->expand($v);
}
function HTML($s, $v=NULL) {
	global $LOC;
	return TEMPLATE($LOC->translate($s), $v);
}
# Because JSON uses braces and brackets, we use angle brackets
# for templates that produce JSON.
function JSON($s, $v=NULL) {
	return TEMPLATE($s, $v, 'json', '<>');
}
function URL($s, $v=NULL) {
	return TEMPLATE($s, $v, 'url-param-value');
}
function T($s, $v=NULL) {
	global $LOC;
	//return TEMPLATE($LOC->translateOne($s), $v);
	return $LOC->getText($s);
}

