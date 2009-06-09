<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class BiblioCopyFields extends DmTable {
	function BiblioCopyFields() {
		$this->DmTable();
		$this->setName('biblio_copy_fields_dm');
		$this->setFields(array(
			'code'=>'number',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
	}
}

