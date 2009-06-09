<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "admin";
$nav = "staff";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Staff.php"));

if (count($_POST) == 0) {
	header("Location: ../admin/staff_list.php");
	exit();
}

$staff = new Staff;
$errs = $staff->update_el(array(
	'userid'=>$_POST['userid'],
	'pwd'=>$_POST['pwd'],
	'pwd2'=>$_POST['pwd2']
));
if ($errs) {
	FieldError::backToForm('../admin/staff_pwd_reset_form.php', $errs);
}

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo T("Password has been reset.").'<br /><br />';
echo '<a href="../admin/staff_list.php">'.T("Return to staff list").'</a>';

Page::footer();
