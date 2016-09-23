<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once("../classes/Calendar.php");
	require_once("../model/Calendars.php");
	//print_r($_REQUEST);echo "<br />";

	function doSetDays($ptr, $cal) {
		$days = array();
		foreach ($_POST as $k => $v) {
			if (preg_match('/^IN-[0-9]-([0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9])$/', $k, $m)) {
				$days[] = array($m[1], $v);
			}
		}
		$ptr->setDays($cal, $days);
	}

	$ptr = new Calendars;
	switch ($_REQUEST['mode']) {
	case "isOpen":
		if($ptr->isOpen($_GET['date'])){
		    echo T("Yes");
        } else {
            echo T("No");
        }
		break;

	case "deleteCalendar":
		if ($_REQUEST["calendar"] != OBIB_MASTER_CALENDAR) {
			$ptr->deleteOne($_REQUEST["calendar"]);
			$msg = T("CalendarDeleted");
		} else {
			$msg = T("CannotDeleteMasterCalendar");
		}
		echo $msg;
		break;

	case 'makeNewCalendar':
		$calendar = $ptr->insert(array('description'=>$_POST['name']));
		doSetDays($ptr, $calendar);
		echo "Created New Calendar '".$_POST['name']."'";
		break;

	case 'saveCalendar':
		$calendar = $_POST['calendar'];
		$ptr->rename($calendar, $_POST['name']);
		doSetDays($ptr, $calendar);
		echo "Successfully updated Calendar '".$_POST['name']."'";
		break;

	case 'getCalendar':
		$template = $_REQUEST['calendar'];

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
		echo '<div style="padding: 4px;" >';
		echo $cal->setStartMonth($d['mon']);
		echo $cal->getYearView($d['year']);
		echo '</div>';
	  break;

	default:
		  echo '<h4 class="error">'.T("invalid mode")."@calendarSrvr.php: &gt;".$_REQUEST['mode']."&lt;</h4><br />";
		break;
	}

