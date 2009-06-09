<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

#****************************************************************************
#*  Checking for query string.  Go back to collection list if none found.
#****************************************************************************
if (!isset($_GET["code"])){
	header("Location: ../admin/biblio_copy_fields_list.php");
	exit();
}
$code = $_GET["code"];
$description = $_GET["desc"];

$BCF = new BiblioCopyFields;
$BCF->deleteOne($code);

$msg = T('biblioCopyFieldsDelMsg', array('desc'=>$description));

header("Location: ../admin/biblio_copy_fields_list.php?msg=".U($msg));
