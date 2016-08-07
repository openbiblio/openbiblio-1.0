<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));
require_once(REL(__FILE__, "../model/Collections.php"));

class Hosts extends DBTable {
	public function __construct() {
		parent::__construct();
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

	protected function validate_el($rec, $insert) { /*return array();*/ }
}
