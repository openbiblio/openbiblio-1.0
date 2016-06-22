<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class States extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('state_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
        $this->setReq(array(
            'code', 'description', 'default_flg',
        ));
		$this->setKey('code');
        //echo "in States::__construct(): ";print_r($this->fields);echo "<br />\n";
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
        //echo "in States::validate_el(): ";print_r($this->reqFields);echo "<br />\n";
        // if no default flg present, set to 'N'
		if (!isset($rec['default_flg'])) {
            $rec['default_flg'] = 'N';
        }
        // check for missing entries
		foreach ($this->reqFields as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
        // duplicate state codes not allowed
		$sql = $this->mkSQL("SELECT * FROM %q WHERE code=%Q ", $this->name, $rec['code']);
		$rows = $this->select($sql);
		//if ($rows->count() != 0) {
        if ($insert&& (count($rows) != 0)) {
			$errors[] = new FieldError('code', T("Duplicate State Code not allowed"));
		}
        // otherwise limit default flg to Y or N only
        if ($rec['default_flg'] != 'Y' && $rec['default_flg']!= 'N') {
			$errors[] = new FieldError('default_flg', T("Default Flg MUST be 'Y' or 'N'"));
        }
		return $errors;
	}
}
