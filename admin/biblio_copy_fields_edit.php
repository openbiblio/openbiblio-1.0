<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

#****************************************************************************
#*  Checking for post vars.  Go back to form if none found.
#****************************************************************************

if (count($_POST) == 0) {
	header("Location: ../admin/biblio_copy_fields_list.php");
	exit();
}

$BCF = new BiblioCopyFields;
$bcf_array = array(
	'code'=>$_POST["code"],
	'description'=>$_POST["description"],
);

$errors = $BCF->update_el($bcf_array);
if (empty($errors)) {
	$msg = T('biblioCopyFieldsEditMsg', array('desc'=>H($coll['description'])));
	header("Location: ../admin/biblio_copy_fields_list.php?msg=".U($msg));
	exit();
} else {
	FieldError::backToForm('../admin/biblio_copy_edit_form.php', $errors);
}
