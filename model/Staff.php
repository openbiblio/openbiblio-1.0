<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));

class Staff extends CoreTable {
	function Staff() {
		$this->DBTable();
		$this->setName('staff');
		# if you add to this array, check with array in ../circ/staff_new_form.php
		$this->setFields(array(
			'userid'=>'number',
			'username'=>'string',
			'pwd'=>'string',
			'last_name'=>'string',
			'first_name'=>'string',
			'suspended_flg'=>'string',
			'admin_flg'=>'string',
			'tools_flg'=>'string',
			'circ_flg'=>'string',
			'circ_mbr_flg'=>'string',
			'catalog_flg'=>'string',
			'reports_flg'=>'string',
		));
		$this->setKey('userid');
		$this->setSequenceField('userid');
	}
	function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('username', 'last_name') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		if (isset($rec['pwd'])) {
			if (!isset($rec['pwd2']) or $rec['pwd'] != $rec['pwd2']) {
				$errors[] = new FieldError('pwd', T("Supplied passwords do not match"));
			}
		}
		if (isset($rec['username'])) {
			$sql = $this->db->mkSQL("SELECT * FROM staff WHERE username=%Q ", $rec['username']);
			if (isset($rec['userid'])) {
				$sql .= $this->db->mkSQL("AND userid <> %N ", $rec['userid']);
			}
			$rows = $this->db->select($sql);
			if ($rows->count() != 0) {
				$errors[] = new FieldError('username', T("Username already taken by another user."));
			}
		}
		return $errors;
	}
	function insert_el($rec, $confirmed=false) {
		if(isset($rec['pwd'])) {
			$rec['pwd'] = md5($rec['pwd']);
		}
		if(isset($rec['pwd2'])) {
			$rec['pwd2'] = md5($rec['pwd2']);
		}
		return parent::insert_el($rec, $confirmed);
	}
	function update_el($rec, $confirmed=false) {
		if (isset($rec['pwd']) && isset($rec['pwd2']) && ($rec['pwd'] == $rec['pwd2']) ) {
			$rec['pwd'] = md5($rec['pwd']);	
			$rec['pwd2'] = md5($rec['pwd2']);
			return parent::update_el($rec, $confirmed);
		} else {
			return "invalid password";
		}
	}
}
