<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DmTable.php"));

class States extends DmTable {
	public function __construct() {
		parent::__construct();
		$this->setName('state_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
        $this->setReq(array(
            'code', 'description', 'default_flg',
        ));
		$this->setKey('code');
        //echo "in States::__construct(): ";print_r($this->fields);echo "<br />\n";
	}

	public function insert($rec, $confirmed=false) {
        // if no default flg present, set to 'N'
		if (!isset($rec['default_flg'])) {
            $rec['default_flg'] = 'N';
        }
        list($parm1, $parm2) = parent::insert($rec, $confirmed=false);
        return array($parm1, $parm2);
    }

}
