<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class MemberAccounts extends DBTable {
	function MemberAccounts() {
		$this->DBTable();
		$this->setName('member_account');
		$this->setFields(array(
			'transid'=>'number',
			'mbrid'=>'number',
			'create_dt'=>'string',
			'create_userid'=>'number',
			'transaction_type_cd'=>'string',
			'amount'=>'number',
			'description'=>'string',
		));
		$this->setKey('transid');
		$this->setSequenceField('transid');
		$this->setForeignKey('mbrid', 'members', 'mbrid');
	}
	function getByMbrid($mbrid) {
		return $this->getMatches(array('mbrid'=>$mbrid), 'create_dt');
	}
	function deleteByMbrid($mbrid) {
		$this->deleteMatches(array('mbrid'=>$mbrid));
	}
	function getBalance($mbrid) {
		$sql = $this->db->mkSQL("SELECT SUM(member_account.amount) balance "
			. "FROM member_account "
			. "WHERE member_account.mbrid = %N ", $mbrid);
		$row = $this->db->select1($sql);
		return $row['balance'];
	}
	function insert_el($rec, $confirmed=false) {
		$date = date('Y-m-d H:i:s');
		$rec['create_dt'] = $rec['last_change_dt'] = $date;
		$rec['create_userid'] = $_SESSION['userid'];
		if ($rec['transaction_type_cd']{0} == '-') {
			$rec['amount'] *= -1;
		}
		return parent::insert_el($rec, $confirmed);
	}
	function update_el($rec, $confirmed=false) {
		Fatal::internalError(T('Update not supported for this table'));
	}
	function validate_el($trans, $insert) {
		if (!$insert) {
			Fatal::internalError(T('Update not supported for this table'));
		}
		$errors = array();
		if (!isset($trans['description']) || trim($trans['description']) == '') {
			$errors[] = new FieldError('description', T("Required field missing"));
		}
		if (!isset($trans['amount']) || is_numeric($trans['amount'])) {
			$errors[] = new FieldError('description', T("Must be numeric"));
		}
		return $errors;
	}
}

