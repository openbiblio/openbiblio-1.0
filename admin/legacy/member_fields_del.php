<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/MemberCustomFields.php"));

if (!isset($_GET["code"])){
	header("Location: ../admin/member_fields_list.php");
	exit();
}
$code = $_GET["code"];
$description = $_GET["desc"];

$fields = new MemberCustomFields;
$fields->deleteOne($code);

$msg = T('memberFieldsDelMsg', array('desc'=>$description));
header("Location: ../admin/member_fields_list.php?msg=".U($msg));
