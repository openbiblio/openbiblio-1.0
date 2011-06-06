<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$restrictToMbrAuth = TRUE;
$nav = "booking_deleted";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));

$bookingid = $_REQUEST["bookingid"];
$bookings = new Bookings;

$bookings->deleteOne($bookingid);

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
echo T('Booking deleted');
 ;
