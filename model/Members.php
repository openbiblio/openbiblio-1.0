<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../classes/Iter.php"));
require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
require_once(REL(__FILE__, "../model/MemberAccounts.php"));

class Members extends CoreTable {
	public function __construct() {
		parent::__construct();
		$this->setName('member');
		$this->setFields(array(
			'mbrid'=>'number',
			'siteid'=>'number',
			'barcode_nmbr'=>'string',
			'first_name'=>'string',
			'last_name'=>'string',
			'first_legal_name'=>'string',
			'last_legal_name'=>'string',
			'address1'=>'string',
			'address2'=>'string',
			'city'=>'string',
			'state'=>'string',
			'zip'=>'string',
			'zip_ext'=>'string',
			'home_phone'=>'string',
			'work_phone'=>'string',
			'email'=>'string',
			'classification'=>'string',
            'password'=>'string',
			'school_grade'=>'number',
			'school_teacher'=>'string',
		));
		$this->setKey('mbrid');
		$this->setSequenceField('mbrid');
		$this->setForeignKey('siteid', 'site', 'siteid');
		$this->setIter('MembersIter');

		$this->setReq(array(
			'last_name',
			'first_name',
			'home_phone',
			'classification',
		));
		if ($_SESSION['mbrBarcode_flg']=='Y') {
		  $this->reqdFlds[] = 'barcode_nmbr';
		}

		$this->custom = new MemberCustomFields;
/*       This is taken care of in .../models/MemberCustomFields
		$this->custom->setName('member_fields');
		$this->custom->setFields(array(
			'mbrid'=>'string',
			'code'=>'string',
			'data'=>'string',
		));
		$this->custom->setKey('mbrid', 'code');
		$this->custom->setForeignKey('mbrid', 'member', 'mbrid');
*/
	}
	
	function getNewBarCode($width) {
		//$sql = $this->mkSQL("select max(copyid) as lastCopy from biblio_copy");
		$sql = $this->mkSQL("select max(cast(barcode_nmbr as signed)) as lastNmbr from member");
		$mbr = $this->select1($sql);
	  if(empty($width)) $w = 13; else $w = $width;
		return sprintf("%0".$w."s",($mbr['lastNmbr']+1));
	}

	// Seems duplicate from above, so removing.
    /*
	function getNextMbr() {
		$sql = $this->mkSQL("select max(barcode_nmbr) as lastMbr from member");
		$lastMbr = $this->select1($sql);
		return $lastMbr["lastMbr"]+1;
	}
    */
	
	function getMbrByBarcode($barcd) {
		$sql = $this->mkSQL("SELECT * FROM member WHERE barcode_nmbr = %Q ", $barcd);
		return $this->select1($sql);
	}
	
	function getMbrByName($nameFrag) {
		$frag = '%'.$nameFrag.'%';
		$sql = $this->mkSQL("SELECT mbrid, barcode_nmbr, first_name, last_name, city, home_phone, work_phone, classification, siteid FROM member WHERE last_name LIKE %Q "
													 ."ORDER BY last_name", $frag);
		return $this->select($sql);
	}

	function getMbrByLegalName($nameFrag) {
		$frag = '%'.$nameFrag.'%';
		$sql = $this->mkSQL("SELECT mbrid, barcode_nmbr, first_name, last_name, first_legal_name, last_legal_name, city, home_phone, work_phone, classification, siteid FROM member "
			."WHERE (last_legal_name LIKE %Q OR last_name LIKE %Q ) "
			."AND (last_legal_name IS NOT NULL OR first_legal_name IS NOT NULL) "
			 ."ORDER BY last_legal_name", $frag, $frag);
		return $this->select($sql);
	}
	
	protected function validate_el($mbr, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
		# Check for duplicate barcodes
		if (isset($mbr['barcode_nmbr']) && ($mbr['barcode_nmbr'] != '000000')) {
			$sql = $this->mkSQL("SELECT COUNT(*) duplicates FROM member "
				. "WHERE barcode_nmbr LIKE %Q",
				$mbr['barcode_nmbr']);
			if (isset($mbr['mbrid'])) {
				$sql .= $this->mkSQL("AND mbrid <> %N ", $mbr['mbrid']);
			}
			$row = $this->select1($sql);
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
		$sql = $this->mkSQL('SELECT mbrid FROM member '
			. 'WHERE password=MD5(%Q) '
			. 'AND (barcode_nmbr=%Q or email=%Q) ',
			$password, $id, $id);
		$rslt = $this->select($sql);
		$rows = $rslt->fetchall();
		if (count($rows) != 1) {
			return NULL;
		}
//		$r = $rows->fetch_assoc();
//		return $r['mbrid'];
		return $rows['mbrid'];
	}
	function insert_el($mbr, $confirmed=false) {
		//echo "in Members::insert_el(); ";
		// this mechanism present in DBtable::validate()
		//foreach ($this->reqdFlds as $field) {
		//	if (!isset($mbr[$field])) {return new OBErr('Required fields missing: '.$field);}
		//}
		if (isset($mbr['password'])) {
			if (strlen($mbr['password']) < 4 && !$_SESSION["hasCircAuth"]) {
				return new FieldError('password', T("Password at least 4 chars"));
			}
			if (!isset($mbr['confirm-pw']) or $mbr['confirm-pw'] != $mbr['password']) {
				return new FieldError('password', T("Supplied passwords do not match"));
			}
			$mbr['password'] = md5($mbr['password']);
		} else {
            $mbr['password'] = md5($this->generatePassword(8));
        }
		$results = parent::insert_el($mbr, $confirmed);
		// Check to make sure insert went through
        // LJ: Not exactly sure, this the line below was meant for an other restuls set, but $this->insert_id seems empty...
		//if (0 == $this->insert_id) {
		//print_r($results);echo "<br />\n";
        if (NULL == $results[0]) {
			return new OBErr(T('Member creation was not successful'));
		} else {
			return $results;
		}
	}
    function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@�$%^&*()';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }
	function update_el($mbr, $confirmed=false) {
		if (isset($mbr['password']) and $mbr['password']) {
			if ($mbr['password'] == '*encrypted*') {
				unset($mbr['password']);
			} else if (strlen($mbr['password']) < 4 && !$_SESSION["hasCircAuth"]) {
				return array(NULL, array(new FieldError('password', T("Password at least 4 chars"))));
			} else if (!isset($mbr['confirm-pw']) or $mbr['confirm-pw'] != $mbr['password']) {
				return array(new FieldError('password', T("Supplied passwords do not match")));
			} else {
				$mbr['password'] = md5($mbr['password']);
			}
		}
		return parent::update_el($mbr, $confirmed);
	}
	function getCalendarId($mbrid) {
		$sql = $this->mkSQL("SELECT calendar FROM site, member "
			. "WHERE site.siteid=member.siteid AND mbrid=%N ",
			$mbrid);
		$row = $this->select1($sql);
		return $row['calendar'];
	}
	function deleteOne() {
		# FIXME - history
		$mbrid = func_get_args()[0];
		$this->custom->deleteMatches(array('mbrid'=>$mbrid));
		$acct = new MemberAccounts;
		$acct->deleteByMbrid($mbrid);
		return parent::deleteOne($mbrid);
	}
	function deleteMatches($fields) {
		$this->lock();
		$rows = $this->getMatches($fields);
		while (($row = $rows->fetch_assoc()) !== NULL) {
			$this->deleteOne($row['mbrid']);
		}
		$this->unlock();
	}

	/* ---------------------------- */
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
//		$this->custom->deleteMatches(array('mbrid'=>$mbrid));
		$this->deleteMatches(array('mbrid'=>$mbrid));
	}
}
class MembersIter extends Iter {
	public function __construct($rows) {
		parent::__construct();
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
