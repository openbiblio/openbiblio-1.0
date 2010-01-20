<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Calendars.php"));

if (count($_POST) == 0) {
	header("Location: ../admin/calendars_list.php");
	exit();
}

$calendars = new Calendars;

if (!isset($_POST['name']) or !$_POST['name']) {
	$_SESSION['postVars'] = $_POST;
	$_SESSION['pageErrors']['name'] = T("Every calendar must have a name.");
	header("Location: ../admin/calendar_edit_form.php");
	exit();
} elseif (!isset($_POST['calendar']) or !$_POST['calendar']) {
	$calendar = $calendars->insert(array('description'=>$_POST['name']));
} else {
	$calendar = $_POST['calendar'];
	$calendars->rename($calendar, $_POST['name']);
}

$days = array();
foreach ($_POST as $k => $v) {
	if (preg_match('/^input-[0-9]-([0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9])$/', $k, $m)) {
		$days[] = array($m[1], $v);
	}
}
$calendars->setDays($calendar, $days);

header("Location: ../admin/calendars_list.php");
