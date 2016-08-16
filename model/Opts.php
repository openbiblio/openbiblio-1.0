<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));
//require_once(REL(__FILE__, "../model/Collections.php"));

class Opts extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('lookup_settings');
		$this->setFields(array(
			'id'=>'number',
			'protocol'=>'string',
			'maxHits'=>'number',
			'timeout'=>'number',
			'callNmbrType'=>'string',
			'autoDewey'=>'string',
			'defaultDewey'=>'string',
			'autoCutter'=>'string',
			'cutterType'=>'string',
			'cutterWord'=>'string',
			'noiseWords'=>'string',
			'autoCollect'=>'string',
			'fictionName'=>'string',
			'fictionCode'=>'string',
			'fictionLoc'=>'string',
			'fictionDew'=>'string'
		));
		$this->setKey('id');
	}

	protected function validate_el($rec, $insert) { return array(); }
}

