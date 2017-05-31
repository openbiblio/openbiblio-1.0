<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * provides common DB facilities needed by look-up table classes
 * @author Micah Stetson
 * modified for PHP 5 - FL
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/DBTable.php"));

class DmTable extends DBTable {
	public function __construct() {
		parent::__construct();
	}

	public function getList($orderby=NULL) {
		$list = array();
		$rslt = $this->getAll($orderby);
        $recs = $rslt->fetchAll();
        // echo "in DmTable::getList(): ";print_r($recs);echo "<br />\n";
        $nRecs = count($recs);
        if ($nRecs < 1) return NULL;
        foreach ($recs as $rec) {
			$list[$rec['code']] = $rec['description'];
		}
		return $list;
	}
	public function getSelectList($orderby=NULL) {
		$list = array();
        $data = array();
		$rslt = $this->getAll($orderby);
        $recs = $rslt->fetchAll();
        // echo "in DmTable::getList(): ";print_r($recs);echo "<br />\n";
        $nRecs = count($recs);
        if ($nRecs < 1) return NULL;
        foreach ($recs as $rec) {
            $data['description'] =$rec['description'];
            $data['default'] = $rec['default_flg'];
			$list[$rec['code']] = $data;
		}
		return $list;
	}
	public function getSelect($all=false) {
		$select = $this->getList();
		if ($all) {
			$select['all'] = 'All';
		}
		return $select;
	}
	public function getDefault() {
		$rslt = $this->getMatches(array('default_flg'=>'Y'));
        $recs = $rslt->fetchAll();
        $nRecs = count($recs);
        //echo "nRecs = $nRecs : ";print_r($recs);echo "<br />\n";
		if ($nRecs < 1) {
			return NULL;
		} else {
			//$r = $recs->fetch_assoc();
			return $recs[0]['code'];
		}
	}

	protected function validate_el($rec, $insert) {
        // individual derviatives SHOULD over-ride this for particular needs
		$errors = array();
        //echo "in DmTable::validate_el(): ";print_r($this->reqFields);echo "<br />\n";
        // check for missing entries
        if (isset($this->reqFields)) {
    		foreach ($this->reqFields as $req) {
    			if ($insert and !isset($rec[$req]) or isset($rec[$req]) and $rec[$req] == '') {
    				$errors[] = new FieldError($req, T("Required field missing"));
    			}
    		}
        }
        // duplicate codes not allowed
		$sql = $this->mkSQL("SELECT * FROM %q WHERE code=%Q ", $this->name, $rec['code']);
		$rslt = $this->select($sql);
        $rows = $rslt->fetchAll();
        if ($insert&& (count($rows) != 0)) {
            //$errors[] = new FieldError('code', T("Duplicate Code not allowed"));
			$errors[] = T("Duplicate Code not allowed");
		}
        // otherwise limit default flg to Y or N only
        if ((isset($rec['default_flg'])) && ($rec['default_flg'] != 'Y' && $rec['default_flg']!= 'N')) {
			//$errors[] = new FieldError('default_flg', T("Default Flg MUST be 'Y' or 'N'"));
			$errors[] = T("Default Flg MUST be 'Y' or 'N'");
        }
		return $errors;
    }

}   // end of class
