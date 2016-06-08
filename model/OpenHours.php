<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class OpenHours extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('open_hours');
		$this->setFields(array(
			'hourid'=>'number',
			'siteid'=>'number',
			'day'=>'number',
			'start_time'=>'number',
			'end_time'=>'number',
			'by_appointment'=>'bool',
			'public_note'=>'string',
			'private_note'=>'string',
		));
		$this->setKey('hourid');
	}
}
