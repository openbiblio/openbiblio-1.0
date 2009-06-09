<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

session_cache_limiter(null);

$nav = "request";
$tab = "opac";
require_once(REL(__FILE__, "../model/Biblios.php"));

if (count($_REQUEST) == 0) {
	header("Location: ../shared/request.php");
	exit();
}
$pageErrors = array();
if (!$_REQUEST['name']) {
	$pageErrors['name'] = T("Please fill in your name.");
}
if (!$_REQUEST['school']) {
	$pageErrors['school'] = T("Please fill in your school.");
}
if (!$_REQUEST['grade']) {
	$pageErrors['grade'] = T("Please fill in your grade.");
}

$from = Settings::get('request_from');
$to = Settings::get('request_to');
$subject = Settings::get('request_subject');
$msg = T("Name:")." ".$_REQUEST['name']."\r\n";
$msg .= T("School:")." ".$_REQUEST['school']."\r\n";
$msg .= T("Grade:").' '.$_REQUEST['grade']."\r\n";
$msg .= T("Patron #:")." ".$_REQUEST['number']."\r\n\r\n";
if ($_REQUEST['call'] == 'Y') {
	if (!$_REQUEST['phone']) {
		$pageErrors['phone'] = T("Please enter your phone number.");
	}
	$msg .= T('requestSendPleaseCall')."\r\n";
	$msg .= T("Phone:")." ".$_REQUEST['phone']."\r\n\r\n";
}
if ($_REQUEST['confirm'] == 'Y') {
	if (!$_REQUEST['email']) {
		$pageErrors['email'] = T("Please enter your e-mail address.");
	}
	$msg .= T('requestSendPleaseSend')."\r\n";
	$msg .= T("Email Address:").$_REQUEST['email']."\r\n\r\n";
}
if ($_REQUEST['alternate'] == 'Y') {
	$msg .= T('requestSendPleaseSelect')."\r\n\r\n";
}
if ($_REQUEST['notes']) {
	$msg .= T("Other notes:")."\r\n\r\n";
	str_replace("\r\n", "\n", $_REQUEST['notes']);
	str_replace("\r", "\n", $_REQUEST['notes']);
	str_replace("\n", "\r\n", $_REQUEST['notes']);
	$msg .= $_REQUEST['notes']."\r\n\r\n";
}

$biblios = new Biblios();
foreach ($_REQUEST['keys'] as $bibid) {
	if (!$_REQUEST['date'][$bibid]
			&& !($_REQUEST['soonest'][$bibid] == 'Y')) {
		$pageErrors["date[$bibid]"] = T('requestSendMustEnterDate');
		continue;
	}
	$biblio = $biblios->getOne($bibid);
	$a = $biblio['marc']->getValues('099$a');
	if (isset($a[0])) {
		$msg .= $a[0]." ";
	}
	$title = "";
	$a = $biblio['marc']->getValues('245$a');
	$b = $biblio['marc']->getValues('245$b');
	if (isset($a[0])) {
		$msg .= $a[0];
	}
	if (isset($b[0])) {
		$msg .= " ".$b[0];
	}
	$msg .= "\r\n\t";
	if ($_REQUEST['date'][$bibid]) {
		$msg .= $_REQUEST['date'][$bibid].' ';
	}
	if ($_REQUEST['soonest'][$bibid] == 'Y') {
		$msg .= T("(when available)");
	}
	$msg .= "\r\n";
}

if (count($pageErrors)) {
	$_SESSION['pageErrors'] = $pageErrors;
	$_SESSION['postVars'] = mkPostVars();
	header("Location: ../shared/request.php");
	exit();
}

$headers = 'From: '.$from;
if ($_REQUEST['email']) {
	$headers .= "\r\n".'Reply-To: '.$_REQUEST['email'];
}
$result = mail($to, $subject, $msg, $headers);

Page::header_opac(array('nav'=>$nav, 'title'=>''));
if ($result) {
	echo '<p>'.T("Request sent successfully.").'</p>';
} else {
	echo '<p>'.T("Request failed, call %library% %phone%", array('library'=>H(Settings::get('library_name'), 'phone'=>H(Settings::get('library_phone')).'</p>';
}
Page::footer();
