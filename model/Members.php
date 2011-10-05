<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
require_once(REL(__FILE__, "../model/MemberAccounts.php"));

class Members extends CoreTable {
	function Members() {
		$this->DBTable();
		$this->setName('member');
		$this->setFields(array(
			'mbrid'=>'number',
			'siteid'=>'number',
			'barcode_nmbr'=>'string',
			'last_name'=>'string',
			'first_name'=>'string',
			'address1'=>'string',
			'address2'=>'string',
			'city'=>'string',
			'state'=>'string',
			'zip'=>'string',
			'zip_ext'=>'string',
			'home_phone'=>'string',
			'work_phone'=>'string',
			'email'=>'string',
//			'password'=>'string',
			'classification'=>'string',
//			'school_grade'=>'string',
//			'school_teacher'=>'string',
		));
		$this->setKey('mbrid');
		$this->setSequenceField('mbrid');
		$this->setForeignKey('siteid', 'site', 'siteid');
		$this->setIter('MembersIter');

		$this->custom = new DBTable;
		$this->custom->setName('member_fields');
		$this->custom->setFields(array(
			'mbrid'=>'string',
			'code'=>'string',
			'data'=>'string',
		));
		$this->reqdFlds = array(
			'last_name',
			'first_name',
			'home_phone',
		);
		if ($_SESSION[mbrBarcode_flg]=='Y') {
		  $this->reqdFlds[] = 'barcode_nmbr';
		}
		$this->custom->setKey('mbrid', 'code');
		$this->custom->setForeignKey('mbrid', 'member', 'mbrid');
	}
	
	function getNextMbr() {
		$sql = $this->db->mkSQL("select max(barcode_nmbr) as lastMbr from member");
		$lastMbr = $this->db->select1($sql);
		return $lastMbr["lastMbr"]+1;
	}
	
	function getMbrByBarcode($barcd) {
		$sql = $this->db->mkSQL("SELECT * FROM member WHERE barcode_nmbr = %Q ", $barcd);
		return $this->db->select1($sql);
	}
	
	function validate_el($mbr, $insert) {
		$errors = array();
		foreach ($this->reqdFlds as $req) {
			if ($insert and !isset($mbr[$req])
					or isset($mbr[$req]) and $mbr[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		# Check for duplicate barcodes
		if (isset($mbr['barcode_nmbr']) && ($mbr['barcode_nmbr'] != '000000')) {
			$sql = $this->db->mkSQL("SELECT COUNT(*) duplicates FROM member "
				. "WHERE barcode_nmbr = %Q ",
				$mbr['barcode_nmbr']);
			if (isset($mbr['mbrid'])) {
				$sql .= $this->db->mkSQL("AND mbrid <> %N ", $mbr['mbrid']);
			}
			$row = $this->db->select1($sql);
			if ($row['duplicates'] != 0) {
				$errors[] = new FieldError('barcode_nmbr', T("Barcode number in use."));
			}
		}
		return $errors;
	}
	function loginMbrid($id, $password) {
		if (!$id or !$password) {
			return NULL;
		}
		$sql = $this->db->mkSQL('SELECT mbrid FROM member '
			. 'WHERE password=MD5(%Q) '
			. 'AND (barcode_nmbr=%Q or email=%Q) ',
			$password, $id, $id);
		$rows = $this->db->select($sql);
		if ($rows->count() != 1) {
			return NULL;
		}
		$r = $rows->next();
		return $r['mbrid'];
	}
	function insert_el($mbr, $confirmed=false) {
		if (isset($mbr['password'])) {
			if (strlen($mbr['password']) < 4 && !$_SESSION["hasCircAuth"]) {
				return array(NULL, array(new FieldError('password', T("Password at least 4 chars"))));
			}
			if (!isset($mbr['confirm-pw']) or $mbr['confirm-pw'] != $mbr['password']) {
				return array(NULL, array(new FieldError('password', T("Supplied passwords do not match."))));
			}
			$mbr['password'] = md5($mbr['password']);
		}
		return parent::insert_el($mbr, $confirmed);
	}
	function update_el($mbr, $confirmed=false) {
		if (isset($mbr['password']) and $mbr['password']) {
			if ($mbr['password'] == '*encrypted*') {
				unset($mbr['password']);
			} else if (strlen($mbr['password']) < 4 && !$_SESSION["hasCircAuth"]) {
				return array(NULL, array(new FieldError('password', T("Password at least 4 chars"))));
			} else if (!isset($mbr['confirm-pw']) or $mbr['confirm-pw'] != $mbr['password']) {
				return array(new FieldError('password', T("Supplied passwords do not match.")));
			} else {
				$mbr['password'] = md5($mbr['password']);
			}
		}
		return parent::update_el($mbr, $confirmed);
	}
	function getCalendarId($mbrid) {
		$sql = $this->db->mkSQL("SELECT calendar FROM site, member "
			. "WHERE site.siteid=member.siteid AND mbrid=%N ",
			$mbrid);
		$row = $this->db->select1($sql);
		return $row['calendar'];
	}
	function deleteOne($mbrid) {
		# FIXME - history
		$this->custom->deleteMatches(array('mbrid'=>$mbrid));
		$acct = new MemberAccounts;
		$acct->deleteByMbrid($mbrid);
		parent::deleteOne($mbrid);
	}
	function deleteMatches($fields) {
		$this->db->lock();
		$rows = $this->getMatches($fields);
		while (($row = $rows->next()) !== NULL) {
			$this->deleteOne($row['mbrid']);
		}
		$this->db->unlock();
	}
	function getCustomFields($mbrid) {
		return $this->custom->getMatches(array('mbrid'=>$mbrid));
	}
	function setCustomFields($mbrid, $customFldsarr) {
		$this->deleteCustomFields($mbrid);
		foreach ($customFldsarr as $code => $data) {
			$fields= array(
				mbrid=>$mbrid ,
				code=>$code,
				data=>$data
			);
			$this->custom->insert($fields);
		}
	}
	function deleteCustomFields($mbrid) {
		$this->custom->deleteMatches(array('mbrid'=>$mbrid));
	}
}
class MembersIter extends Iter {
	function MembersIter($rows) {
		$this->rows = $rows;
	}
	function next() {
		$row = $this->rows->next();
		if (!$row)
			return NULL;
		if ($row['password']) {
			$row['password'] = '*encrypted*';
		}
		return $row;
	}
	function skip() {
		$this->rows->skip();
	}
	function count() {
		return $this->rows->count();
	}
}
