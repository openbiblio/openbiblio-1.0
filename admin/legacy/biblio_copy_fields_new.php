<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "admin";
$nav = "biblio_copy_fields";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

if (count($_POST) == 0) {
	header("Location: ../admin/biblio_copy_fields_new_form.php");
	exit();
}

$fields = new BiblioCopyFields;
list($id, $errs) = $fields->insert_el(array(
	'code'=>@$_POST['code'],
	'description'=>@$_POST['description'],
));
if ($errs) {
	FieldError::backToForm('../admin/biblio_copy_fields_new_form.php', $errs);
}
$msg = T('biblioCopyFieldsNewMsg', array('desc'=>H(@$_POST['description'])));
header("Location: ../admin/biblio_copy_fields_list.php?msg=".U($msg));
