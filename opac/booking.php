<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../classes/InfoDisplay.php"));


$tab = "opac";
$nav = "account/bookings/view";

if (!isset($_SESSION['authMbrid'])) {
	header('Location: ../opac/login.php');
	exit();
}

if (!isset($_REQUEST['bookingid'])) {
	header('Location: ../opac/bookings.php');
	exit();
}

$bookings = new Bookings;
$b = $bookings->getOne($_REQUEST['bookingid']);
if (!in_array($_SESSION['authMbrid'], $b['mbrids'])) {
	header('Location: ../opac/bookings.php');
	exit();
}
$biblios = new Biblios;
$bib = $biblios->getOne($b['bibid']);

$states = array(
	'booked' => T('Booked'),
	'out' => T('Out'),
	'returned' => T('Returned'),
);
if ($b['out_histid']) {
	if ($b['ret_histid']) {
		$status = 'returned';
	} else {
		$status = 'out';
	}
} else {
	$status = 'booked';
}

if ($status == 'booked'
		and isset($_REQUEST['action'])
		and $_REQUEST['action'] == 'delete') {
	$bookings->removeMember($b['bookingid'], $_SESSION['authMbrid']);
	header('Location: ../opac/bookings.php?msg='.U(T('Booking deleted')));
	exit();
}

Page::header_opac(array('nav'=>$nav, 'title'=>''));

$d = new InfoDisplay;
$d->title = T("Booking Info");
if ($status == 'booked') {
	$d->buttons = array(array(
		T("Delete"),
		'../opac/booking.php?action=delete&bookingid='.U($b['bookingid']),
		T("Really delete booking of %item%?", array('item'=>$bib['marc']->getValue('099$a'))),
	));
}
echo $d->begin();
echo $d->row(T("Item:"), $bib['marc']->getValue('099$a'));
echo $d->row(T("Title:"), $bib['marc']->getValue('245$a').' '.$bib['marc']->getValue('245$b'));
echo $d->row(T("Status:"), $states[$status]);
echo $d->row(T("Out Date:"), H($b['book_dt']));
echo $d->row(T("Return Date:"), H($b['due_dt']));
echo $d->end();

Page::footer();

