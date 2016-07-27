,<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class CircCollections extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('collection_circ');
		$this->setFields(array(
			'code'=>'number',
			'days_due_back'=>'number',
			'minutes_due_back'=>'number',
			'regular_late_fee'=>'number',
			'due_date_calculator'=>'string',
			'minutes_before_closing'=>'number',
			'important_date'=>'date',
			'important_date_purpose'=>'string',
			'number_of_minutes_between_fee_applications'=>'number',
			'number_of_minutes_in_grace_period'=>'number',
		));
		$this->setKey('code');
		$this->setForeignKey('code', 'collection_dm', 'code');
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('code', 'days_due_back', 'minutes_due_back', 'due_date_calculator', 'regular_late_fee') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		$positive = array('days_due_back', 'regular_late_fee');
		foreach ($positive as $f) {
			if (!is_numeric($rec[$f])) {
				$errors[] = new FieldError($f, T("Field must be numeric"));
			} else if ($rec[$f] < 0) {
				$errors[] = new FieldError($f, T("Field cannot be less than zero"));
			}
		}
		return $errors;
	}
}

class DistCollections extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('collection_dist');
		$this->setFields(array(
			'code'=>'number',
			'restock_threshold'=>'number',
		));
		$this->setKey('code');
		$this->setForeignKey('code', 'collection_dm', 'code');
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('code', 'restock_threshold') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		$positive = array('restock_threshold');
		foreach ($positive as $f) {
			if (!is_numeric($rec[$f])) {
				$errors[] = new FieldError($f, T("Field must be numeric"));
			} else if ($rec[$f] < 0) {
				$errors[] = new FieldError($f, T("Field cannot be less than zero"));
			}
		}
		return $errors;
	}
}

class Collections extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('collection_dm');
		$this->setFields(array(
			'code'=>'number',
			'description'=>'string',
			'default_flg'=>'string',
			'type'=>'string',
		));
        $this->setReq(array(
            'code', 'description', 'default_flg', 'type'
        ));
		$this->setSequenceField('code');
		$this->setKey('code');

		$this->colltypes = array(
			'Circulated' => new CircCollections,
			'Distributed' => new DistCollections,
		);
	}

	protected function validate_el($rec, $insert) {
		$errors = array();
        // all required fields present?
		foreach ($this->reqFields as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
        // valid collection type?
		if (isset($rec['type']) and !array_key_exists($rec['type'], $this->colltypes)) {
			$errors[] = new FieldError('type', T("Bad collection type"));
		}
        // duplicate state codes not allowed
		$sql = $this->mkSQL("SELECT * FROM %q WHERE code=%Q ", $this->name, $rec['code']);
		$rslt = $this->select($sql);
        $rows = $rslt->fetchAll();
        if ($insert&& (count($rows) != 0)) {
			//$errors[] = new FieldError('code', T("Duplicate State Code not allowed"));
			$errors[] = T("Duplicate Code not allowed");
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

	public function getTypeSelect() {
		$types = array();
		foreach (array_keys($this->colltypes) as $t) {
			$types[$t] = $t;
		}
		return $types;
	}
	public function getByBibid($bibid) {
		$sql = "SELECT c.* FROM collection_circ c, biblio b "
			. "WHERE c.code=b.collection_cd "
			. $this->mkSQL("AND b.bibid=%N ", $bibid);
		return $this->select1($sql);
	}
	public function getAllWithStats() {
		$sql = "SELECT c.*, "
			. "COUNT(distinct b.bibid) as count "
			. "FROM collection_dm c "
			. "LEFT JOIN biblio b "
			. "ON b.collection_cd=c.code "
			. "GROUP BY c.code, c.description, c.default_flg "
			. "ORDER BY c.description ";
		return $this->select($sql);
	}
	public function getTypeData($rec) {
		$table = $this->colltypes[$rec['type']];
		return $table->getOne($rec['code']);
	}
	public function get_name($code) {
		$sql = "SELECT description "
			. "FROM collection_dm "
			. "WHERE code='".$code."';";
		$row = $this->select1($sql);
		return $row['description'];
	}
	public function insert_el($rec, $confirmed = false) {
		list ($id, $errs) = DBTable::insert_el($rec, $confirmed);
		if ($errs)
			return array(NULL, $errs);
		$rec['code'] = $id;
		list (, $errs) = $this->colltypes[$rec['type']]->insert_el($rec);
		if ($errs) {
			DBTable::deleteOne($id);
			return array(NULL, $errs);
		}
		return array($id, NULL);
	}
	public function update_el($rec, $confirmed = false) {
		$old = $this->getOne($rec['code']);
		$errs = DBTable::update_el($rec, $confirmed);
		if ($errs)
			return $errs;
		$updated = $this->getOne($rec['code']);
		if ($old['type'] == $updated['type']) {
			$table = $this->colltypes[$updated['type']];
			$errs = $table->update_el($rec);
		} else {
			$otable = $this->colltypes[$old['type']];
			$ntable = $this->colltypes[$updated['type']];
			$otable->deleteOne($rec['code']);
			list(, $errs) = $ntable->insert_el($rec);
		}
		return $errs;
	}
	public function deleteOne() {
		$code = func_get_args(0);
		DBTable::deleteOne($code);
		foreach ($this->colltypes as $table) {
			$table->deleteOne($code);
		}
	}
	public function deleteMatches($fields) {
		$rows = $this->getMatches($fields);
		while ($row = $rows->fetch_assoc()) {
			$this->deleteOne($row['code']);
		}
	}
}
