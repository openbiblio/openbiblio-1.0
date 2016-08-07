<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class MarcSubfields extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('usmarc_subfield_dm');
		$this->setFields(array(
			'tag'=>'number',
			'subfield_cd'=>'string',
			'description'=>'string',
			'repeatable_flg'=>'string',
		));
		$this->setKey('tag','subfield_cd');
		$this->setSequenceField('subfield_cd');
	}
	protected function validate_el($rec, $insert) { /*return array();*/ }
}
