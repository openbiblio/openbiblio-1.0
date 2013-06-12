<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "calendars/edit";
	$confirm_links = true;

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../classes/Calendar.php"));
	require_once(REL(__FILE__, "../model/Calendars.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	if (isset($_REQUEST['calendar'])) {
		$calendar = $_REQUEST['calendar'];
	} elseif (isset($_SESSION['postVars'])) {
		$calendar = $_SESSION['postVars']['calendar'];
	} else {
		$calendar = NULL;
	}
	if ($calendar) {
		$template = $calendar;
		$calendars = new Calendars;
		$cal = $calendars->getOne($calendar);
		if (empty($cal)) {
			header('Location: ../admin/calendars_list.php');
		}
		$calname = $cal['description'];
	} else {
		$template = 1;
		$calname = '';
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	if (isset($_REQUEST['msg'])) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}
	class EditingCalendar extends Calendar {
		function EditingCalendar($calendar, $start, $end) {
			$this->calendar = $calendar;

			$calendars = new Calendars;
			$rows = $calendars->getDays($calendar, $start, $end);
			$this->open = array();
			while ($row = $rows->next()) {
				if ($row['open'] == 'No') {
					$this->open[$row['date']] = 'No';
				} else {
					$this->open[$row['date']] = 'Yes';
				}
			}
		}
		function getCalendarLink($month, $year) {
			$params = 'month='.U($month).'&year='.U($year);
			$params .= '&calendar='.U($this->calendar);
			return "../admin/calendar_edit_form.php?".$params;
		}
		function getWeekDayHTML($wday, $month, $year) {
			$f = sprintf("toggleDays('%1d', '%04d', '%02d')",
				H($wday), H($year), H($month));
			return '<a onclick="'.$f.'">' . $this->dayNames[$wday] . '</a>';
		}
		function getMonthNameHTML($month, $year, $showYear) {
			$monthName = $this->monthNames[$month - 1];
			$f = sprintf("toggleDays('*', '%04d', '%02d')",
				H($year), H($month));
			return '<a onclick="'.$f.'">' . $monthName
				. (($showYear > 0) ? " " . $year : "") . '</a>';
		}
		function getDateHTML($day, $month, $year) {
			$date = sprintf("%04d-%02d-%02d", $year, $month, $day);
			if ($this->open[$date] == 'Yes') {
				$class .= "calendarOpen ";
			} elseif ($this->open[$date] == 'No') {
				$class .= "calendarClosed ";
			} else {
				$class .= "calendarUnknown ";
			}
			$dt = getdate(mktime(0, 0, 0, $month, $day, $year));
			$id = $dt['wday'].'-'.$date;
			return '<input type="hidden" id="input-'.H($id).'" '
				. 'name="input-'.H($id).'" value="'.H($this->open[$date]).'" />'
				. '<a onclick="toggleDay(\''.$id.'\')" >'.$day.'</a>';
		}
		function getDateId($day, $month, $year) {
			$dt = getdate(mktime(0, 0, 0, $month, $day, $year));
			$date = sprintf("%1d-%04d-%02d-%02d", $dt['wday'], $year, $month, $day);
			return "date-".$date;
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
			return $class;
		}
	}
?>
<style>
	.calendarClosed { background-color:green; color:white; }
</style>
<script type="text/javascript">
	function toggleDay(id) {
		var input$ = $('#input-'+id);
		var cell$ = $('#date-'+id);
		var className = "";
		if (cell$.hasClass('calendarToday')) {
			className = "calendarToday ";
		}
		if(input$.val() == 'Yes') {
      input$.val('No');
			cell$.removeClass('calendarOpen');
			cell$.addClass('calendarClosed');
		} else {
      input$.val('Yes');
			cell$.removeClass('calendarClosed');
			cell$.addClass('calendarOpen');
		}
		modified=true; // for link confirmation
	}
	function toggleDays(wday, year, month) {
		var pattern = '^input-(';
		if (wday == '*') {
			pattern += '[0-9]';
		} else {
			pattern += wday;
		}
		pattern += '-'+year+'-'+month+'-[0-9][0-9])$';
		var re = new RegExp(pattern);
		$('input').each(function(n){
			var m = re.exec(this.id);
			if (m) toggleDay(m[1]);
		});
	}
</script>

	<h3><?php echo T("Edit Calendar"); ?></h3>

	<p class="note"><?php echo T("calendarEditFormMsg");?></p>
	<form name="calendar_edit" id="calendar_edit" method="post" action="../admin/calendar_edit.php">
		<fieldset>
			<input type="hidden" name="calendar" value="<?php echo H($calendar); ?>" />

			<table class="biblio_view">
			<tr>
				<td class="name" valign="bottom"><?php echo T("Name:"); ?></td>
				<td class="value" valign="bottom"><?php echo inputfield('text', 'name', $calname, array('size'=>'32')); ?></td>
				<td class="value" valign="bottom"><input type="submit" value="<?php echo T("Save Changes"); ?>" class="button" /></td>
			</tr>
			</table>

		</fieldset>

		<fieldset>
<?php

	$d = getdate(time());
	if ($_REQUEST['month'] != "") {
		$d['mon'] = $_REQUEST['month'];
	}
	if ($d['mon'] >= 7) {
		$d['mon'] = 7;
	} else {
		$d['mon'] = 1;
	}
	if ($_REQUEST['year'] != "") {
		$d['year'] = $_REQUEST['year'];
	}
	list($start, $err) = Date::read_e($d['year'].'-'.$d['mon'].'-01');
	assert(!$err);
	$end = Date::addMonths($start, 12);
	$cal = new EditingCalendar($template, $start, $end);
	echo '<div style="padding: 4px; border-top: solid #006500 2px; border-bottom: solid #006500 2px">';
	//echo '<div style="padding: 4px; >';
	echo $cal->setStartMonth($d['mon']);
	echo $cal->getYearView($d['year']);
	echo '</div>';

?>
		</fieldset>
		<div style="padding-top: 4px; text-align: right">
			<input type="submit" value="<?php echo T("Save Changes"); ?>" class="button" />
		</div>
	</form>

<?php
	require_once("../shared/footer.php");

	 ;
