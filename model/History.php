<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));

class History extends DBTable {
  function History() {
    $this->DBTable();
    $this->setName('biblio_status_hist');
    $this->setFields(array(
      'histid'=>'number',
      'bibid'=>'number',
      'copyid'=>'number',
      'status_cd'=>'string',
      'status_begin_dt'=>'string',
    ));
    $this->setKey('histid');
    $this->setSequenceField('histid');
    $this->setForeignKey('bibid', 'biblio', 'bibid');
    $this->setForeignKey('copyid', 'biblio_copy', 'copyid');
  }
  function update_el($rec) {
    Fatal::internalError(T("Cannot update history entries"));
  }
  function insert_el($rec) {
    $rec['status_begin_dt'] = date('Y-m-d H:i:s');
    $this->db->lock();
    list($id, $errs) = parent::insert_el($rec);
    if (!$errs) {
      $copies = new Copies;
      $copies->update(array('copyid'=>$rec['copyid'], 'histid'=>$id));
      if (isset($rec['bookingid']) and $rec['bookingid']) {
        $bookings = new Bookings;
        $update = array('bookingid'=>$rec['bookingid']);
        if ($rec['status_cd'] == OBIB_STATUS_OUT) {
          $update['out_histid']=$id;
          $update['out_dt']=$rec['status_begin_dt'];
        } else {
          $update['ret_histid']=$id;
          $update['ret_dt']=$rec['status_begin_dt'];
        }
        $bookings->update($update);
      }
    }
    $this->db->unlock();
    return array($id, $errs);
  }
}
