<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../classes/Queryi.php'));

class DBTable {
	var $db;
	var $name;
	var $fields = array();
	var $key = array();
	var $sequence = NULL;
	var $iter = NULL;
	var $foreign_keys = array();
	var $types = array(
		'string'=>'%Q',
		'number'=>'%N',
	);
	function DBTable() {
		$this->db = new Queryi();
	}
	function setName($name) {
		$this->name = $name;
	}
	function setFields($fields) {
		$this->fields = $fields;
		# FIXME - Check that field types are valid.
	}
	function setKey() {
		$this->key = func_get_args();
		foreach ($this->key as $k) {
			if (!isset($this->fields[$k])) {
				Fatal::internalError(T("Key field %key% not in field list", array('key'=>$k)));
			}
		}
	}
	function setSequenceField($sequence) {
		$this->sequence = $sequence;
		if (!isset($this->fields[$sequence])) {
			Fatal::internalError(T("DBTableSequenceField", array('sequence'=>$sequence)));
		}
	}
	function setForeignKey($key, $table, $field) {
		$this->foreign_keys[] = array('key'=>$key, 'table'=>$table, 'field'=>$field);
	}
	function setIter($classname) {
		$this->iter = $classname;
	}
	/* Abstract Method OVERRIDE THIS */
	function validate_el($rec, $insert) {
//echo "in DBTable:validate_el<br />\n";
		return array();
	}
	function maybeGetOne() {
		$key = func_get_args();
		$sql = $this->db->mkSQL('SELECT * FROM %I WHERE ', $this->name)
			. $this->_keyTerms($key);
		$row = $this->db->select01($sql);
		if ($row && $this->iter) {
			$c = $this->iter;	# Silly PHP
			$it = new $c(new ArrayIter(array($row)));
			return $it->next();
		}
		return $row;
	}
	function getOne() {
		$key = func_get_args();
		$row = call_user_func_array(array($this, 'maybeGetOne'), $key);
		if (!$row) {
			Fatal::internalError(T("Bad key (%key%) for %name% table", array('key'=>implode(', ', $key), 'name'=>$this->name)));
		}
		return $row;
	}
	function getAll($orderby=NULL) {
		$sql = $this->db->mkSQL('SELECT * FROM %I ', $this->name);
		if (!empty($orderby)) $sql .= $this->db->mkSQL('ORDER BY %q ', $orderby);
		if ($this->iter) {
			$c = $this->iter;	# Silly PHP
			return new $c($this->db->select($sql));
		} else
			return $this->db->select($sql);
	}
	function getMatches($fields, $orderby=NULL) {
		$sql = $this->db->mkSQL('SELECT * FROM %I WHERE ', $this->name)
			. $this->_pairs($fields, ' AND ');
		if ($orderby)
			$sql .= $this->db->mkSQL(' ORDER BY %I ', $orderby);
		if ($this->iter) {
			$c = $this->iter;	# Silly PHP
			return new $c($this->db->select($sql));
		} else
			return $this->db->select($sql);
	}
	function checkForeignKeys_el($rec) {
		$errors = array();
		foreach ($this->foreign_keys as $k) {
			if (!isset($rec[$k['key']]) or $rec[$k['key']] == NULL) {
				continue;
			}
			$sql = $this->db->mkSQL('SELECT * FROM %I '
				. 'WHERE %I='.$this->types[$this->fields[$k['key']]],
				$k['table'], $k['field'], $rec[$k['key']]);
			$r = $this->db->select01($sql);
			if (!$r) {
				$errors[] = new FieldError($k['key'], T("DBTableBadForeignKey", array('key'=>$rec[$k['key']], 'field'=>$k['key'])));
			}
		}
		return $errors;
	}
	function skipIgnorableErrors($errors) {
		$errs = array();
		foreach ($errors as $e) {
			if (!is_a($e, 'IgnorableError')) {
				$errs[] = $e;
			}
		}
		return $errs;
	}
	function insert($rec, $confirmed=false) {
		list($seq_val, $errors) = $this->insert_el($rec, $confirmed);
		if ($errors) {
			Fatal::internalError(T("DBTableErrorInserting", array('name'=>$this->name, 'error'=>Error::listToStr($errors))));
		}
		return $seq_val;
	}
	function insert_el($rec, $confirmed=false) {
//echo "in DBtable: insert_el<br />\n";
		$this->db->lock();
		$errs = $this->checkForeignKeys_el($rec);
		if (!empty($errs)) {
			$this->db->unlock();
			return array(NULL, $errs);
		}
//echo "ckFrnKeys done<br />\n";
		$errs = $this->validate_el($rec, true);
//echo "confirmed=$confirmed<br />\n";
		if ($confirmed) {
			$errs = $this->skipIgnorableErrors($errs);
		}
		if (!empty($errs)) {
			$this->db->unlock();
			return array(NULL, $errs);
		}
//echo "validate_el done<br />\n";
		$sql = $this->db->mkSQL('INSERT INTO %I SET ', $this->name)
			. $this->_pairs($rec);
//echo "sql=$sql<br />\n";
		$this->db->act($sql);
		if ($this->sequence) {
			if (isset($rec[$this->sequence])) {
				$seq_val = $rec[$this->sequence];
			} else {
				$seq_val = $this->db->getInsertID();
			}
		} else {
			$seq_val = NULL;
		}
//echo "act done<br />\n";
		$this->db->unlock();
		return array($seq_val, array());
	}
	function update($rec, $confirmed=false) {
		$errors = $this->update_el($rec, $confirmed);
		if ($errors) {
			Fatal::internalError(T("DBTableErrorUpdating", array('name'=>$this->name, 'error'=>Error::listToStr($errors))));
		}
	}
	function update_el($rec, $confirmed=false) {
		$key = array();
		foreach ($this->key as $k) {
			if (!isset($rec[$k]))
				Fatal::internalError(T("DBTableIncompleteKey", array('key'=>$k)));
			$key[] = $rec[$k];
		}
		$this->db->lock();
		$errs = $this->checkForeignKeys_el($rec);
		if (!empty($errs)) {
			$this->db->unlock();
			return $errs;
		}
		$errs = $this->validate_el($rec, false);
		if ($confirmed) {
			$errs = $this->skipIgnorableErrors($errs);
		}
		if (!empty($errs)) {
			$this->db->unlock();
			return $errs;
		}
		$sql = $this->db->mkSQL('UPDATE %I ', $this->name)
			. ' SET '.$this->_pairs($rec)
			. ' WHERE '.$this->_keyTerms($key);
		$this->db->act($sql);
		$this->db->unlock();
		return array();
	}
	function deleteOne() {
		$this->db->lock();
		$sql = $this->db->mkSQL('DELETE FROM %I WHERE ', $this->name)
			. $this->_keyTerms(func_get_args());
		$this->db->act($sql);
		$this->db->unlock();
	}
	function deleteMatches($fields) {
		$this->db->lock();
		$sql = $this->db->mkSQL('DELETE FROM %I WHERE ', $this->name)
			. $this->_pairs($fields, ' AND ');
		$this->db->act($sql);
		$this->db->unlock();
	}
	function _keyTerms($key) {
		if (count($key) != count($this->key)) {
			Fatal::internalError(T("Wrong key length"));
		}
		$terms = array();
		for ($i=0; $i<count($key); $i++) {
			$name = $this->key[$i];
			$type = $this->fields[$name];
			$terms[] = $this->db->mkSQL('%I='.$this->types[$type], $name, $key[$i]);
		}
		return implode(' AND ', $terms);
	}
	function _pairs($rec, $separator=', ') {
		$vals = array();
		foreach ($this->fields as $name => $type) {
			if (isset($rec[$name])) {
				$vals[] = $this->db->mkSQL('%I='.$this->types[$type], $name, $rec[$name]);
			}
		}
		return implode($separator, $vals);
	}
}
