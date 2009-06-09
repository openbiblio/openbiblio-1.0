<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "opac";
$nav = "register";

require_once(REL(__FILE__, "../model/Members.php"));
require_once(REL(__FILE__, "../model/Sites.php"));
require_once(REL(__FILE__, "../classes/Form.php"));


if (isset($_SESSION['authMbrid'])) {
	header('Location: ../opac/index.php');
	exit();
}

$members = new Members;
$sites = new Sites;

function setupMember_el($values) {
	global $members;
	$mbrs = $members->getMatches(array(
		'siteid'=>$values['site'],
		'first_name'=>$values['first_name'],
		'last_name'=>$values['last_name'],
	));
	if ($mbrs->count() != 1) {
		return array(NULL, array(new Error(T('registerNoMatch'))));
	}
	$mbr = $mbrs->next();
	if (strlen($mbr['password'])) {
		return array(NULL, array(new Error(T('registerGotLogin'))));
	}
	$_SESSION['authMbrid'] = $mbr['mbrid'];
	$errs = $members->update_el(array(
		'mbrid'=>$mbr['mbrid'],
		'email'=>$values['email'],
		'password'=>$values['password'],
		'confirm-pw'=>$values['confirm-pw'],
	));
	if ($errs) {
		return array(NULL, $errs);
	} else {
		return array($members->getOne($mbr['mbrid']), NULL);
	}
}

$form = array(
	'title' => T("Register"),
	'name' => 'register',
	'action' => '../opac/register.php',
	'cancel'=> '../opac/index.php',
	'fields' => array(
		array('name'=>'site', 'type'=>'select', 'title'=>T("Site:"),
			'options'=>$sites->getSelect()),
		array('name'=>'first_name', 'title'=>T("First Name:")),
		array('name'=>'last_name', 'title'=>T("Last Name:")),
		array('name'=>'email', 'title'=>T("Email (optional)"), 'default'=>''),
		array('name'=>'password', 'type'=>'password',
			'title'=>T("Password:"), 'attrs'=>array('size'=>10)),
		array('name'=>'confirm-pw', 'type'=>'password',
			'title'=>T("Password (Confirm):"), 'attrs'=>array('size'=>10)),
	),
);
list($values, $errs) = Form::getCgi_el($form['fields']);

if (!$values['_posted']) {
	$errs = array();
} else {
	list($mbr, $errs) = setupMember_el($values);
	if (!$errs) {
		Page::header_opac(array('nav'=>$nav, 'title'=>''));
		echo '<h1>'.T("Success").'</h1>';
		echo '<p>'.T('registerYouHaveRegd').'</p>';
		echo '<p>'.T('registerNextTime', array('barcode'=>H($mbr['barcode_nmbr']))).'</p>';
		echo '<p>'.T('registerEditInfo', array('link'=>'<a href="../opac/edit_account.php">', 'end'=>'</a>')).'</p>';
		echo '<p><a href="../opac/index.php">'.T("Start using the catalog").'</a></p>';
		Page::footer();
		exit();
	}
}

Page::header_opac(array('nav'=>$nav, 'title'=>''));

echo '<p>'.T('registerMustMatch').'</p>';

$form['values'] = $values;
$form['errors'] = $errs;
Form::display($form);

Page::footer();
