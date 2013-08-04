<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Queryi.php"));
require_once(REL(__FILE__, "../classes/Marc.php"));

# Note: this doesn't follow the standard table API.
# It's really only intended to be used by Biblios.php

class MarcStore extends Queryi{
	public function __construct () {
		parent::__construct();
	}
	function delete($bibid) {
		$this->lock();
		$subsql = $this->mkSQL("delete from biblio_subfield where bibid=%N ", $bibid);
		$this->act($subsql);
		$fldsql = $this->mkSQL("delete from biblio_field where bibid=%N ", $bibid);
		$this->act($fldsql);
		$this->unlock();
	}
	public function fetchMarcFlds ($bibid) {
		$sql = $this->mkSQL("select * "
			. "from biblio_field as bf "
			. "left join biblio_subfield as bs "
			. "on bf.fieldid=bs.fieldid "
			. "where bf.bibid=%N "
			. "order by bf.seq, bf.fieldid, bs.seq ",
			$bibid);
		$rows = $this->select($sql);
		return $rows;
	}
	function get($bibid) {
		$rows = $this->fetchMarcFlds($bibid);
		if ($rows->num_rows == 0) {
			return NULL;
		}

		$rec = new MarcRecord();
		$fieldid = NULL;
		$field = NULL;
		while (($row = $rows->fetch_assoc()) !== NULL) {
			if ($row['fieldid'] != $fieldid) {
				if ($field) {
					array_push($rec->fields, $field);
				}
				$field = NULL;
				$fieldid = $row['fieldid'];
				if (preg_match('/000|LDR/i', $row['tag'])) {
					$err = $rec->setLeader($row['field_data']);
					assert('!$err');
					continue;
				} else if (substr($row['tag'], 0, 2) == '00') {
					$field = new MarcControlField($row['tag'], $row['field_data']);
				} else {
					if (strlen($row['ind1_cd']) == 0) {
						$row['ind1_cd'] = ' ';
					}
					 if (strlen($row['ind2_cd']) == 0) {
						$row['ind2_cd'] = ' ';
					}
				 $field = new MarcDataField($row['tag'], $row['ind1_cd'] . $row['ind2_cd']);
				}
				$field->fieldid = $fieldid;
			}
			if (is_a($field, 'MarcDataField')) {
				$sf = new MarcSubfield($row['subfield_cd'], $row['subfield_data']);
				$sf->subfieldid = $row['subfieldid'];
				array_push($field->subfields, $sf);
			}
		}
		if ($field) {
			array_push($rec->fields, $field);
		}
		return $rec;
	}

	function put($bibid, $record) {
		$this->lock();
		$this->delete($bibid);
		$fldseq = 1;
		if (!$this->_putControl($bibid, $fldseq, "LDR", $record->getLeader())) {
			$this->unlock();
			return false;
		}
		$fields = $record->getFields();
//print_r($rcd);
		foreach ($fields as $field) {
			$fldseq += 1;
			if (is_a($field, 'MarcControlField')) {
				if (!$this->_putControl($bibid, $fldseq, $field->tag, $field->data)) {
					$this->unlock();
					return false;
				}
			} else if (is_a($field, 'MarcDataField')){
				$fieldid = $this->_putData($bibid, $fldseq, $field->tag, $field->indicators);
				if (!$fieldid) {
					$this->unlock();
					return false;
				}
				$subseq = 1;
				foreach ($field->subfields as $subf) {
					if (!$this->_putSub($bibid, $fieldid, $subseq++, $subf->identifier, $subf->data)) {
						$this->unlock();
						return false;
					}
				}
			}
		}
		$this->unlock();
		return true;
	}
	function _putControl($bibid, $seq, $tag, $data) {
		$sql = $this->mkSQL("insert into biblio_field values "
			. "(%N, NULL, %N, %Q, NULL, NULL, %Q, NULL) ",
			$bibid, $seq, $tag, $data);
//		$this->act($sql);
		return $this->getInsertID();
	}
	function _putData($bibid, $seq, $tag, $ind) {
		$sql = $this->mkSQL("insert into biblio_field values "
			. "(%N, NULL, %N, %Q, %Q, %Q, NULL, NULL) ",
			$bibid, $seq, $tag, $ind{0}, $ind{1});
//		$this->act($sql);
		return $this->getInsertID();
	}
	function _putSub($bibid, $fieldid, $seq, $identifier, $data) {
		$sql = $this->mkSQL("insert into biblio_subfield values "
			. "(%N, %N, NULL, %N, %Q, %Q) ",
			$bibid, $fieldid, $seq, $identifier, $data);
//		$this->act($sql);
		return $this->getInsertID();
	}
}
