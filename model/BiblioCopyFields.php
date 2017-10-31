<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class BiblioCopyFields extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('biblio_copy_fields_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
        $this->setReq(array(
            'code', 'dedescription',
        ));
	}
	function validate_el($rec, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
		return $errors;
	}
}

