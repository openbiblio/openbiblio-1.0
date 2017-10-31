<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class MediaTypes extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('material_type_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
			'adult_checkout_limit'=>'number',
			'juvenile_checkout_limit'=>'number',
			'image_file'=>'string',
			'srch_disp_lines'=>'number',
		));
        $this->setReq(array(
            'description', 'default_flg', 'adult_checkout_limit', 'juvenile_checkout_limit', 'srch_disp_lines',
        ));
		$this->setSequenceField('code');
		$this->setKey('code');
	}

	protected function validate_el($rec, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
        // test checkout_limits
		$positive = array('adult_checkout_limit', 'juvenile_checkout_limit');
		foreach ($positive as $f) {
			if (!is_numeric($rec[$f])) {
				$errors[] = new FieldError($f, T("Field must be numeric"));
			} else if ($rec[$f] < 0) {
				$errors[] = new FieldError($f, T("Field cannot be less than zero"));
			}
		}
        // otherwise limit default flg to Y or N only
        if ($rec['default_flg'] != 'Y' && $rec['default_flg']!= 'N') {
			$errors[] = new FieldError('default_flg', T("Default Flg MUST be 'Y' or 'N'"));
        }
		return $errors;
	}

	public function insert($rec, $confirmed=false) {
        // if no default flg present, set to 'N'
		if (!isset($rec['default_flg'])) {
            $rec['default_flg'] = 'N';
        }
        list($parm1, $parm2) = parent::insert($rec, $confirmed=false);
        return array($parm1, $parm2);
    }
	public function getAllWithStats() {
		$sql = "SELECT t.code, t.description, t.default_flg, "
				 . 				"t.adult_checkout_limit, t.juvenile_checkout_limit, "
				 . 				"t.image_file, t.srch_disp_lines, COUNT(distinct b.bibid) as count "
				 . " FROM material_type_dm t "
				 . " LEFT JOIN biblio b "
				 . "   ON b.material_cd=t.code "
				 . "GROUP BY t.code, t.description, t.default_flg, "
				 . "				 t.adult_checkout_limit, t.juvenile_checkout_limit, "
				 . "				 t.image_file, t.srch_disp_lines "
				 . "ORDER BY t.description ";
		return $this->select($sql);
	}
//	function getAll($orderBy=null) {
	public function getAll($orderBy='description') {
		$sql = "SELECT * FROM material_type_dm "
				 . " ORDER BY $orderBy ";
		return $this->select($sql);
	}
	public function getByBibid($bibid) {
		$sql = "SELECT m.* FROM material_type_dm m, biblio b"
				 . " WHERE $bibid = b.bibid AND m.code = b.material_cd";
		return $this->select1($sql);
	}
	public function get_name($code) {
		$sql = "SELECT t.description "
			. "FROM material_type_dm t "
			. "WHERE code='".$code."';";
		$row = $this->select1($sql);
		return $row['description'];
	}
	public function getIcons() {
		$sql = "SELECT t.code, t.image_file "
			. "FROM material_type_dm t ";
		$rslt = $this->select($sql);
		return $rslt;
	}

}
