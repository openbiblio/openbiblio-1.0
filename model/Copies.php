<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../model/History.php"));

class Copies extends CoreTable {
  function Copies() {
    $this->CoreTable();
    $this->setName('biblio_copy');
    $this->setFields(array(
      'bibid'=>'number',
      'copyid'=>'number',
      'barcode_nmbr'=>'string',
      'copy_desc'=>'string',
      #'vendor'=>'string',
      #'fund'=>'string',
      #'price'=>'string',
      #'expiration'=>'string',
      'histid'=>'number',
    ));
    $this->setKey('copyid');
    $this->setSequenceField('copyid');
    $this->setForeignKey('bibid', 'biblio', 'bibid');
    $this->setForeignKey('histid', 'biblio_status_hist', 'histid');

    $this->custom = new DBTable;
    $this->custom->setName('biblio_copy_fields');
    $this->custom->setFields(array(
      'copyid'=>'string',
      'code'=>'string',
      'data'=>'string',
    ));
    $this->custom->setKey('copyid', 'code');
   }

  function getNextCopy() {
  	$sql = $this->db->mkSQL("select max(copyid) as nextCopy from biblio_copy");
  	$nextCopy = $this->db->select1($sql);
  	//print_r($nextCopy);
  	return $nextCopy["nextCopy"]+1;
  }

  function insert_el($copy) {
    $this->db->lock();
    list($id, $errors) = parent::insert_el($copy);
    if (!$errors) {
      $history = new History;
      $history->insert(array(
        'bibid'=>$copy['bibid'], 'copyid'=>$id, 'status_cd'=>'in',
      ));
    }
    $this->db->unlock();
    return array($id, $errors);
  }
  function validate_el($copy, $insert) {
    $errors = array();
    foreach (array('bibid', 'barcode_nmbr') as $req) {
      if ($insert and !isset($copy[$req])
          or isset($copy[$req]) and $copy[$req] == '') {
        $errors[] = new FieldError($req, T("Required field missing"));
      }
    }
    /* Check for duplicate barcodes */
    if (isset($copy['barcode_nmbr'])) {
      $sql = $this->db->mkSQL("select count(*) count from biblio_copy "
                          . "where barcode_nmbr=%Q ", $copy['barcode_nmbr']);
      if (isset($copy['copyid'])) {
        $sql .= $this->db->mkSQL("and not copyid=%N ", $copy['copyid']);
      }
      $duplicates = $this->db->select1($sql);
      if ($duplicates['count'] > 0) {
        $errors[] = new FieldError('barcode_nmbr', T("Barcode number already in use."));
      }
    }
    return $errors;
  }
  function normalizeBarcode($barcode) {
    return ereg_replace('^([A-Za-z]+)?0*(.*)', '\\1\\2', $barcode);
  }
  function getByBarcode($barcode) {
    $rows = $this->getMatches(array('barcode_nmbr'=>$barcode));
    if ($rows->count() == 0) {
      $barcode = $this->normalizeBarcode($barcode);
      $rows = $this->getMatches(array('barcode_nmbr'=>$barcode));
    }
    if ($rows->count() == 0) {
      return NULL;
    } else if ($rows->count() == 1) {
      return $rows->next();
    } else {
      Fatal::internalError(T("Duplicate barcode: %barcode%", array('barcode'=>$barcode)));
    }
  }
  function getMemberCheckouts($mbrid) {
    $sql = "select bc.* "
           . "from biblio_copy bc, booking bk, booking_member bkm "
           . "where bc.histid=bk.out_histid "
           . "and bkm.bookingid=bk.bookingid ";
    $sql .= $this->db->mkSQL("and bkm.mbrid=%N ", $mbrid);
    return $this->db->select($sql);
  }
  function lookupBulk_el($barcodes) {
    $copyids = array();
    $bibids = array();
    $errors = array();
    foreach ($barcodes as $b) {
      $copy = $this->getByBarcode($b);
      if (!$copy) {
        $errors[] = new Error(T("No copy with barcode %barcode%", array('barcode'=>$b)));
      } else {
        if (!in_array($copy['copyid'], $copyids)) {
          $copyids[] = $copy['copyid'];
        }
        if (!in_array($copy['bibid'], $bibids)) {
          $bibids[] = $copy['bibid'];
        }
      }
    }
    return array($copyids, $bibids, $errors);
  }
  function lookupNoCopies($bibids, $del_copyids) {
    $no_copies = array();
    foreach ($bibids as $bibid) {
      $has_copies = false;
      $copies = $this->getMatches(array('bibid'=>$bibid));
      while ($c = $copies->next()) {
        if (!in_array($c['copyid'], $del_copyids)) {
          $has_copies = true;
          break;
        }
      }
      if (!$has_copies) {
        $no_copies[] = $bibid;
      }
    }
    return $no_copies;
  }
  function getShelvingCart() {
    $sql = "select bc.* "
           . "from biblio_copy bc, biblio_status_hist bsh "
           . "where bc.histid=bsh.histid "
           . $this->db->mkSQL("and bsh.status_cd=%Q ",
               OBIB_STATUS_SHELVING_CART);
    return $this->db->select($sql);
  }
  function checkin($bibids,$copyids) {
    $this->db->lock();
    $history = new History;
    for ($i=0; $i < count($bibids); $i++) {
     $hist = array(
       'bibid'=>$bibids[$i],
       'copyid'=>$copyids[$i],
       'status_cd'=>OBIB_STATUS_IN,
     );
     $history->insert($hist);
    }
    $this->db->unlock();
  }
  function massCheckin() {
    $this->db->lock();
    $cart = $this->getShelvingCart();
    $bibids = array();
    $copyids = array();
    while ($copy = $cart->next()) {
      array_push($bibids, $copy['bibid']);
      array_push($copyids, $copy['copyid']);
    }
    $this->checkin($bibids, $copyids);
    $this->db->unlock();
  }
  function getCustomFields($copyid) {
    return $this->custom->getMatches(array('copyid'=>$copyid));
  }

  function setCustomFields($copyid, $customFldsarr) {
    $this->custom->deleteMatches(array('copyid'=>$copyid));
    foreach ($customFldsarr as $code => $data) {
      $fields= array(
        copyid=>$copyid ,
        code=>$code,
        data=>$data
      );
      $this->custom->insert($fields);
    }
  }
}