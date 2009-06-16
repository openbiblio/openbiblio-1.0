<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

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
