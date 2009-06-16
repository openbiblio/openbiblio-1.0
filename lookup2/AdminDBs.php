<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));
require_once(REL(__FILE__, "../model/Collections.php"));

class Opts extends DBTable {
	function Opts() {
		$this->DBTable();
		$this->setName('lookup_settings');
		$this->setFields(array(
			'id'=>'number',
			'protocoll'=>'string',
			'maxHits'=>'number',
			'callNmbrType'=>'string',
			'autoDewey'=>'string',
			'defaultDewey'=>'string',
			'autoCutter'=>'string',
			'cutterType'=>'string',
			'cutterWord'=>'string',
			'autoCollect'=>'string',
			'fictionName'=>'string',
			'fictionCode'=>'string',
			'fictionLoc'=>'string',
			'fictionDew'=>'string'
		));
		$this->setKey('id');
	}
}

class Hosts extends DBTable {
	function Hosts() {
		$this->DBTable();
		$this->setName('lookup_hosts');
		$this->setFields(array(
			'id'=>'number',
			'seq'=>'number',
			'active'=>'string',
			'host'=>'string',
			'name'=>'string',
			'db'=>'string',
			'user'=>'string',
			'pw'=>'string'
		));
		$this->setKey('id');
		$this->setSequenceField('seq');
	}
}

class myColl extends Collections {
	function getDefault() {
		$sql = "SELECT * FROM ".$this->name." WHERE `default_flg`='Y' ";
		return $this->db->select1($sql);
	}
}
