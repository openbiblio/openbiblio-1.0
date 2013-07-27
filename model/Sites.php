<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class Sites extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('site');
		$this->setFields(array(
			'siteid'=>'number',
			'name'=>'string',
			'code'=>'string',
			'delivery_note'=>'string',
			'address1'=>'string',
			'address2'=>'string',
			'city'=>'string',
			'state'=>'string',
			'zip'=>'string',
			'phone'=>'string',
			'fax'=>'string',
			'email'=>'string',
			'calendar'=>'number',
		));
		$this->setKey('siteid');
		$this->setSequenceField('siteid');
		$this->setForeignKey('calendar', 'calendar_dm', 'code');
	}
	function getByMbrid($mbrid) {
		$sql = $this->mkSQL('SELECT s.* FROM member m, site s '
			. 'WHERE m.mbrid=%N and s.siteid=m.siteid ', $mbrid);
		return $this->select1($sql);
	}
	function getSelect($all=false) {
		$select = array();
		if ($all) {
			$select['all'] = 'All';
		}
		$recs = $this->getAll('name');
		//while ($rec = $recs->fetch_assoc()) {
		while ($rec = $recs->fetch_assoc()) {
			$select[$rec['siteid']] = $rec['name'];
		}
		return $select;
	}
	protected function validate_el($rec, $insert) {
		$errors = array();
		foreach (array('name') as $req) {
			if ($insert and !isset($rec[$req])
					or isset($rec[$req]) and $rec[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		return $errors;
	}
	////TODO this should not be needed, but DBTable function doesn't work - FL
	//function deleteOne($siteid) {
	//	$this->lock();
	//	$sql = $this->mkSQL('DELETE FROM `site` WHERE `siteid`=%N', $siteid);
	//	$this->act($sql);
	//	$this->unlock();
	//}
}
