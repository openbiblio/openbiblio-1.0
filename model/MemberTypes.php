<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class MemberTypes extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('mbr_classify_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
			'max_fines'=>'number',
		));
        $this->setReq(array(
            'description', 'default_flg', 'max_fines',
        ));
		$this->setKey('code');
	}

	public function insert($rec, $confirmed=false) {
        // if no default flg present, set to 'N'
		if (!isset($rec['default_flg'])) {
            $rec['default_flg'] = 'N';
        }
        list($parm1, $parm2) = parent::insert($rec, $confirmed=false);
        return array($parm1, $parm2);
    }

	protected function validate_el($rec, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
        // duplicate mbrType codes not allowed
		$sql = $this->mkSQL("SELECT * FROM %q WHERE code=%Q ", $this->name, $rec['code']);
		$rslt = $this->select($sql);
        $rows = $rslt->fetchAll();
        if ($insert&& (count($rows) != 0)) {
			//$errors[] = new FieldError('code', T("Duplicate Code not allowed"));
			$errors[] = T("Duplicate Code not allowed");
		}
        // otherwise limit default flg to Y or N only
        if ($rec['default_flg'] != 'Y' && $rec['default_flg']!= 'N') {
			$errors[] = new FieldError('default_flg', T("Default Flg MUST be 'Y' or 'N'"));
        }
		return $errors;
	}
}
