<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
/**   required change 6Sept2013 - FL
 *
ALTER TABLE `material_fields` CHANGE `form_type` `form_type`
ENUM( 'text', 'textarea', 'date', 'datetime', 'month', 'number', 'url',
'tel', 'email', 'search', 'color', 'password', 'range' )
NOT NULL DEFAULT 'text';
ALTER TABLE `material_fields` ADD `validation_cd` VARCHAR( 10 ) NULL AFTER `form_type`;
 *
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

/**
 * providess an API to the Material_fields database table
 * @author Micah Stetson
 */

class MaterialFields extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('material_fields');
		$this->setFields(array(
			'material_field_id'=>'number',
			'material_cd'=>'number',
			'tag'=>'string',
			'subfield_cd'=>'string',
			'position'=>'number',
			'label'=>'string',
			'form_type'=>'string',
			'validation_cd'=>'string',
			'required'=>'string',
			'repeatable'=>'string',
			'search_results'=>'string',
		));
		$this->setKey('material_field_id');
		$this->setSequenceField('material_field_id');
		$this->setForeignKey('material_cd', 'material_type_dm', 'code');
	}

	protected function validate_el($rec, $insert) { /*return array();*/ }

	public function getDisplayInfo ($nmbr) {
		$media = [];
		$set = $this->getAll('material_cd,position');
		while ($row = $set->fetch_assoc()) {
      if (($nmbr == 'all') || ($row['material_cd'] == $nmbr)) {
				$media[$row['material_cd']][$row['position']] =
					array('tag'=>$row['tag'],'suf'=>$row['subfield_cd'],'lbl'=>$row['label'],'row'=>$row['position']);
			}
		}
		return $media;
	}
	public function getMediaTags ($code) {
		$tags = array();
		$set = $this->getAll('material_cd,position');
		while ($row = $set->fetch_assoc()) {
			if ($row['material_cd'] == $code) {
				$n = 1;
				do {
					$tag = $row['tag'].'$'.$row['subfield_cd'];
					if ($row['repeatable'] > 0) $tag .= '$'.$n;
					$tags[$tag] = array('line'=>$row['position'],
			 												'lbl'=>$row['label'],
															'required'=>$row['required'],
															'repeatable'=>$row['repeatable'],
															'seq'=>$row['seq'],
															'form_type'=>$row['form_type'],
															'validation_cd'=>$row['validation_cd']
															);
					$n++;
				} while ($n<=$row['repeatable']);
			}
		}
		return $tags;
	}
}
