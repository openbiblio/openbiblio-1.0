<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class MaterialTypes extends DmTable {
	function MaterialTypes() {
		$this->DmTable();
		$this->setName('material_type_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
			'adult_checkout_limit'=>'number',
			'juvenile_checkout_limit'=>'number',
			'image_file'=>'string',
		));
		$this->setSequenceField('code');
		$this->setKey('code');
	}
	function getAllWithStats() {
		$sql = "SELECT t.code, t.description, t.default_flg, "
				 . 				"t.adult_checkout_limit, t.juvenile_checkout_limit, "
				 . 				"t.image_file, COUNT(distinct b.bibid) as count "
				 . " FROM material_type_dm t "
				 . " LEFT JOIN biblio b "
				 . "   ON b.material_cd=t.code "
				 . "GROUP BY t.code, t.description, t.default_flg, "
				 . "				 t.adult_checkout_limit, t.juvenile_checkout_limit, "
				 . "				 t.image_file "
				 . "ORDER BY t.description ";
		return $this->db->select($sql);
	}
//	function getAll($orderBy=null) {
	function getAll($orderBy='description') {
		$sql = "SELECT * FROM material_type_dm "
				 . " ORDER BY $orderBy ";
		return $this->db->select($sql);
	}
	function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('description', 'adult_checkout_limit', 'juvenile_checkout_limit') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		$positive = array('adult_checkout_limit', 'juvenile_checkout_limit');
		foreach ($positive as $f) {
			if (!is_numeric($rec[$f])) {
				$errors[] = new FieldError($f, T("Field must be numeric"));
			} else if ($rec[$f] < 0) {
				$errors[] = new FieldError($f, T("Field cannot be less than zero"));
			}
		}
		return $errors;
	}
	function get_name($code) {
		$sql = "SELECT t.description "
			. "FROM material_type_dm t "
			. "WHERE code='".$code."';";
		$row = $this->db->select1($sql);
		return $row['description'];
	}

}
