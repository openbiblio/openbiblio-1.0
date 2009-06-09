<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "view";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Holds.php"));


#****************************************************************************
#*  Checking for post vars.  Go back to form if none found.
#****************************************************************************
if (count($_POST) == 0) {
	header("Location: ../circ/index.php");
	exit();
}
$mbrid = $_POST["mbrid"];
$barcode = $_POST["holdBarcodeNmbr"];
$copies = new Copies;
$copy = $copies->getByBarcode($barcode);
if (!$copy) {
  $msg = T("Copy barcode %barcode% does not exist", array('barcode'=>$barcode));
	header("Location: ../circ/mbr_view.php?mbrid=".U($_POST["mbrid"])."&msg=".U($msg));
	exit();
}

#**************************************************************************
#*  Insert hold
#**************************************************************************
// we need to also insert into status history table
$holds = new Holds;
$holds->insert(array(
	'bibid'=>$copy['bibid'],
	'copyid'=>$copy['copyid'],
	'mbrid'=>$mbrid,
));

#**************************************************************************
#*  Go back to member view
#**************************************************************************
header("Location: ../circ/mbr_view.php?mbrid=".U($_POST["mbrid"]));
