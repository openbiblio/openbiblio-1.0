<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * provides common DB facilities needed by most primary table classes
 * @author Micah Stetson
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class DmTable extends DBTable {
	public function __construct() {
		parent::__construct();
	}

	public function getList() {
		$list = array();
		$recs = $this->getAll();
		//while ($rec = $recs->fetch_assoc()) {
		while ($rec = $recs->fetch_assoc()) {
			$list[$rec['code']] = $rec['description'];
		}
		return $list;
	}
	public function getSelect($all=false) {
		$select = $this->getList();
		if ($all) {
			$select['all'] = 'All';
		}
		return $select;
	}
	public function getDefault() {
		$recs = $this->getMatches(array('default_flg'=>'Y'));
		if ($recs->num_rows != 1) {
			return NULL;
		} else {
			$r = $recs->fetch_assoc();
			return $r['code'];
		}
	}

	protected function validate_el($rec, $insert) { /*return array();*/ }
}
