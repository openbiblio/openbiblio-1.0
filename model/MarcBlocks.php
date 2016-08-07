<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../classes/DBTable.php"));

class MarcBlocks extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('usmarc_block_dm');
		$this->setFields(array(
			'block_nmbr'=>'number',
			'description'=>'string',
		));
		$this->setKey('block_nmbr');
		$this->setSequenceField('block_nmbr');
	}
	protected function validate_el($rec, $insert) { /*return array();*/ }
}

