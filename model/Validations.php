<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class Validations extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('validation_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'pattern'=>'string',
		));
		$this->setKey('code');
	}
	////TODO this should not be needed, but DBTable function doesn't work - FL
	function deleteOne($code) {
		$this->lock();
		$sql = $this->mkSQL('DELETE FROM `validation_dm` WHERE `code`=%Q', $code);
		$this->act($sql);
		$this->unlock();
	}
}
