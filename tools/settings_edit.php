<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/Form.php"));

$tab = "admin";
$nav = "settings";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

if (count($_POST) == 0) {
	header("Location: ../tools/settings_edit_form.php");
	exit();
}

list($settings, $errs) = Form::getCgi_el(Settings::getFormFields('tools'));
if (empty($errs)) {
	$errs = Settings::setAll_el($settings);
	setSessionFmSettings();
}
else if (!empty($errs)) {
	$_SESSION["postVars"] = $_POST;
	$_SESSION["pageErrors"] = $errs;
	header("Location: ../tools/settings_edit_form.php");
	exit();
}

header("Location: ../tools/settings_edit_form.php?updated=Y");
exit();
