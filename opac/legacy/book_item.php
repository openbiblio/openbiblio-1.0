<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/Collections.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Members.php"));
require_once(REL(__FILE__, "../model/Sites.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../classes/Calendar.php"));
require_once(REL(__FILE__, "../classes/Form.php"));

$tab = "opac";
$nav = "book_item";

if (!isset($_SESSION[authMbrid])) {
	header('Location: ../opac/login.php');
	exit();
}

class BookingCalendar extends Calendar {
	function BookingCalendar($start, $end, $bibid, $calendar=OBIB_MASTER_CALENDAR) {
		$this->bibid = $bibid;
		$this->calendar = $calendar;

		$copies = new Copies;
		$bcopies = $copies->getMatches(array('bibid'=>$bibid));
		$this->allCopies = $bcopies->count();
		$bookings = new Bookings;
		$rows = $bookings->getCalendarTotals($start, $end, $calendar, NULL, $bibid);
		$this->copies=array();
		$this->open = array();
		while ($row = $rows->fetch_assoc()) {
			$this->copies[$row['date']] = $row['ncopies'];
			$this->open[$row['date']] = $row['open'];
		}
	}
	function getCalendarLink($month, $year) {
		$params = 'month='.U($month).'&year='.U($year);
		if ($this->bibid !== NULL) {
			$params .= '&bibid='.U($this->bibid);
		}
		return "../opac/book_item.php?".$params;
	}
	function getDateHTML($day, $month, $year) {
		return '<a class="datelink" href="#" onclick="document.forms[\bookdate\].date.value=\.$year.-.$month.-.$day.\">.$day.</a>'; //FIXME?
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

function book($bibid, $mbrid, $date, $days_out) {
	list($book_dt, $error) = Date::read_e($date);
	if ($error) {
		return array(new FieldError(date, $error->toStr()));
	}
	if ($book_dt <= date('Y-m-d')) {
		return array(new FieldError('date', T('bookItemOnlyStaff')));
	}
	$due_dt = Date::addDays($book_dt, $days_out);
	$booking = array(
		'bibid'=>$bibid,
		'book_dt'=>$book_dt,
		'due_dt'=>$due_dt,
		'mbrids'=>array($mbrid),
	);
	$bookings = new Bookings;
	list($id, $errs) = $bookings->insert_el($booking);
	return $errs;
}

$form = array(
	'title' => T("Choose booking date"),
	'name' => 'bookdate',
	'action' => '../opac/book_item.php',
	'fields' => array(
		array('name'=>'bibid', 'type'=>'hidden'),
		array('name'=>'month', 'type'=>'hidden'),	# Just for calendar display
		array('name'=>'year', 'type'=>'hidden'),	# Just for calendar display
		array('name'=>'item', 'type'=>'fixed', 'title'=>T("Item Number:")),
		array('name'=>'title', 'type'=>'fixed', 'title'=>T("Title:")),
		array('name'=>'date', 'title'=>T("Date:"), 'attrs'=>array('size'=>10)),
		array('name'=>'days_out', 'title'=>T("Days Out:"), 'attrs'=>array('size'=>5)),
	),
);
list($values, $errs) = Form::getCgi_el($form['fields']);

if (!$values['bibid']) {
	header('Location: ../opac/index.php');
	exit();
}

$biblios = new Biblios;
$bib = $biblios->getOne($values['bibid']);
$collections = new Collections;
$collection = $collections->getOne($bib['collection_cd']);
if ($collection['type'] != 'Circulated') {
	$errs[] = new Error(T("This item cannot be booked."));
}
$cdat = $collections->getTypeData($collection);
$values['item'] = $bib['marc']->getValue('099$a');
$values['title'] = $bib['marc']->getValue('245$a');
if (!$values['days_out']) {
	$values['days_out'] = $cdat['days_due_back'];
}

if (!$values['_posted']) {
	$errs = array();
} else {
	$errs = book($values['bibid'], $_SESSION['authMbrid'], $values['date'], $values['days_out']);
	if (!$errs) {
		header('Location: ../opac/bookings.php');
		exit();
	}
}

Page::header_opac(array('nav'=>$nav, 'title'=>''));

$sites = new Sites;
$site = $sites->getByMbrid($_SESSION['authMbrid']);
if ($site['delivery_note']) {
	echo '<p>'.str_replace("\n", "<br />", H($site['delivery_note'])).'</p>';
}

$form['values'] = $values;
$form['errors'] = $errs;
Form::display($form);

$d = getdate(time());
if ($values['month']) {
	$d['mon'] = $values['month'];
}
if ($values['year']) {
	$d['year'] = $values['year'];
}
list($start, $err) = Date::read_e($d['year'].'-'.$d['mon'].-01);
if ($err) {
	Fatal::internalError(T("Unexpected error reading date: %date%", array('date'=>$err->toStr())));
}
$end = Date::addMonths($start, 3);
$members = new Members;
$cal = new BookingCalendar($start, $end, $values['bibid'], $members->getCalendarId($_SESSION['authMbrid']));
echo '<div style="padding: 0 auto; border-top: solid #006500 2px; border-bottom: solid #006500 2px">';
echo $cal->getHThreeMonthView($d['mon'], $d['year']);
echo '</div>';

 ;

