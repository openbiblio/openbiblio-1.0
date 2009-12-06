<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$restrictToMbrAuth = TRUE;
$nav = "newconfirm";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Members.php"));

if (count($_POST) == 0) {
	header("Location: ../circ/mbr_new_form.php");
	exit();
}

$mbr = array(
	'siteid'=>$_POST["siteid"],
	'barcode_nmbr'=>$_POST["barcode_nmbr"],
	'last_name'=>$_POST["last_name"],
	'first_name'=>$_POST["first_name"],
	'address1'=>$_POST["address1"],
	'address2'=>$_POST["address2"],
	'city'=>$_POST["city"],
	'state'=>$_POST["state"],
	'zip'=>$_POST["zip"],
	'zip_ext'=>$_POST["zip_ext"],
	'home_phone'=>$_POST["home_phone"],
	'work_phone'=>$_POST["work_phone"],
	'email'=>$_POST["email"],
	//'password'=>$_POST["password"],
	//"confirm-pw"=>$_POST["confirm-pw"],
	'classification'=>$_POST["classification"],
	//'school_grade'=>$_POST["school_grade"],
	//'school_teacher'=>$_POST["school_teacher"],
);

#****************************************************************************
#*  Autobarco
#*
#* FIXME RACE: User A and User B each try to insert a copy concurrently.
#* User A's process gets next copy id, then checks for a duplicate barcode,
#* Before the final insert, though, User B's process asks for the next copy id
#* and checks for a duplicate barcode.  At that point, both inserts will succeed
#* and two copies will have the same barcode.  Several different interleavings
#* either cause the duplicate barcode check to fail or cause duplicate barcodes
#* to be entered.  This can be fixed with a lock or by an atomic
#* get-and-increment-sequence-value operation.  I'll fix it later. -- Micah
#*
#* perhaps a random number would be a better choice, then two near simultaneous
#* requests would be even less likely to be duplicates. -- Fred
#****************************************************************************
$members = new Members;

if (($_SESSION['mbrBarcode_flg']=='Y') and ($_SESSION['mbr_autoBarcode_flg']=='Y')) {
	$nzeros = "5";
	$mbrNmbr= $members->getNextMbr();
	$_POST["barcode_nmbr"] = sprintf("%0".$nzeros."s",$mbrNmbr);
	$mbr['barcode_nmbr'] = $_POST["barcode_nmbr"];
}

list($mbrid, $errors) = $members->insert_el($mbr);
if ($errors) {
	FieldError::backToForm('../circ/mbr_new_form.php', $errors);
}

/*
FIXME -- broken code - fred
	$customFields = new MemberCustomFields;
$custom = array();
foreach ($customFields->getSelect() as $name => $title) {
	if (isset($_REQUEST['custom_'.$name])) {
		$custom[$name] = $_POST['custom_'.$name];
	}
	}
$members->setCustomFields($mbrid, $custom);
*/

$msg = T("Member has been successfully added.");
header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid)."&reset=Y&msg=".U($msg));
