<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class CircCollections extends DBTable {
	function CircCollections() {
		$this->DBTable();
		$this->setName('collection_circ');
		$this->setFields(array(
			'code'=>'number',
			'days_due_back'=>'number',
			'daily_late_fee'=>'number',
		));
		$this->setKey('code');
		$this->setForeignKey('code', 'collection_dm', 'code');
	}
	function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('code', 'days_due_back', 'daily_late_fee') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		$positive = array('days_due_back', 'daily_late_fee');
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
	function DistCollections() {
		$this->DBTable();
		$this->setName('collection_dist');
		$this->setFields(array(
			'code'=>'number',
			'restock_threshold'=>'number',
		));
		$this->setKey('code');
		$this->setForeignKey('code', 'collection_dm', 'code');
	}
	function validate_el($rec, $insert) {
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
	function Collections() {
		$this->DmTable();
		$this->setName('collection_dm');
		$this->setFields(array(
			'code'=>'number',
			'description'=>'string',
			'default_flg'=>'string',
			'type'=>'string',
		));
		$this->setSequenceField('code');
		$this->setKey('code');

		$this->colltypes = array(
			'Circulated' => new CircCollections,
			'Distributed' => new DistCollections,
		);
	}
	function getTypeSelect() {
		$types = array();
		foreach (array_keys($this->colltypes) as $t) {
			$types[$t] = $t;
		}
		return $types;
	}
	function getByBibid($bibid) {
		$sql = "SELECT c.* FROM collection_circ c, biblio b "
			. "WHERE c.code=b.collection_cd "
			. $this->db->mkSQL("AND b.bibid=%N ", $bibid);
		return $this->db->select1($sql);
	}
	function getAllWithStats() {
		$sql = "SELECT c.*, "
			. "COUNT(distinct b.bibid) as count "
			. "FROM collection_dm c "
			. "LEFT JOIN biblio b "
			. "ON b.collection_cd=c.code "
			. "GROUP BY c.code, c.description, c.default_flg "
			. "ORDER BY c.description ";
		return $this->db->select($sql);
	}
	function getTypeData($rec) {
		$table = $this->colltypes[$rec['type']];
		return $table->getOne($rec['code']);
	}
	function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('description', 'type') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		if (isset($rec['type']) and !array_key_exists($rec['type'], $this->colltypes)) {
			$errors[] = new FieldError('type', T("Bad collection type"));
		}
		return $errors;
	}
	function get_name($code) {
		$sql = "SELECT description "
			. "FROM collection_dm "
			. "WHERE code='".$code."';";
		$row = $this->db->select1($sql);
		return $row['description'];
	}
	function insert_el($rec) {
		list ($id, $errs) = DBTable::insert_el($rec);
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
	function update_el($rec) {
		$old = $this->getOne($rec['code']);
		$errs = DBTable::update_el($rec);
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
	function deleteOne($code) {
		DBTable::deleteOne($code);
		foreach ($this->colltypes as $table) {
			$table->deleteOne($code);
		}
	}
	function deleteMatches($fields) {
		$rows = $this->getMatches($fields);
		while ($row = $rows->fetch_assoc()) {
			$this->deleteOne($row['code']);
		}
	}
}
