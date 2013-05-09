<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");


$actions = array(
	'cart_add' => '../shared/cart_add.php',
	'cart_del' => '../shared/cart_del.php',
	'booking_mbr_add' => '../circ/booking_edit.php',
	'booking_mbr_del' => '../circ/booking_edit.php',
);

$filename = NULL;
foreach ($actions as $name => $file) {
	if ($_REQUEST['action_'.$name]) {
		$filename = $file;
		break;
	}
}

assert('$filename !== NULL');
require_once($filename);
