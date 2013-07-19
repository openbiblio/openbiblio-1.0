<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class Holds extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('biblio_hold');
		$this->setFields(array(
			'bibid'=>'number',
			'copyid'=>'number',
			'holdid'=>'number',
			'hold_begin_dt'=>'string',
			'mbrid'=>'number',
		));
		$this->setKey('holdid');
		$this->setSequenceField('holdid');
		$this->setForeignKey('bibid', 'biblio', 'bibid');
		$this->setForeignKey('copyid', 'biblio_copy', 'copyid');
		$this->setForeignKey('mbrid', 'member', 'mbrid');
	}
	function getFirstHold($copyid) {
		$sql = "SELECT * FROM biblio_hold "
					 . $this->mkSQL("WHERE copyid=%N ", $copyid)
					 . "ORDER BY hold_begin_dt LIMIT 1 ";
		return $this->select01($sql);
	}
	function insert_el($rec, $confirmed=false) {
		$rec['hold_begin_dt'] = date('Y-m-d H:i:s');
		return parent::insert_el($rec, $confirmed);
	}
	function _cleanup() {
		include_once(REL(__FILE__, "../model/History.php"));
		# Select copies with an 'on hold' status that have no hold records.
		$sql = "SELECT c.* FROM biblio_copy c "
					 . "JOIN biblio_status_hist h "
					 . "LEFT JOIN biblio_hold bh "
					 . "ON bh.copyid=c.copyid "
					 . "WHERE h.histid=c.histid "
					 . "AND h.status_cd='hld' "
					 . "AND bh.copyid IS NULL ";
		$copies = $this->select($sql);
		$history = new History;
		while ($copy = $copies->fetch_assoc()) {
			$history->insert(array(
				'bibid'=>$copy['bibid'],
				'copyid'=>$copy['copyid'],
				'status_cd'=>'in',
			));
		}
	}
	function deleteOne($holdid) {
		parent::deleteOne($holdid);
		$this->_cleanup();
	}
	function deleteMatches($fields) {
		parent::deleteMatches($fields);
		$this->_cleanup();
	}
}
