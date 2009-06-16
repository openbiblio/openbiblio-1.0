<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

require_once(REL(__FILE__, "../model/Staff.php"));

$pageErrors = "";
if (count($_POST) == 0) {
	header("Location: ../shared/loginform.php");
	exit();
}

$username = $_POST["username"];
$error_found = false;
if ($username == "") {
	$error_found = true;
	$pageErrors["username"] = T("Username is required.");
}
$pwd = $_POST["pwd"];
if ($pwd == "") {
	$error_found = true;
	$pageErrors["pwd"] = T("Password is required.");
}

if (!$error_found) {
	$staff = new Staff;
	$rows = $staff->getMatches(array('username'=>$username, 'pwd'=>md5($pwd)));
	if ($rows->count() == 1) {
		$user = $rows->next();
	} else {
		# invalid username or password.  Add one to login attempts.
		$error_found = true;
		$pageErrors["pwd"] = T("Invalid signon.");
		
		# FIXME - The old code would suspend a user's account after three
		# failed login attempts.  That's a very easy denial of service,
		# if you know the staff usernames.  I've removed that feature,
		# but we are now open to an online dictionary attack.  A better
		# method might be to disallow login from a particular IP for a
		# time after several failed attempts.
	}
}

if ($error_found == true) {
	$_SESSION["postVars"] = $_POST;
	$_SESSION["pageErrors"] = $pageErrors;
	header("Location: ../shared/loginform.php");
	exit();
}

//if ($user->isSuspended()) {
//	header("Location: ../shared/suspended.php");
//	exit();
//}

unset($_SESSION["postVars"]);
unset($_SESSION["pageErrors"]);

$_SESSION["username"] = $user['username'];
$_SESSION["userid"] = $user['userid'];
$_SESSION["loginAttempts"] = 0;
$_SESSION["hasAdminAuth"] = ($user['admin_flg'] == 'Y');
$_SESSION["hasCircAuth"] = ($user['circ_flg'] == 'Y');
$_SESSION["hasCircMbrAuth"] = ($user['circ_mbr_flg'] == 'Y');
$_SESSION["hasCatalogAuth"] = ($user['catalog_flg'] == 'Y');
$_SESSION["hasReportsAuth"] = ($user['reports_flg'] == 'Y');

header("Location: ".$_SESSION["returnPage"]);
exit();
