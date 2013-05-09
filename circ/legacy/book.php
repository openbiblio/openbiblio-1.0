<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../classes/Date.php"));
$tab = circulation;


$bibid = $_POST['bibid'];
$mbrid = $_SESSION['currentMbrid'];
$date = $_POST['date'];
$days = $_POST['days'];
$confirm_date = NULL;
$confirm_days = NULL;
if (isset($_POST['onfirm_date'])) {
	$confirm_date = $_POST['confirm_date'];
}
if (isset($_POST['confirm_days'])) {
	$confirm_days = $_POST['confirm_days'];
}

if (!$bibid or !$mbrid or !$date or !$days) {
	header("Location: ../circ/index.php");
	exit();
}

$confirmed=false;
if ($date == $confirm_date and $days == $confirm_days) {
	$confirmed = true;
}

list($book_dt, $error) = Date::read_e($date);
if ($error) {
	$_SESSION['postVars'] = mkPostVars();
	$_SESSION['pageErrors'] = array();
	$_SESSION['pageErrors']['date'] = $error->toStr();
	header("Location: ../circ/bookdate.php");
	exit();
}
$due_dt = Date::addDays($book_dt, $days);
$booking = array(
	'bibid'=>$bibid,
	'book_dt'=>$book_dt,
	'due_dt'=>$due_dt,
	'mbrids'=>array($mbrid),
);
$bookings = new Bookings;
list($bookingid, $errors) = $bookings->insert_el($booking, $confirmed);
if ($errors) {
	$_SESSION['postVars'] = mkPostVars();
	foreach ($errors as $e) {
		if (is_a($e, IgnorableError)) {
			$_SESSION['postVars']['confirm_date'] = $date;
			$_SESSION['postVars']['confirm_days'] = $days;
		}
	}
	$_SESSION['pageErrors'] = array();
	header("Location: ../circ/bookdate.php?msg=".U(Error::listToStr($errors)));
	exit();
}

#**************************************************************************
#*  Go back to member view
#**************************************************************************
header("Location: ../circ/mbr_view.php?mbrid=".$mbrid);
