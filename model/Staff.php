<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));

class Staff extends CoreTable {
  function Staff() {
    $this->DBTable();
    $this->setName('staff');
    $this->setFields(array(
      'userid'=>'number',
      'username'=>'string',
      'pwd'=>'string',
      'last_name'=>'string',
      'first_name'=>'string',
      'suspended_flg'=>'string',
      'admin_flg'=>'string',
      'circ_flg'=>'string',
      'circ_mbr_flg'=>'string',
      'catalog_flg'=>'string',
      'reports_flg'=>'string',
    ));
    $this->setKey('userid');
    $this->setSequenceField('userid');
  }
  function validate_el($rec, $insert) {
    $errors = array();
    foreach (array('username') as $req) {
      if ($insert and !isset($rec[$req])
          or isset($rec[$req]) and $rec[$req] == '') {
        $errors[] = new FieldError($req, T("Required field missing"));
      }
    }
    return $errors;
  }
}
