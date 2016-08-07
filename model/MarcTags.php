<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class MarcTags extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('usmarc_tag_dm');
		$this->setFields(array(
			'block_nmbr'=>'number',
			'tag'=>'number',
			'description'=>'string',
			'ind1_description'=>'string',
			'ind2_description'=>'string',
			'repeatable_flg'=>'string',
		));
		$this->setKey('block_nmbr','tag');
		$this->setSequenceField('tag');
	}
	protected function validate_el($rec, $insert) { /*return array();*/ }
}

