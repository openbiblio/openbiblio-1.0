<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Iter.php"));
require_once(REL(__FILE__, "../classes/DBTable.php"));

class ReportDisplaysIter extends Iter {
	public function __construct($rows) {
		$this->rows = $rows;
	}
	function next() {
		//$row = $this->rows->fetch_assoc();
		$row = $this->rows->fetchAll();
		if (!$row)
			return NULL;
		$row['params'] = unserialize($row['params']);
		return $row;
	}
	function skip() {
		$this->rows->skip();
	}
	function count() {
		return $this->rows->count();
	}
}

class ReportDisplays extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('report_displays');
		$this->setFields(array(
			'page'=>'string',
			'position'=>'number',
			'report'=>'string',
			'title'=>'string',
			'max_rows'=>'number',
			'params'=>'string',
		));
		$this->setKey('page', 'position');
        $this->setReq(array(
            'page', 'position', 'report', 'title', 'max_rows', 'params',
        ));
	}
	function validate_el($rec, $insert) {
		$errors = array();
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
		foreach (array('position', 'max_rows') as $num) {
			if (isset($rec[$num]) and !is_numeric($rec[$num])
					or $rec[$num] <= 0) {
				$errors[] = new FieldError($num, T("Field must be greater than zero"));
			}
		}
		return $errors;
	}
	function getOne() {
		$page = func_get_arg(0);
		$position = func_get_arg(1);
		$row = parent::getOne($page, $position);
		if (!$row)
			return NULL;
		$row['params'] = unserialize($row['params']);
		return $row;
	}
	function getAll($order_by = NULL) {
		$rows = parent::getAll($order_by);
		return new ReportDisplaysIter($rows);
	}
	function getMatches($fields, $order_by = NULL) {
		$rows = parent::getMatches($fields, $order_by);
		return new ReportDisplaysIter($rows);
	}
	function insert_el($rec, $confirmed = false) {
		if (isset($rec['params']))
			$rec['params'] = serialize($rec['params']);
		return parent::insert_el($rec, $confirmed);
	}
	function update_el($rec, $confirmed = false) {
		if (isset($rec['params']))
			$rec['params'] = serialize($rec['params']);
		return parent::update_el($rec, $confirmed);
	}
}
