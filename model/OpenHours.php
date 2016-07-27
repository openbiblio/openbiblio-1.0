<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class OpenHours extends DBTable {
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

	protected function validate_el($rec, $insert) {
                $errors = array();
        // check for missing entries
                foreach ($this->reqFields as $req) {
                        if ($insert and !isset($rec[$req])
                                        or isset($rec[$req]) and $rec[$req] == '') {
                                $errors[] = new FieldError($req, T("Required field missing"));
                        }
                }
                return $errors;
	}
}
