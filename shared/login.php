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
echo "in login.php ln#58<br />\n";

$username = $_POST["username"];
$error_found = false;
if ($username == "") {
	$error_found = true;
	$pageErrors["username"] = T("Username is required.");
    //echo "need user name<br />\n";
}
$pwd = $_POST["pwd"];
if ($pwd == "") {
	$error_found = true;
	$pageErrors["pwd"] = T("Password is required.");
    //echo "need pw<br />\n";
}
//echo "user: $username; pw: $pwd<br />\n";

if (!$error_found) {
    //echo "in login ln#32";echo "got id & pw<br />\n";echo "<br />\n";

	$staff = new Staff($dbConst);
    //echo "username: '$username';  pwd: '".md5($pwd)."'<br />\n";
	$rows = $staff->getMatches(array('username'=>$username, 'pwd'=>md5($pwd)));
	$user = $rows->fetch(PDO::FETCH_ASSOC);
    $nUsers = count($user);
    //echo "in login ln#38 [";print_r($user);echo "] <br />\n";
    //echo "in login # matches found: $nUsers <br />\n";

	//if ($rows->count() == 1) {
	//if ($rows->num_rows == 1) {
    if ($nUsers  == 0) {
		# invalid username or password.  Add one to login attempts.
		$error_found = true;
		$pageErrors["pwd"] = T("Invalid signon.");
        //echo "invalid signin<br />\n";

		# FIXME - The old code would suspend a user's account after three
		# failed login attempts.  That's a very easy denial of service,
		# if you know the staff usernames.  I've removed that feature,
		# but we are now open to an online dictionary attack.  A better
		# method might be to disallow login from a particular IP for a
		# time after several failed attempts.
	}
}
//echo "in login ln#57<br />\n";

if ($error_found == true) {
    echo "login error found<br />\n";
	$_SESSION["postVars"] = $_POST;
	$_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../shared/loginform.php");
	exit();
}

if ($user['suspended_flg'] == 'Y') {
    header("Location: ../shared/suspended.php");
	exit();
}

unset($_SESSION["postVars"]);
unset($_SESSION["pageErrors"]);

if(isset($_REQUEST['selectSite'])){
	$_SESSION['current_site'] = $_REQUEST['selectSite'];
	// Also set as cookie, this allows in libraries with multiple sites to automatic logon on the same library "(set for 1 year)
	setcookie("OpenBiblioSiteID", $_SESSION['current_site'], time()+60*60*24*365);
}

$_SESSION["username"] = $user['username'];
$_SESSION["userid"] = $user['userid'];
$_SESSION["secret_key"] = $user['secret_key'];
$_SESSION["loginAttempts"] = 0;
$_SESSION["hasAdminAuth"] = ($user['admin_flg'] == 'Y');
$_SESSION["hasCircAuth"] = ($user['circ_flg'] == 'Y');
$_SESSION["hasCircMbrAuth"] = ($user['circ_mbr_flg'] == 'Y');
$_SESSION["hasCatalogAuth"] = ($user['catalog_flg'] == 'Y');
$_SESSION["hasReportsAuth"] = ($user['reports_flg'] == 'Y');
$_SESSION["hasToolsAuth"] = ($user['tools_flg'] == 'Y');
echo "in login ln#92<br />\n";

setSessionFmSettings();
//print_r($_SESSION);echo "<br />";exit();

header("Location: ".$_SESSION["returnPage"]);
exit();
