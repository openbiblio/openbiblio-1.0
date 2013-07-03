<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "opac";
$nav = "account/bookings";

require_once(REL(__FILE__, "../model/Members.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../classes/Report.php"));
require_once(REL(__FILE__, "../classes/Calendar.php"));
require_once(REL(__FILE__, "../classes/Links.php"));
require_once(REL(__FILE__, "../classes/TableDisplay.php"));


if (!isset($_SESSION['authMbrid'])) {
	header('Location: ../opac/login.php');
	exit();
}

class BookingCalendar extends Calendar {
	function BookingCalendar($start, $end, $mbrid, $calendar=OBIB_MASTER_CALENDAR) {
		$this->mbrid = $mbrid;
		$this->calendar = $calendar;

		$bookings = new Bookings;
		$rows = $bookings->getCalendarTotals($start, $end, $calendar, $mbrid);
		$this->copies=array();
		$this->open = array();
		while ($row = $rows->fetch_assoc()) {
			$this->copies[$row['date']] = $row['ncopies'];
			$this->open[$row['date']] = $row['open'];
		}
	}
	function getCalendarLink($month, $year) {
		$params = 'month='.U($month).'&year='.U($year);
		return "../opac/bookings.php?".$params;
	}
	function getDateClass($day, $month, $year) {
		$class = "";
		$today = getdate(time());
		if ($year == $today["year"]
				&& $month == $today["mon"]
				&& $day == $today["mday"]) {
			$class .= "calendarToday ";
		}
		$date = sprintf("%d-%02d-%02d", $year, $month, $day);
		if ($this->open[$date] == 'Yes') {
			$class .= "calendarOpen ";
		} elseif ($this->open[$date] == 'No') {
			$class .= "calendarClosed ";
		} else {
			$class .= "calendarUnknown ";
		}
		$count = $this->copies[$date];
		if ($this->allCopies and $count >= $this->allCopies) {
			$class .= "bookedAll ";
		} elseif ($count >= 1) {
			$class .= "bookedSome ";
		} else {
			$class .= "bookedNone ";
		}
		return $class;
	}
}

$d = getdate(time());
if ($_REQUEST['month'] != "") {
	$d['mon'] = $_REQUEST['month'];
}
if ($_REQUEST['year'] != "") {
	$d['year'] = $_REQUEST['year'];
}
list($start, $err) = Date::read_e($d['year'].'-'.$d['mon'].-01);
if ($err) {
	Fatal::internalError(T("Unexpected error reading date: %date%", array('date'=>$err->toStr())));
}
$end = Date::addMonths($start, 3);

Page::header_opac(array('nav'=>$nav, 'title'=>''));

echo '<h2>'.T("My Bookings").'</h2>';
$members = new Members;
$cal = new BookingCalendar($start, $end, $_SESSION['authMbrid'], $members->getCalendarId($_SESSION['authMbrid']));
echo '<div style="padding: 0 auto; border-top: solid #006500 2px; border-bottom: solid #006500 2px">';
echo $cal->getHThreeMonthView($d['mon'], $d['year']);
echo '</div>';

$rpt = Report::create('bookings');
$rpt->init(array(
	'mbrid'=>$_SESSION['authMbrid'],
	'ret_since'=>$start,
	'out_before'=>$end,
	'order_by'=>'outd'
));
$t = new TableDisplay;
$t->columns = array(
	$t->mkCol(T("Status")),
	$t->mkCol(T("Item")),
	$t->mkCol(T("Title")),
	$t->mkCol(T("Checkout")),
	$t->mkCol(T("Return")),
);
echo $t->begin();
while ($r = $rpt->each()) {
	echo $t->row(
		Links::mkLink('booking_opac', H($r['bookingid']), H($r['status'])),
		H($r['item_num']),
		Links::mkLink('biblio', H($r['bibid']), H($r['title'])),
		H($r['outd']),
		H($r['retd'])
	);
}
echo $t->end();

 ;
