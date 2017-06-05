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
        $this->setReq(array(
            'name', 'code', 'city', 'delivery_note',
        ));
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
        //echo "in sites::getSelect()"; print_r($recs);echo "<br />\n";
        //if ($recs->num_rows <= 0){
        //   return 'default';
        //}
		//while ($rec = $recs->fetch_assoc()) {
        foreach ($recs as $rec) {
			$select[$rec['siteid']] = $rec['name'];
		}
		return $select;
	}

	protected function validate_el($rec, $insert) {
		// check for required fields done in DBTable
		$errors = parent::validate_el($rec, $insert);
		return $errors;
	}

	function deleteOne() {
		$id_to_delete = func_get_arg(0);
		if ($_SESSION['current_site'] == $id_to_delete) {
			$error = new OBErr(T("Please do not delete the current site."));
			return $error->toStr();
		}
		$sql = "SELECT COUNT(copyid) as copies FROM biblio_copy WHERE siteid=" . $id_to_delete;
		$row = $this->select1($sql);
		if (0 != $row['copies']) {
			$error = new OBErr(T("You cannot delete a site that has copies attached to it."));
			return $error->toStr();
		}

		return parent::deleteOne($id_to_delete);
	}
}
