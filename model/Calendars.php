<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));
require_once(REL(__FILE__, "../classes/Date.php"));

class Calendars extends DmTable {
  function Calendars() {
    $this->DmTable();
    $this->setName('calendar_dm');
    $this->setFields(array(
      'code'=>'string',
      'description'=>'string',
      'default_flg'=>'string',
    ));
    $this->setKey('code');
    $this->setSequenceField('code');
  }
  function rename($code, $name) {
    $this->update(array('code'=>$code, 'description'=>$name));
  }
  function deleteOne($code) {
    if ($code == OBIB_MASTER_CALENDAR)
      Fatal::internalError(T("Cannot Delete Master Calendar"));
    $this->db->lock();
    $sql = $this->db->mkSQL('DELETE FROM calendar_dm WHERE code=%N', $code);
    $this->db->act($sql);
    $sql = $this->db->mkSQL('DELETE FROM calendar WHERE calendar=%N', $code);
    $this->db->act($sql);
    $this->db->unlock();
  }
  function extend($calendar, $from, $to) {
    $this->db->lock();
    $sql = $this->db->mkSQL('SELECT MAX(date) max, MIN(date) min FROM calendar '
                            . 'WHERE calendar=%N GROUP BY calendar ',
                            $calendar);
    $row = $this->db->select01($sql);
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
    $this->db->unlock();
  }
  function _createDays($calendar, $from, $to) {
    if (Date::daysLater($to, $from) < 0) {
      Fatal::internalError(T('CalendarsLaterDate'));
    }
    foreach (Date::getDays($from, $to) as $d) {
      $sql = $this->db->mkSQL("INSERT INTO calendar SET "
                              . "calendar=%N, date=%Q, open='Unset' ",
                              $calendar, $d);
      $this->db->act($sql);
    }
  }
  function getDays($calendar, $from, $to) {
    $this->extend($calendar, $from, $to);
    $sql = $this->db->mkSQL('SELECT date, open FROM calendar '
                            . 'WHERE calendar=%N AND date >= %Q '
                            . 'AND date <= %Q ',
                            $calendar, $from, $to);
    return $this->db->select($sql);
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
      $sql .= $this->db->mkSQL('(%N, %Q, %Q), ',
                               $calendar, $date,
                               $open);
    }
    # Remove trailing ', '
    $sql = substr($sql, 0, -2);
    # Be sure we can't have gaps in the calendar.
    $this->extend($calendar, $min, $max);
    $this->db->lock();
    $this->db->act($sql);
    $this->db->unlock();
  }
}
