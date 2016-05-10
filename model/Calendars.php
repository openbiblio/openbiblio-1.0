<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));
require_once(REL(__FILE__, "../classes/Date.php"));

class Calendars extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('calendar_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
		$this->setSequenceField('code');
	}
	function isOpen($calendar, $day) {
		$sql = $this->mkSQL('SELECT open FROM calendar '
			. 'WHERE calendar=%N AND date = %Q ',
			$calendar, $day);
		return $this->select1($sql);
	}
	function rename($code, $name) {
		$this->update(array('code'=>$code, 'description'=>$name));
	}
	function deleteOne() {
		$code = func_get_args(0);
		if ($code == OBIB_MASTER_CALENDAR)
			Fatal::internalError(T("CannotDeleteMasterCalendar"));
		$this->lock();
		$sql = $this->mkSQL('DELETE FROM calendar_dm WHERE code=%N', $code);
		$this->act($sql);
		$sql = $this->mkSQL('DELETE FROM calendar WHERE calendar=%N', $code);
		$this->act($sql);
		$this->unlock();
	}
	function extend($calendar, $from, $to) {
		$this->lock();
		$sql = $this->mkSQL('SELECT MAX(date) max, MIN(date) min FROM calendar '
			. 'WHERE calendar=%N GROUP BY calendar ',
			$calendar);
		$row = $this->select01($sql);
		if (!$row) {
			$this->_createDays($calendar, $from, $to);
		} else {
			$min = $row['min'];
			$max = $row['max'];
			if (Date::daysLater($min, $from)) {
				$this->_createDays($calendar, $from, Date::addDays($min, -1));
			}
			if (Date::daysLater($to, $max)) {
				$this->_createDays($calendar, Date::addDays($max, 1), $to);
			}
		}
		$this->unlock();
	}
	function _createDays($calendar, $from, $to) {
		if (Date::daysLater($to, $from) < 0) {
			Fatal::internalError(T("CalendarsLaterDate"));
		}
		foreach (Date::getDays($from, $to) as $d) {
			$sql = $this->mkSQL("INSERT INTO calendar SET "
//				. "calendar=%N, date=%Q, open='Unset' ",
				. "calendar=%N, date=%Q, open='Yes' ",
				$calendar, $d);
			$this->act($sql);
		}
	}
	function getDays($calendar, $from, $to) {
		$this->extend($calendar, $from, $to);
		$sql = $this->mkSQL('SELECT date, open FROM calendar '
			. 'WHERE calendar=%N AND date >= %Q '
			. 'AND date <= %Q ',
			$calendar, $from, $to);
		//echo "sql=$sql<br />";
		return $this->select($sql);
	}
	function setDays($calendar, $days) {
		if (empty($days)) {
			return;
		}
		$min = '9999-99-99';
		$max = '0000-00-00';
		$sql = 'REPLACE INTO calendar VALUES ';
		foreach ($days as $d) {
			list($date, $open) = $d;
			if ($date < $min) {
				$min = $date;
			}
			if ($date > $max) {
				$max = $date;
			}
			$sql .= $this->mkSQL('(%N, %Q, %Q), ',
				$calendar, $date, $open);
		}
		# Remove trailing ', '
		$sql = substr($sql, 0, -2);
		# Be sure we can't have gaps in the calendar.
		$this->extend($calendar, $min, $max);
		$this->lock();
		$this->act($sql);
		$this->unlock();
	}
}
