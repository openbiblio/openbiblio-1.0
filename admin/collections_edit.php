<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Collections.php"));

#****************************************************************************
#*  Checking for post vars.  Go back to form if none found.
#****************************************************************************

if (count($_POST) == 0) {
	header("Location: ../admin/collections_list.php");
	exit();
}

$collections = new Collections;
$coll = array(
	'code'=>$_POST["code"],
	'description'=>$_POST["description"],
	'type'=>$_POST["type"],
	'days_due_back'=>$_POST["days_due_back"],
	'daily_late_fee'=>$_POST["daily_late_fee"],
	'restock_threshold'=>$_POST["restock_threshold"],
);

$errors = $collections->update_el($coll);
if (empty($errors)) {
	$msg = T("Collection, %desc%, has been updated.", array('desc'=>H($coll['description'])));
	header("Location: ../admin/collections_list.php?msg=".U($msg));
	exit();
} else {
	FieldError::backToForm('../admin/collections_edit_form.php', $errors);
}
