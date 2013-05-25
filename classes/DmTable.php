<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class DmTable extends DBTable {
	function DmTable() {
		$this->DBTable();
	}
	function getList() {
		$list = array();
		$recs = $this->getAll('description');
		while ($rec = $recs->next()) {
			$list[$rec['code']] = $rec['description'];
		}
		return $list;
	}
	function getSelect($all=false) {
		$select = $this->getList();
		if ($all) {
			$select['all'] = 'All';
		}
/*
		$recs = $this->getAll('description');
		while ($rec = $recs->next()) {
			$select[$rec['code']] = $rec['description'];
		}
*/
		return $select;
	}
	function getDefault() {
		$recs = $this->getMatches(array('default_flg'=>'Y'));
		if ($recs->count() != 1) {
			return NULL;
		} else {
			$r = $recs->next();
			return $r['code'];
		}
	}
}
