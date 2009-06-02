<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once(REL(__FILE__, "../classes/Query.php"));

  class SessionHandler extends Query {
    function open($save_path, $session_name) {
    }
    function read($id) {
      $sql = $this->mkSQL("select data from php_sess where id=%Q ", $id);
      $l = $this->exec($sql);
      if (count($l)) {
        return $l[0]['data'];
      }
      return "";
    }
    function write($id, $sess_data) {
      $sql = $this->mkSQL("replace into php_sess values (%Q, sysdate(), %Q) ",
                          $id, $sess_data);
      return $this->_query($sql, "Can't write session data");
    }
    function destroy($id) {
      $sql = $this->mkSQL("delete from php_sess where id=%Q ", $id);
      if (!$this->_query($sql, "Can't destroy session data.")) {
        return false;
      }
      $sql = $this->mkSQL("delete from cart where sess_id=%Q ", $id);
      return $this->_query($sql, "Can't destroy session request cart.");
    }
    function gc($maxlifetime) {
      $sql = $this->mkSQL("delete from php_sess where "
                          . "unix_timestamp()-unix_timestamp(last_access_dt) > %N ",
                          $maxlifetime);
      $this->exec($sql);
      $this->exec("delete cart from cart left join php_sess "
                  . "on sess_id=php_sess.id "
                  . "where php_sess.id is NULL ");
      return true;
    }
  }
  $_session_handler = new SessionHandler();
  session_set_save_handler(
    array(&$_session_handler, 'open'),
    array(&$_session_handler, 'close'),
    array(&$_session_handler, 'read'),
    array(&$_session_handler, 'write'),
    array(&$_session_handler, 'destroy'),
    array(&$_session_handler, 'gc'));
