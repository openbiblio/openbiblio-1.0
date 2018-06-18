<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class MemberCustomFields extends DBTable {
	public function __construct () {
		parent::__construct();
		$this->setName('member_fields');
		$this->setFields(array(
			'mbrid'=>'number',
			'code'=>'string',
			'data'=>'string',
		));
        $this->setReq(array(
            'mbrid', 'code', 'data',
        ));
		$this->setKey('mbrid', 'code');
	}
	protected function validate_el($rec, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
		return $errors;
	}

}
