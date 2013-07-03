<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "book";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/Members.php"));
	require_once(REL(__FILE__, "../model/Bookings.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Calendar.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));


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
			$params .= '&calendar='.U($this->calendar);
			return "../circ/bookdate.php?".$params;
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

	$mbrid = $_SESSION['currentMbrid'];
	if (isset($_REQUEST['bibid'])) {
		$bibid = $_REQUEST['bibid'];
	} elseif (isset($_SESSION['postVars'])) {
		$bibid = $_SESSION[postVars]['bibid'];
	} else {
		$bibid = NULL;
	}
	if (!$mbrid or !$bibid) {
		header('Location: ../circ/index.php');
	}
	$biblios = new Biblios();
	$biblio = $biblios->getOne($bibid);

	$members = new Members;
	$mbr = $members->getOne($_SESSION['currentMbrid']);

	$collections = new Collections;
	$collection = $collections->getOne($biblio['collection_cd']);
	$collection = $collections->getOne($biblio['collection_cd']);
	if ($collection['type'] != 'Circulated') {
		header("Location: ../biblio_view.php?bibid=".U($bibid)."&msg=".T("This item cannot be booked."));
		exit();
	}
	$cdat = $collections->getTypeData($collection);

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	if (isset($_REQUEST['msg'])) {
		echo '<p class="error">'.$_REQUEST['msg'].'</p>';
	}
	if (isset($_SESSION['postVars']['confirm_date'])) {
		echo '<p class="error">'.T("To ignore book again").'</p>';
	}
	echo 'h2'.T("Choose booking date").'</h2>';
?>
<form name="bookdate" id="bookdate" method="post" action="../circ/book.php">
<input type="hidden" name="bibid" value="<?php echo H($bibid); ?>" />
<?php
	if (isset($_SESSION['postVars']['confirm_date'])) {
		echo '<input type="hidden" name="confirm_date" value="'
				 . H($_SESSION['postVars']['confirm_date']).'" />';
	}
	if (isset($_SESSION['postVars']['confirm_days'])) {
		echo '<input type="hidden" name="confirm_days" value="'
				 . H($_SESSION['postVars']['confirm_days']).'" />';
	}
?>
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
<tr>
<td class="name"><?php echo T("Item Number:"); ?></td>
<td class="value">
<?php
		echo H($biblio[marc]->getValue('099$a'));
 ?>
</td>
</tr>
<tr>
<td class="name"><?php echo T("Title:"); ?></td>
<td class="value">
<?php
		echo H($biblio['marc']->getValue('245$a')). ' ';
		echo H($biblio['marc']->getValue('245$b'));
 ?>
</td>
</tr>
<tr>
<td class="name"><?php echo T("Date:"); ?></td>
<td class="value"><?php echo inputfield('date', 'date','' , array('size'=>10)); ?></td>
</tr>
<tr>
<td class="name"><?php echo T("Days Out:"); ?></td>
<td class="value"><?php echo inputfield('number', 'days', $cdat['days_due_back'], array('size'=>4)); ?></td>
</tr>
<tr>
<td></td><td class="value"><input type="submit" value="<?php echo T("Submit"); ?>" class="button" /></td>
</tr>
</table>
</form>
<?php

	$d = getdate(time());
	if ($_REQUEST['month'] != "") {
		$d['mon'] = $_REQUEST['month'];
	}
	if ($_REQUEST['year'] != "") {
		$d['year'] = $_REQUEST['year'];
	}
	list($start, $err) = Date::read_e($d['year'].'-'.$d['mon'].'-01');
	assert(!$err);
	$end = Date::addMonths($start, 3);
	$cal = new BookingCalendar($start, $end, $bibid, $members->getCalendarId($mbrid));
	echo '<div style="padding: 0 auto; border-top: solid #006500 2px; border-bottom: solid #006500 2px">';
	echo $cal->getHThreeMonthView($d['mon'], $d['year']);
	echo '</div>';

	$rpt = Report::create(bookings);
	$rpt->init(array(
		'ret_since'=>$start,
		'out_before'=>$end,
		'bibid'=>$bibid,
	));
	$disp = new ReportDisplay($rpt);
	$t = new TableDisplay;
	$t->columns = $disp->columns();
	echo $t->begin();
	while ($r = $rpt->fetch_assoc()) {
		echo $t->rowArray($disp->row($r));
	}
	echo $t->end();

	 ;
