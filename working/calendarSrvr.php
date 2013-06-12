<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once("../classes/Calendar.php");
	require_once("../model/Calendars.php");
	//print_r($_REQUEST);echo "<br />";

	switch ($_REQUEST['mode']) {
	case 'saveCalendar':
		$calendars = new Calendars;

		if (!isset($_POST['calendar']) or !$_POST['calendar']) {
			$calendar = $calendars->insert(array('description'=>$_POST['name']));
		} else {
			$calendar = $_POST['calendar'];
			$calendars->rename($calendar, $_POST['name']);
		}

		$days = array();
		foreach ($_POST as $k => $v) {
			if (preg_match('/^input-[0-9]-([0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9])$/', $k, $m)) {
				$days[] = array($m[1], $v);
			}
		}
		$calendars->setDays($calendar, $days);
		break;

	case 'getCalendar':
		$template = $_REQUEST['calCd'];

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
		//echo '<div style="padding: 4px; border-top: solid #006500 2px; border-bottom: solid #006500 2px">';
		echo '<div style="padding: 4px;" >';
		echo $cal->setStartMonth($d['mon']);
		echo $cal->getYearView($d['year']);
		echo '</div>';
	  break;

	default:
		  echo '<h4 class="error">'.T("invalid mode")."@calendarSrvr.php: &gt;".$_REQUEST['mode']."&lt;</h4><br />";
		break;
	}

