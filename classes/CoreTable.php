<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../classes/DBTable.php"));

class CoreTable extends DBTable {
	function CoreTable() {
		$this->DBTable();
	}
	function setFields($fields) {
		$common = array(
			'create_dt'=>'string',
			'last_change_dt'=>'string',
			'last_change_userid'=>'number',
			#'delete_dt'=>'string',
		);
		parent::setFields(array_merge($common, $fields));
	}
	function insert_el($rec, $confirmed=false) {
		$date = date('Y-m-d H:i:s');
		$rec['create_dt'] = $rec['last_change_dt'] = $date;
		$rec['last_change_userid'] = $_SESSION['userid'];
		return parent::insert_el($rec, $confirmed);
	}
	function update_el($rec, $confirmed=false) {
		$date = date('Y-m-d H:i:s');
		$rec['last_change_dt'] = $date;
		$rec['last_change_userid'] = $_SESSION['userid'];
		return parent::update_el($rec, $confirmed);
	}
}
