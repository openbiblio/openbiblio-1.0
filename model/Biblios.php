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

	protected function validate_el($rec, $insert) { /*return array();*/ }

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
	function xupdate ($updtData) {
		/** currently does not post new entries to database, do NOT use 
		 *  TODO should be fixed to replace mess in biblioChange.php
		 */

		## get user screen content
		$updts = $updtData['marc'];
		## get database content
		$crntRec = $this->marc->get($updtData['bibid']);
		//$flds = $crntRec->fields;
		function updateExisting($updts, $crntRec) {
			## scan for differences between two data sets
			## all (but only) fields present in DB are considered here.
			## mark user fields when/if scanned
			for($i=0; $i<count($crntRec->fields); $i++) {
				$field = $crntRec->fields[$i];
				foreach ($crntRec->fields[$i]->subfields as $n=>$subfld) {
					$tag = $field->tag.'$'.$subfld->identifier;
					if ($updts[$tag]) {
						$newData = $updts[$tag]['value'];
					} else {
						$tag = $field->tag.'$'.$subfld->identifier.'$'.($n+1);
						$newData = $updts[$tag]['value'];
					}
					$updts[$tag]['scanned']='yes';
					if ($newData != $subfld->data) {
						if ($crntRec->fields[$i]->subfields[$n]->data) {
	          		$crntRec->fields[$i]->subfields[$n]->data = $newData;
						}
					}
				}
			}
			return array($updts, $crntRec);
		}
		function addNewMarcFlds ($updts, $crntRec){
			## step B. review user dataset for fields not scanned above
			foreach ($updts as $key=>$value) {
				if (!array_key_exists('scanned', $value)) {
					list($tag, $suf, $seq) = explode('$', $key);
					$mrcRec = new MarcField($tag, $suf);
	        $crntRec->fields[] = $mrcRec;
				}
			}
			return array($updts, $crntRec);
		}

		list($updts, $crntRec) = updateExisting($updts, $crntRec);
		list($updts, $crntRec) = addNewMarcFlds($updts, $crntRec);
		list($updts, $crntRec) = updateExisting($updts, $crntRec);
//return "stopping as requested";

		$newBiblio = $updtData; ## has needed structure
		$newBiblio['marc'] = $crntRec; ## replaces Marc portion
		parent::update($newBiblio);
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

