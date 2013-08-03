<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../classes/DBTable.php"));

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
			'required'=>'string',
			'repeatable'=>'string',
			'search_results'=>'string',
		));
		$this->setKey('material_field_id');
		$this->setSequenceField('material_field_id');
		$this->setForeignKey('material_cd', 'material_type_dm', 'code');
	}

	protected function validate_el($rec, $insert) { /*return array();*/ }

	function getDisplayInfo ($nmbr) {
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
	function getMediaTags ($code) {
		$tags = array();
		$set = $this->getAll('material_cd,position');
		while ($row = $set->fetch_assoc()) {
			if ($row['material_cd'] == $code) {
//echo "repeatable====>";print_r($row['repeatable']);echo"<br />\n";
				for ($n=0; $n<=$row['repeatable']; $n++){
					$tag = $row['tag'].'$'.$row['subfield_cd'];
					if ($n>0) $tag .= '#'.$n;
//echo "tag====>{$tag}<br />\n";
					$tags[$tag] = array('line'=>$row['position'],
			 										 'lbl'=>$row['label'],
													 'required'=>$row['required'],
													 'repeat'=>$row['repeatable'],
													 'form_type'=>$row['form_type']);
				}
			}
		}
		return $tags;
	}
}
