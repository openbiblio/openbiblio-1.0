<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "opac";
$nav = "account/edit";

require_once(REL(__FILE__, "../model/Members.php"));
require_once(REL(__FILE__, "../model/Sites.php"));
require_once(REL(__FILE__, "../classes/Form.php"));


if (!isset($_SESSION['authMbrid'])) {
	header('Location: ../opac/index.php');
	exit();
}

$members = new Members;
$mbr = $members->getOne($_SESSION['authMbrid']);
$sites = new Sites;
$site = $sites->getOne($mbr['siteid']);

$form = array(
	'title' => T("Edit Member Info"),
	'name' => 'edit_account',
	'action' => '../opac/edit_account.php',
	'cancel'=> '../opac/my_account.php',
	'fields' => array(
		array('name'=>'name', 'type'=>'fixed', 'title'=>T("Name:"),
			'default'=>$mbr['last_name'].', '.$mbr['first_name']),
		array('name'=>'site', 'type'=>'fixed', 'title'=>T("Site:"),
			'default'=>$site['name']),
		array('name'=>'barcode_nmbr', 'type=>fixed', 'title'=>T("Card Number:"),
			'default'=>$mbr['barcode_nmbr']),
		array('name'=>'email', 'title'=>T("Email Address:"),
			'default'=>$mbr['email']),
		array('name'=>'password', 'type'=>'password', 'title'=>T("Password:"),
			'attrs'=>array('size'=>10, 'onchange'=>'document.forms["edit_account"].elements["confirm-pw"].value=""'),
			default=>$mbr['password']),
		array('name'=>'confirm-pw', 'type'=>'password', 'title'=>T("Password (Confirm):"), 'attrs'=>array('size'=>10),
			'default'=>$mbr['password']),
	),
);
list($values, $errs) = Form::getCgi_el($form['fields']);

if (!$values['_posted']) {
	$errs = array();
} else {
	$values['mbrid'] = $_SESSION['authMbrid'];
	$errs = $members->update_el($values);
	if (!$errs) {
		header('Location: ../opac/my_account.php');
		exit();
	}
}

Page::header_opac(array('nav'=>$nav, 'title'=>''));

$form['values'] = $values;
$form['errors'] = $errs;
Form::display($form);

Page::footer();
