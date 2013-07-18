<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class TransactionTypes extends DmTable {
	public function __construct() {
//		$this->DmTable();
		parent::__construct();
		$this->setName('transaction_type_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
	}
}
