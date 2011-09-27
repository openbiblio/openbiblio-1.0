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
			//'protocol'=>'string',
			'maxHits'=>'number',
			'timeout'=>'number',
			'callNmbrType'=>'string',
			'autoDewey'=>'string',
			'defaultDewey'=>'string',
			'autoCutter'=>'string',
			'cutterType'=>'string',
			'cutterWord'=>'string',
			'noiseWords'=>'string',
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
			'port'=>'number',
			'name'=>'string',
			'db'=>'string',
			'service'=>'string',
			'syntax'=>'string',
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
