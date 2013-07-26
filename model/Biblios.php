<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../classes/Iter.php"));
require_once(REL(__FILE__, "../model/MarcStore.php"));
require_once(REL(__FILE__, "../model/BiblioImages.php"));

class Biblios extends CoreTable {
	public function __construct() {
		parent::__construct();
		$this->setName('biblio');
		$this->setFields(array(
			'bibid'=>'number',
			'material_cd'=>'number',
			'collection_cd'=>'number',
			'opac_flg'=>'string',
		));
		$this->setKey('bibid');
		$this->setSequenceField('bibid');
		$this->setIter('BiblioIter');
		
		$this->marc = new MarcStore;
		$this->marcRec = new MarcRecord;
	}
	function insert_el($biblio) {
		$this->lock();
		if (!isset($biblio['marc']) or !is_a($biblio['marc'], 'MarcRecord')) {
			return array(NULL, array(new FieldError('marc', T("No MARC record set"))));
		}
		list($bibid, $errors) = parent::insert_el($biblio);
		if ($errors) {
			return array($bibid, $errors);
		}
		$this->marc->put($bibid, $biblio['marc']);
		$this->unlock();
		return array($bibid, NULL);
	}
	function update_el($biblio) {
		$this->lock();
		if (!isset($biblio['bibid'])) {
			Fatal::internalError(T("No bibid set in biblio update"));
		}
		if (isset($biblio['marc']) and is_a($biblio['marc'], 'MarcRecord')) {
			$this->marc->put($biblio['bibid'], $biblio['marc']);
		}
		$r = parent::update_el($biblio);
		$this->unlock();
		return $r;
	}
	function deleteOne($bibid) {
		$this->lock();
		$imgs = new BiblioImages;
		$imgs->deleteByBibid($bibid);
		$this->marc->delete($bibid);
		parent::deleteOne($bibid);
		$this->unlock();
	}
	function deleteMatches($fields) {
		$this->lock();
		$rows = parent::getMatches($fields);
		while ($r = $rows->fetch_assoc()) {
			$this->deleteOne($r['bibid']);
		}
		$this->unlock();
	}
}

class BiblioIter extends Iter {
	public function __construct($rows) {
		parenr::__construct();
		$this->rows = $rows;
		$this->marc = new MarcStore;
	}
	function next() {
		$row = $this->rows->next();
		if (!$row)
			return NULL;
		$row['marc'] = $this->marc->get($row['bibid']);
		return $row;
	}
	function skip() {
		$this->rows->skip();
	}
	function count() {
		return $this->rows->count();
	}
}

