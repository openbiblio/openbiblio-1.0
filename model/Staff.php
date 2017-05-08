<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));

class Staff extends CoreTable {
	public function __construct() {
		parent::__construct();
		$this->setName('staff');
		# if you add to this array, check with array in ../circ/staff_new_form.php
		$this->setFields(array(
			'userid'=>'number',
			'username'=>'string',
			'pwd'=>'string',
			'last_name'=>'string',
			'first_name'=>'string',
			'secret_key'=>'string',
			'suspended_flg'=>'string',
			'admin_flg'=>'string',
			'tools_flg'=>'string',
			'circ_flg'=>'string',
			'circ_mbr_flg'=>'string',
			'catalog_flg'=>'string',
			'reports_flg'=>'string',
			'start_page'=>'string',
		));
    $this->setReq(array(
        'username', 'pwd', 'last_name', 'secret_key', 'suspended_flg', 'admin_flg', 'tools_flg', 'circ_flg', 'circ_mbr_flg', 'catalog_flg', 'reports_flg', 'start_page',
    ));
		$this->setKey('userid');
		$this->setSequenceField('userid');
	}

	public function getStartPage($id) {
		$rslt = $this->getOne($id);
 		//echo "in Staff::getStartPage(): rslt=";print_r($rslt);echo "<br /> \n";
		$userData = $rslt->fetch();
 		//echo "in Staff::getStartPage(): userData";print_r($userData);echo "<br /> \n";
		return $userData['start_page'];
	}

	protected function validate_el($rec, $insert) {
		$errors = array();
        // all required fields present?
		foreach ($this->reqFields as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				//$errors[] = new FieldError($req, T("Required field missing"));
				$errors[] = array('NULL', T("Required field missing").": ".$req);
			}
		}
        $nFlg = 0;
		foreach (array('admin','circ','circ_mbr','catalog','reports','tools') as $flg) {
			if (isset($rec[$flg.'_flg']) && ($rec[$flg.'_flg'] == 'Y')) {
                $nFlg++;
			} else {
				$rec[$flg.'_flg'] = 'N';
			}
		}
        if ($nFlg < 1) {
            $errors[] = array('NULL', T("Role MUST be selected"));
        }
        // login credentials
		if (isset($rec['pwd'])) {
			if (!isset($rec['pwd2']) or ($rec['pwd'] != $rec['pwd2']) ) {
				//$errors[] = new FieldError('pwd', T("Supplied passwords do not match"));
				$errors[] = array('NULL', T("Supplied passwords do not match"));
			}
		}
		if (isset($rec['username'])) {
			$sql = $this->mkSQL("SELECT 1 FROM staff WHERE username=%Q ", $rec['username']);
			if (isset($rec['userid'])) {
				$sql .= $this->mkSQL("AND userid <> %N ", $rec['userid']);
			}
			$rows = $this->select($sql);
			if ($rows->fetchColumn()) {
				//$errors[] = new FieldError('username', T("Username already taken by another user"));
				$errors[] = array('NULL', T("Username already taken by another user"));
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
		if(!isset($rec['secret_key'])) {
			$rec['secret_key'] = md5(time());
		}
		unset($rec['userid']);
		$rslt = parent::insert_el($rec, $confirmed);
        return $rslt;
	}
	function update_el($rec, $confirmed=false) {
		return parent::update_el($rec, $confirmed); // will call above validate_el()
	}
}
