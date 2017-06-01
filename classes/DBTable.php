<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../classes/Queryi.php'));

/**
 * provides common DB facilities used by various Models
 * @author Micah Stetson
 * modified for PHP 5.0 FL
 * modified to suport required Field validations - June 2016 FL
 */

abstract class DBTable extends Queryi {
	protected $name;
	protected $fields = array();
    protected $reqFields = array();
	protected $key = array();
	protected $sequence = NULL;
	protected $iter = NULL;
	protected $foreign_keys = array();
	protected $types = array(
		'string'=>'%Q',
		'number'=>'%N',
	);

	public function __construct() {
		parent::__construct();
	}

	abstract protected function validate_el($rec, $insert); /*{ return array(); }*/

	## ------------------ setters ---------------------------------------- ##
	protected function setName($name) {
		$this->name = $name;
	}
	protected function setFields($fields) {
		$this->fields = $fields;
		# FIXME - Check that field types are valid.
	}
	protected function setReq($fields) {
		$this->reqFields = $fields;
        //echo "in DBTable::setReq(): ";print_r($this->reqFields);echo "<br />\n";
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
	protected function setForeignKey($key, $table, $field) {
		$this->foreign_keys[] = array('key'=>$key, 'table'=>$table, 'field'=>$field);
	}
	protected function setIter($classname) {
		$this->iter = $classname;
	}
	protected function setSequenceField($sequence) {
		$this->sequence = $sequence;
		if (!isset($this->fields[$sequence])) {
			Fatal::internalError(T("DBTableSequenceField", array('sequence'=>$sequence)));
			echo "sql=$sql<br />\n";
		}
	}

	## ------------------ getters ---------------------------------------- ##
    public function getName() {return $this->name;}
    public function getFields() {return $this->fields;}

	## ------------------------------------------------------------------------ ##
	public function getKeyList($key, $fields) {
		$sql = $this->mkSQL('SELECT %I FROM %I WHERE ', $key, $this->name)
			. $this->_pairs($fields, ' AND ');
        //if ($this->name == "biblio_copy") {echo "in DBTable::getKeyList(), sql= $sql <br />\n";}
		//print_r($fields);
        if ($this->iter) {
			$c = $this->iter;	# Silly PHP
			return new $c($this->select($sql));
		} else
			return $this->select($sql);
	}
	public function maybeGetOne() {
		$key = func_get_args();
		$sql = $this->mkSQL('SELECT * FROM %I WHERE ', $this->name)
			. $this->_keyTerms($key);
 		//echo "in DBTable::maybeGetOne(): ";echo "$sql";echo "<br /> \n";
		$row = $this->select01($sql);
		// echo "in DBTable::maybeGetOne(): ";print_r("$row");echo "<br /> \n";
		return $row;
	}
	public function getOne() {
		$key = func_get_args();
		$row = call_user_func_array(array($this, 'maybeGetOne'), $key);
		if (is_null($row)) {
			//Fatal::internalError(T("Bad key (%key%) for %name% table", array('key'=>implode(', ', $key), 'name'=>$this->name)));
            echo "Fatal::internalError";echo "Bad key ";print_r($key);echo" for table $this->name";
			//echo "sql=$sql<br />\n";
		}
 		//echo "in DBTable::getOne(): ";print_r($row);echo "<br /> \n";
		return $row;
	}
	public function getAll($orderby = NULL) {
		$sql = $this->mkSQL('SELECT * FROM %I ', $this->name);
		if (!empty($orderby)) {$sql .= "ORDER BY $orderby";}
        //if ($this->name == 'lookup_settings') echo "in DBTable::getAll() sql = $sql<br />\n";
        //echo "sql = $sql<br />\n";

        $recs = $this->select($sql);
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
	public function insert($rec, $confirmed=false) {
        list($seqVal, $errors) = $this->insert_el($rec, $confirmed);
		if ($errors) {
			//Fatal::internalError(T("DBTableErrorInserting")." '".$this->name."', ".Error::listToStr($errors));
            //echo "Error: $this->name; ";print_r($errors);echo "<br />\n";
            return array(NULL, $errors);
		}
        return array($seqVal, 'Success');
	}
	public function insert_el($rec, $confirmed=false) {
		//echo "in DBTbl:insert_el ,";print_r($rec);echo "<br />\n";
		$this->lock();
		//$errs = $this->checkForeignKeys_el($rec);
		//if (!empty($errs)) {
		//	$this->unlock();
		//	return array(NULL, $errs);
		//}

		$errs = $this->validate_el($rec, true);
		if ($confirmed) {
			$errs = $this->skipIgnorableErrors($errs);
		}
		if (!empty($errs)) {
			$this->unlock();
            //echo "in DBTable::insert_el(), after validate: ";print_r($errs);echo "<br /> \n";
			return array(NULL, $errs);
		}

		$sql = $this->mkSQL('INSERT INTO %I SET ', $this->name)
			. $this->_pairs($rec);
		//echo "sql = $sql <br />\n";
		$this->act($sql);
		if ($this->sequence) {
			if (isset($rec[$this->sequence])) {
				$seq_val = $rec[$this->sequence];
			} else {
				//echo 'getting auto increment key';
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
			//Fatal::internalError(T("DBTableErrorUpdating")." '".$this->name."', ".Error::listToStr($errors));
            //echo "Error: $this->name; ";print_r($errors);echo "<br />\n";
            return $errors;
		} else {
        	return T("Success");
		}
	}
	public function update_el($rec, $confirmed=false) {
		//echo "in DBTable::update_el()";
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

		if (!$errs) {
			$sql = $this->mkSQL('UPDATE %I ', $this->name)
				. ' SET '.$this->_pairs($rec)
				. ' WHERE '.$this->_keyTerms($key);
			$rslt = $this->act($sql);
			$errs = $rslt->fetch();
			$this->unlock();
		}
		if ($errs) {
			return $errs;
			//return array();
		} else {
        	return T("Success");
		}
	}
	public function deleteOne() {
		//echo "in DBTable::deleteOne()";
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
	protected function checkForeignKeys_el($rec) {
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
                 $s = "$name = '".str_replace("'", "''", $rec[$name])."'";
//echo "mkSql string: $s <br />\n";
                 $vals[] = $s;
			}
		}
//print_r($vals);
		return implode($separator, $vals);
	}
}
