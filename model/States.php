<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class States extends DmTable {
	function States() {
		$this->DmTable();
		$this->setName('state_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
	}
	////TODO this should not be needed, but DBTable function doesn't work - FL
	function deleteOne($code) {
		$this->db->lock();
		$sql = $this->db->mkSQL('DELETE FROM `state_dm` WHERE `code`=%Q', $code);
		$this->db->act($sql);
		$this->db->unlock();
	}
}
