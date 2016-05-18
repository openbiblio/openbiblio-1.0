<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../classes/DBTable.php"));

/**
 * Adds common date-time stamps to child primary table classes
 * @author Micah Stetson
 */

abstract class CoreTable extends DBTable {
	public function __construct($dbConst) {
        $this->dbConst = $dbConst;
		parent::__construct($this->dbConst);
	}

	protected function validate_el($rec, $insert) { /* abstracted in DBTable */ }

	protected function setFields($fields) {
		$common = array(
			'create_dt'=>'string',
			'last_change_dt'=>'string',
			'last_change_userid'=>'number',
			#'delete_dt'=>'string',
		);
		parent::setFields(array_merge($common, $fields));
	}
	public function insert_el($rec, $confirmed=false) {
		$date = date('Y-m-d H:i:s');
		$rec['create_dt'] = $rec['last_change_dt'] = $date;
		$rec['last_change_userid'] = $_SESSION['userid'];
		return parent::insert_el($rec, $confirmed);
	}
	public function update_el($rec, $confirmed=false) {
		$date = date('Y-m-d H:i:s');
		$rec['last_change_dt'] = $date;
		$rec['last_change_userid'] = $_SESSION['userid'];
		return parent::update_el($rec, $confirmed);
	}
}
