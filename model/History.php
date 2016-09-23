<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));

/**
 * This class provides an interface to the Biblio_status_hist table
 * @author Micah Stetson
 */

class History extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('biblio_status_hist');
		$this->setFields(array(
			'histid'=>'number',
			'bibid'=>'number',
			'copyid'=>'number',
			'status_cd'=>'string',
			'status_begin_dt'=>'string',
		));
		$this->setKey('histid');
		$this->setSequenceField('histid');
		$this->setForeignKey('bibid', 'biblio', 'bibid');
		$this->setForeignKey('copyid', 'biblio_copy', 'copyid');
	}
	public function update_el($rec, $confirmed=false) {
		Fatal::internalError(T("Cannot update history entries"));
	}
	public function insert_el($rec, $confirmed=false) {
		$rec['status_begin_dt'] = date('Y-m-d H:i:s');
		$this->lock();
		list($id, $errs) = parent::insert_el($rec, $confirmed);
		$this->unlock();
		return array($id, $errs);
	}

	protected function validate_el($new, $insert) {

	}
}
