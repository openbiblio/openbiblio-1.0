<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Sites.php"));

if (!isset($_REQUEST["siteid"])){
	header("Location: ../admin/sites_list.php");
	exit();
}

$sites = new Sites;
$sites->deleteOne($_REQUEST["siteid"]);

$msg = T("Site, %name%, has been deleted.", array('name'=>$_REQUEST["name"]));
header("Location: ../admin/sites_list.php?msg=".U($msg));
