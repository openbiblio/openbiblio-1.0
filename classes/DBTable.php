<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../classes/Queryi.php'));

/**
 * provides common DB facilities needed by lookup table classes
 * @author Micah Stetson
 */

abstract class DBTable extends Queryi {
	private $name;
	private $fields = array();
	private $key = array();
	private $sequence = NULL;
	private $iter = NULL;
	private $foreign_keys = array();
	private $types = array(
		'string'=>'%Q',
		'number'=>'%N',
	);

	public function __construct() {
		parent::__construct();
	}

	abstract protected function validate_el($rec, $insert); /*{ return array(); }*/

	## ------------------------------------------------------------------------ ##
	protected function setFields($fields) {
		$this->fields = $fields;
		# FIXME - Check that field types are valid.
	}
	protected function setKey() {
		$this->key = func_get_args();
		foreach ($this->key as $k) {
			if (!isset($this->fields[$k])) {
				Fatal::internalError(T("Key field %key% not in field list", array('key'=>$k)));
			echo "sql=$sql<br />\n";
			}
		}
	}
	public function getKeyList($key, $fields) {
		$sql = $this->mkSQL('SELECT %I FROM %I WHERE ', $key, $this->name)
			. $this->_pairs($fields, ' AND ');
//if ($this->name == "biblio_copy") {echo "in DBTable::getKeyList(), sql= $sql <br />\n";}
		if ($this->iter) {
			$c = $this->iter;	# Silly PHP
			return new $c($this->select($sql));
		} else
			return $this->select($sql);
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function setSequenceField($sequence) {
		$this->sequence = $sequence;
		if (!isset($this->fields[$sequence])) {
			Fatal::internalError(T("DBTableSequenceField", array('sequence'=>$sequence)));
			echo "sql=$sql<br />\n";
		}
	}
	public function setForeignKey($key, $table, $field) {
		$this->foreign_keys[] = array('key'=>$key, 'table'=>$table, 'field'=>$field);
	}
	public function setIter($classname) {
		$this->iter = $classname;
	}
	public function maybeGetOne() {
		$key = func_get_args();
		$sql = $this->mkSQL('SELECT * FROM %I WHERE ', $this->name)
			. $this->_keyTerms($key);
        //echo "in DBTable::maybeGetOne(): ";echo "$sql";echo "<br /> \n";
		$row = $this->select01($sql);
		return $row;
	}
	public function getOne() {
		$key = func_get_args();
		$row = call_user_func_array(array($this, 'maybeGetOne'), $key);
		if (is_null($row)) {
			//Fatal::internalError(T("Bad key (%key%) for %name% table", array('key'=>implode(', ', $key), 'name'=>$this->name)));
            //echo "Fatal::internalError";echo "Bad key ";print_r($key);echo" for table $this->name";
			//echo "sql=$sql<br />\n";
		}
		return $row;
	}
	public function getAll($orderby = NULL) {
		$sql = $this->mkSQL('SELECT * FROM %I ', $this->name);
		//if (!empty($orderby)) $sql .= $this->mkSQL('ORDER BY %q ', $orderby);
		if (!empty($orderby)) {$sql .= "ORDER BY $orderby";}
        $recs = $this->select($sql);
//if ($this->name == 'biblio_status_dm') {
//    echo  "$sql <br /> \n";
//    print_r($recs);echo  "<br /> \n";
//}
		return $recs;
	}
	public function getMatches($fields, $orderby=NULL) {
        //echo "in DBTable::getMatches()";print_r($fields);echo "<br />\n";
		$sql = $this->mkSQL('SELECT * FROM %I WHERE ', $this->name)
			. $this->_pairs($fields, ' AND ');
		if ($orderby)
			$sql .= $this->mkSQL(' ORDER BY %I ', $orderby);
        //echo "in DBTable::getMatches(), sql= $sql <br />\n";
		if ($this->iter) {
			$c = $this->iter;	# Silly PHP
            $rslt = new $c($this->select($sql));
            //echo "in DBTable::getMatches() (with itr): ";print_r($rslt);echo "<br />\n";
			return $rslt;
		} else {
            $rslt = $this->select($sql);
            //echo "in DBTable::getMatches() (without itr): ";print_r($rslt);echo "<br />\n";
			return $rslt;
        }
	}
	public function checkForeignKeys_el($rec) {
		$errors = array();
		foreach ($this->foreign_keys as $k) {
			if (!isset($rec[$k['key']]) or $rec[$k['key']] == NULL) {
				continue;
			}
			$sql = $this->mkSQL('SELECT * FROM %I '.
				  'WHERE %I='.$this->types[$this->fields[$k['key']]],
				  $k['table'], $k['field'], $rec[$k['key']]);
			$r = $this->select01($sql);
			if (!$r) {
				$errors[] = new FieldError($k['key'], T("DBTableBadForeignKey", array('key'=>$rec[$k['key']], 'field'=>$k['key'])));
			}
		}
		return $errors;
	}
	public function insert($rec, $confirmed=false) {
		list($seq_val, $errors) = $this->insert_el($rec, $confirmed);
//		if ($errors) {
//			Fatal::internalError(T("DBTableErrorInserting")." '".$this->name."', ".Error::listToStr($errors));
//		}
		return array($seq_val, $errors);
	}
	public function insert_el($rec, $confirmed=false) {
//echo "in DBTbl:insert_el ,";
//print_r($rec);
		$this->lock();
		//$errs = $this->checkForeignKeys_el($rec);
		//if (!empty($errs)) {
		//	$this->unlock();
		//	return array(NULL, $errs);
		//}
//		$errs = $this->validate_el($rec, true);
//		if ($confirmed) {
//			$errs = $this->skipIgnorableErrors($errs);
//		}
//		if (!empty($errs)) {
//			$this->unlock();
//			return array(NULL, $errs);
//		}
		$sql = $this->mkSQL('INSERT INTO %I SET ', $this->name)
			. $this->_pairs($rec);
		$this->act($sql);
		if ($this->sequence) {
			if (isset($rec[$this->sequence])) {
				$seq_val = $rec[$this->sequence];
			} else {
				$seq_val = $this->getInsertID();
			}
		} else {
			$seq_val = NULL;
		}
		$this->unlock();
		return array($seq_val, array());
	}
	public function update($rec, $confirmed=false) {
		$errors = $this->update_el($rec, $confirmed);
		if ($errors) {
			//Fatal::internalError(T("DBTableErrorUpdating", array('name'=>$this->name, 'error'=>Error::listToStr($errors))));
			Fatal::internalError(T("DBTableErrorUpdating")." '".$this->name."', ".Error::listToStr($errors));
		}
	}
	public function update_el($rec, $confirmed=false) {
		$key = array();
		foreach ($this->key as $k) {
			if (!isset($rec[$k]))
				Fatal::internalError(T("DBTableIncompleteKey", array('key'=>$k)));
			$key[] = $rec[$k];
		}
		$this->lock();
		$errs = $this->checkForeignKeys_el($rec);
		if (!empty($errs)) {
			$this->unlock();
			return $errs;
		}
		$errs = $this->validate_el($rec, false);
		if ($confirmed) {
			$errs = $this->skipIgnorableErrors($errs);
		}
		if (!empty($errs)) {
			$this->unlock();
			return $errs;
		}
		$sql = $this->mkSQL('UPDATE %I ', $this->name)
			. ' SET '.$this->_pairs($rec)
			. ' WHERE '.$this->_keyTerms($key);
		$this->act($sql);
		$this->unlock();
		return array();
	}
	public function deleteOne() {
		$this->lock();
		$sql = $this->mkSQL('DELETE FROM %I WHERE ', $this->name)
			. $this->_keyTerms(func_get_args());
        //echo "sql=$sql<br />\n";
		$this->act($sql);
		$this->unlock();
	}
	public function deleteMatches($fields) {
		$this->lock();
		$sql = $this->mkSQL('DELETE FROM %I WHERE ', $this->name)
			. $this->_pairs($fields, ' AND ');
		$this->act($sql);
		$this->unlock();
	}

	## ------------------------------------------------------------------------ ##
	private function skipIgnorableErrors($errors) {
		$errs = array();
		foreach ($errors as $e) {
			if (!is_a($e, 'IgnorableError')) {
				$errs[] = $e;
			}
		}
		return $errs;
	}
	private function _keyTerms($key) {
		if (count($key) != count($this->key)) {
			Fatal::internalError(T("Wrong key length"));
		}
		$terms = array();
		for ($i=0; $i<count($key); $i++) {
			$name = $this->key[$i];
			$type = $this->fields[$name];
			$terms[] = $this->mkSQL('%I='.$this->types[$type], $name, $key[$i]);
		}
		return implode(' AND ', $terms);
	}
	private function _pairs($rec, $separator=', ') {
//echo "in pairs,";print_r($rec);echo ".:.:."; print_r($separator); echo "<br />\n";
		$vals = array();
		foreach ($this->fields as $name => $type) {
//echo "name=$name; type=$type; rec[$name]=$rec[$name] <br/>\n";
			if (isset($rec[$name])) {
//echo "type ".$this->types[$type]." <br /> \n";
				 //$s = $this->mkSQL('%I='.$this->types[$type], $name, $rec[$name]);
				 //$s = $this->mkSQL("%I=".$this->types[$type], $name, $rec[$name]);
                 $s = "`$name` = '".$rec[$name]."'";
//echo "mkSql string: $s <br />\n";
                 $vals[] = $s;
			}
		}
//print_r($vals);
		return implode($separator, $vals);
	}
}
