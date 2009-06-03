<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../classes/DBTable.php"));

class MaterialFields extends DBTable {
  function MaterialFields() {
    $this->DBTable();
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
}