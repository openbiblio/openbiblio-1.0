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
$user = array(
	'userid'=>$_POST['userid'],
	'last_name'=>$_POST['last_name'],
	'first_name'=>$_POST['first_name'],
	'username'=>$_POST['username'],
);
foreach (array('circ', 'circ_mbr', 'admin', 'reports', 'suspended') as $flg) {
	if (isset($_POST[$flg.'_flg'])) {
		$user[$flg.'_flg'] = 'Y';
	} else {
		$user[$flg.'_flg'] = 'N';
	}
}
$errs = $staff->update_el($user);
if ($errs) {
	FieldError::backToForm("../admin/staff_edit_form.php", $errs);
}

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo T("Staff member, %name%, has been updated.", array('name'=>H($user['first_name']).' '.H($user['last_name']))).'<br /><br />';
echo '<a href="../admin/staff_list.php">'.T("Return to staff list").'</a>';

Page::footer();
