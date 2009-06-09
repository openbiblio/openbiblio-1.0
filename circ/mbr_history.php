<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "mbr/hist";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Members.php"));
	require_once(REL(__FILE__, "../model/Bookings.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Calendar.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));


	#****************************************************************************
	#*  Checking for get vars.  Go back to form if none found.
	#****************************************************************************
	if (count($_REQUEST) == 0) {
		header("Location: ../circ/index.php");
		exit();
	}

	$mbrid = $_REQUEST["mbrid"];
	$members = new Members;
	$mbr = $members->getOne($mbrid);

	class BookingCalendar extends Calendar {
		function BookingCalendar($start, $end, $mbrid, $calendar=OBIB_MASTER_CALENDAR) {
			$this->mbrid = $mbrid;
			$this->calendar = $calendar;

			$bookings = new Bookings;
			$rows = $bookings->getCalendarTotals($start, $end, $calendar, $mbrid);
			$this->copies=array();
			$this->open = array();
			while ($row = $rows->next()) {
				$this->copies[$row['date']] = $row['ncopies'];
				$this->open[$row['date']] = $row['open'];
			}
		}
		function getCalendarLink($month, $year) {
			$params = 'month='.U($month).'&year='.U($year);
			if ($this->mbrid !== NULL) {
				$params .= '&mbrid='.U($this->mbrid);
			}
			$params .= '&calendar='.U($this->calendar);
			return "../circ/mbr_history.php?".$params;
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

	#**************************************************************************
	#*  Show biblio checkout history
	#**************************************************************************
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h2><?php echo T("Booking History"); ?></h2>
<table class="biblio_view">
<tr>
<td class="name"><?php echo T("Member:"); ?></td>
<td class="value">
<?php
		echo H($mbr['first_name']).' ' ;
		echo H($mbr['last_name']).' ' ;
		echo '('.H($mbr['barcode_nmbr']).')';
 ?>
</td>
</tr>
</table>
<?php

	$d = getdate(time());
	if ($_REQUEST['month'] != "") {
		$d['mon'] = $_REQUEST['month'];
	}
	if ($_REQUEST['year'] != "") {
		$d['year'] = $_REQUEST['year'];
	}
	list($start, $err) = Date::read_e($d['year'].'-'.$d['mon'].-01);
	assert(!$err);
	$end = Date::addMonths($start, 3);
	$cal = new BookingCalendar($start, $end, $mbrid, $members->getCalendarId($mbrid));
	echo '<div style="padding: 0 auto; border-top: solid #006500 2px; border-bottom: solid #006500 2px">';
	echo $cal->getHThreeMonthView($d['mon'], $d['year']);
	echo '</div>';

	$rpt = Report::create('bookings');
	$rpt->init(array(
		'ret_since'=>$start,
		'out_before'=>$end,
		'mbrid'=>$mbrid,
	));
	$disp = new ReportDisplay($rpt);
	$t = new TableDisplay;
	$t->columns = $disp->columns();
	echo $t->begin();
	while ($r = $rpt->next()) {
		echo $t->rowArray($disp->row($r));
	}
	echo $t->end();

	Page::footer();
