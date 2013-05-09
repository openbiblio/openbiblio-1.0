<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../model/Cart.php"));
$tab="circulation";
$nav = "pending";

if (count($_REQUEST) == 0 or !isset($_REQUEST['id'])) {
  $msg = T("No bookings selected for checkout.");
	header("Location: ../circ/bookings.php?msg=".U($msg));
	exit();
}

$checkouts = array();
foreach ($_REQUEST['id'] as $bookingid) {
	if (isset($_REQUEST["barcodes"][$bookingid])) {
		$checkouts[$bookingid] = $_REQUEST["barcodes"][$bookingid];
	} else {
		$checkouts[$bookingid] = NULL;
	}
}
$bookings = new Bookings;
$errors = $bookings->checkoutBatch_el($checkouts);

if (!empty($errors)) {
	array_unshift($errors, new Error(T("Bookings not checked out, errors below")));
	FieldError::backToForm('../circ/booking_pending.php', $errors);
}

$cart = getCart(bookingid);
foreach ($checkouts as $bookingid => $barcode) {
	if (!$cart->contains($bookingid)) {
		$cart->add($bookingid);
	}
}
header("Location: ".$cart->viewURL()."?tab=".U($tab));
