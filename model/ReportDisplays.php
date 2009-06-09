<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class ReportDisplaysIter extends Iter {
	function ReportDisplaysIter($rows) {
		$this->rows = $rows;
	}
	function next() {
		$row = $this->rows->next();
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
	function ReportDisplays() {
		$this->DBTable();
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
	}
	function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('page', 'position', 'report', 'title', 'max_rows', 'params') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] === '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		foreach (array('position', 'max_rows') as $num) {
			if (isset($rec[$num]) and !is_numeric($rec[$num])
					or $rec[$num] <= 0) {
				$errors[] = new FieldError($num, T("Field must be greater than zero"));
			}
		}
		return $errors;
	}
	function getOne($page, $position) {
		$row = parent::getOne($page, $position);
		if (!$row)
			return NULL;
		$row['params'] = unserialize($row['params']);
		return $row;
	}
	function getAll() {
		$rows = parent::getAll();
		return new ReportDisplaysIter($rows);
	}
	function getMatches($fields) {
		$rows = parent::getMatches($fields);
		return new ReportDisplaysIter($rows);
	}
	function insert_el($rec) {
		if (isset($rec['params']))
			$rec['params'] = serialize($rec['params']);
		return parent::insert_el($rec);
	}
	function update_el($rec) {
		if (isset($rec['params']))
			$rec['params'] = serialize($rec['params']);
		return parent::update_el($rec);
	}
}
