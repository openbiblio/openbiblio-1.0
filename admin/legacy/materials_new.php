<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/MediaTypes.php"));

#****************************************************************************
#*  Checking for post vars.  Go back to form if none found.
#****************************************************************************
if (count($_POST) == 0) {
	header("Location: ../admin/materials_new_form.php");
	exit();
}

$mattypes = new MediaTypes;
$type = array(
	'description'=>$_POST["description"],
	'default_flg'=>'N',
	'adult_checkout_limit'=>$_POST["adult_checkout_limit"],
	'juvenile_checkout_limit'=>$_POST["juvenile_checkout_limit"],
	'image_file'=>$_POST["image_file"],
);

list($id, $errors) = $mattypes->insert_el($type);
if (empty($errors)) {
	$msg = T("Material type, %desc%, has been added.", array('desc'=>H($type['description'])));
	header("Location: ../admin/materials_list.php?msg=".U($msg));
	exit();
} else {
	FieldError::backToForm('../admin/materials_new_form.php', $errors);
}
