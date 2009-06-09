<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class MemberCustomFields extends DmTable {
	function MemberCustomFields() {
		$this->DmTable();
		$this->setName('member_fields_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
	}
	function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('description', 'code') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		return $errors;
	}
}
