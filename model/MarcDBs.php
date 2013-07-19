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
}

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
			'repeatable'=>'string',
		));
		$this->setKey('block_nmbr','tag');
		$this->setSequenceField('tag');
	}
}

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
}
