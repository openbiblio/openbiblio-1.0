<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class Stock extends DBTable {
  function Stock() {
    $this->DBTable();
    $this->setName('biblio_stock');
    $this->setFields(array(
      'bibid'=>'number',
      'count'=>'number',
      'vendor'=>'string',
      'fund'=>'string',
      'price'=>'string',
    ));
    $this->setKey('bibid');
    $this->setForeignKey('bibid', 'biblio', 'bibid');
  }
  function validate_el($rec, $insert) {
    $errors = array();
    foreach (array('bibid', 'count') as $req) {
      if ($insert and !isset($rec[$req])
          or isset($rec[$req]) and $rec[$req] === '') {
        $errors[] = new FieldError($req, T("Required field missing"));
      }
    }
    return $errors;
  }
  function getUnderStocked() {
    $sql = "SELECT IFNULL(s.count, 0) count, b.*, "
           . "c.description collection, cd.restock_threshold, "
           . "s.subfield_data title "
           . "FROM collection_dist cd, biblio b, collection_dm c, "
           . "LEFT JOIN biblio_stock s ON s.bibid=b.bibid "
           . "LEFT JOIN biblio_field f ON f.bibid=b.bibid "
           . "LEFT JOIN biblio_subfield s ON s.fieldid=f.fieldid "
           . "WHERE b.collection_cd=cd.code AND c.code=cd.code "
           . "AND f.tag='245' and s.subfield_cd='a' "
           . "AND IFNULL(s.count, 0) <= cd.restock_threshold ";
    return $this->db->select($sql);
  }
}
