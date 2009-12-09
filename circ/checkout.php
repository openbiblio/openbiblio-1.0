<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "view";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));

if (count($_POST) == 0) {
	header("Location: ../circ/index.php");
	exit();
}

$bookings = new Bookings;
$err = $bookings->quickCheckout_e($_POST["barcodeNmbr"], array($_POST["mbrid"]));
if ($err) {
	if(is_array($err)){
		$errors = ""; $nErr = 0;
		foreach($err as $error)	{
			if ($nErr > 0) $errors .= '<br />';
			$errors .= $error->toStr();
			$nErr++;
		}
	} elseif (is_object($err)) {
		$errors = $err->toStr();
	} else {
		$errors = $err;
	}
	$pageErrors["barcodeNmbr"] = $errors;
	$postVars["barcodeNmbr"] = $_POST["barcodeNmbr"];
	$_SESSION["postVars"] = $postVars;
	$_SESSION["pageErrors"] = $pageErrors;
	header("Location: ../circ/mbr_view.php?mbrid=".U($_POST["mbrid"]));
	exit();
}

header("Location: ../circ/mbr_view.php?mbrid=".U($_POST["mbrid"]));
