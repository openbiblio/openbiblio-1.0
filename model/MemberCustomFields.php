<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));
require_once(REL(__FILE__, "../classes/DmTable.php"));

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
            'code', 'mbrid', 'data',
        ));
		$this->setKey('code');
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
        //echo "in MemberCustomFields::validate_el(): ";print_r($this->reqFields);echo "<br />\n";
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
