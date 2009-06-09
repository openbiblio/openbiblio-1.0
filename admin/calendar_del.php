<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "admin";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Calendars.php"));
require_once(REL(__FILE__, "../functions/errorFuncs.php"));

if (!isset($_REQUEST["calendar"])){
	header("Location: ../admin/calendars_list.php");
	exit();
}

if ($_REQUEST["calendar"] != OBIB_MASTER_CALENDAR) {
	$calendars = new Calendars;
	$calendars->deleteOne($_REQUEST["calendar"]);
	$msg = T("Calendar Deleted");
} else {
	$msg = T("Cannot Delete Master Calendar");
}
header("Location: ../admin/calendars_list.php?msg=".U($msg));
