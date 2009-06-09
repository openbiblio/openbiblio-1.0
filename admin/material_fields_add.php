<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/MaterialFields.php"));

$tab = "admin";
$nav = "new";

require_once(REL(__FILE__, "../shared/logincheck.php"));

if (count($_POST) == 0) {
	header("Location: ../admin/material_list.php");
	exit();
}

$rec = array();
$rec['material_cd'] = $_POST["material_cd"];
$rec['tag'] = $_POST["tag"];
$rec['subfield_cd']  = $_POST["subfield_cd"];
$rec['position']  = $_POST["position"];
$rec['label']  = $_POST["label"];
$rec['form_type'] = $_POST["form_type"];
$rec['required'] = $_POST["required"];
$rec['repeatable'] = $_POST["repeatable"];
$rec['search_results'] = $_POST["search_results"];

if (!$rec['label']) {
	$pageErrors['label'] = T("Field is required.");
}
if (!empty($pageErrors)) {
	$_SESSION["postVars"] = $_POST;
	$_SESSION["pageErrors"] = $pageErrors;
	header("Location: ../admin/material_fields_add_form.php");
	exit();
}

$mat = new MaterialFields;
$mat->insert($rec);

unset($_SESSION["postVars"]);
unset($_SESSION["pageErrors"]);
$msg = T("New Field Added Successfully");
header("Location: material_fields_view.php?material_cd=".U($rec['material_cd'])."&msg=".U($msg));
