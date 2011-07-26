<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab='admin';
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/MaterialFields.php"));

if (!isset($_GET["material_field_id"])) {
	Fatal::internalError(T("material_field_id not set"));
}

$mf = new MaterialFields;
$mf->deleteOne($_GET["material_field_id"]);
$msg = T("Field Successfully Deleted");
header("Location: material_fields_view.php?material_cd=".U($_GET["material_cd"])."&msg=".U($msg));
