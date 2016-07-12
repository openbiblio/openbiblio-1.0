<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Queryi.php"));

/* previously known as 'SessionHandler'
 * renamed due to conflict with PHP 5.4+ class of same name - FL
 *
 *does not appear to any longer be in use - FL July 2016
 */

class OBsession {
	function __construct() {
		$this->db = new Queryi;
	}

	function open($save_path, $session_name) {
		return true;
	}
	function close() {
		return true;
	}
	function read($id) {
		$sql = $this->mkSQL("select data from php_sess where id=%Q ", $id);
		$row = $this->select01($sql);
		if ($row) {
			return $row['data'];
		}
		return "";
	}
	function write($id, $sess_data) {
		$sql = $this->mkSQL("replace into php_sess values (%Q, sysdate(), %Q) ",
			$id, $sess_data);
		$this->act($sql);
		return true;
	}
	function destroy($id) {
		$sql = $this->mkSQL("delete from php_sess where id=%Q ", $id);
		$this->act($sql);
		$this->mkSQL("delete from cart where sess_id=%Q ", $id);
		$this->act($sql);
		return true;
	}
	function gc($maxlifetime) {
		$sql = $this->mkSQL("delete from php_sess where "
			. "unix_timestamp()-unix_timestamp(last_access_dt) > %N ",
			$maxlifetime);
		$this->act($sql);
		$this->act("delete cart from cart left join php_sess "
			. "on sess_id=php_sess.id "
			. "where php_sess.id is NULL ");
		return true;
	}
}
$_session_handler = new SessionHandler();
/*
session_set_save_handler(
	 array(&$_session_handler, 'open')
	,array(&$_session_handler, 'close')
	,array(&$_session_handler, 'read')
	,array(&$_session_handler, 'write')
	,array(&$_session_handler, 'destroy')
	,array(&$_session_handler, 'gc')
	);
*/
