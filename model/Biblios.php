<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../classes/MarcQuery.php"));

class BiblioIter extends Iter {
  function BiblioIter($rows) {
    $this->rows = $rows;
    $this->marcq = new MarcQuery;
  }
  function next() {
    $row = $this->rows->next();
    if (!$row)
      return NULL;
    $row['marc'] = $this->marcq->get($row['bibid']);
    return $row;
  }
  function skip() {
    $this->rows->skip();
  }
  function count() {
    return $this->rows->count();
  }
}

class Biblios extends CoreTable {
  function Biblios() {
    $this->CoreTable();
    $this->setName('biblio');
    $this->setFields(array(
      'bibid'=>'number',
      'material_cd'=>'number',
      'collection_cd'=>'number',
      'opac_flg'=>'string',
    ));
    $this->setKey('bibid');
    $this->setSequenceField('bibid');

    $this->marcq = new MarcQuery;
  }
  function getOne($bibid) {
    $row = parent::getOne($bibid);
    if (!$row)
      return NULL;
    $row['marc'] = $this->marcq->get($bibid);
    return $row;
  }
  function getAll() {
    $rows = parent::getAll();
    return new BiblioIter($rows);
  }
  function getMatches($fields) {
    $rows = parent::getMatches($fields);
    return new BiblioIter($rows);
  }
  function insert_el($biblio) {
    $this->db->lock();
    if (!isset($biblio['marc']) or !is_a($biblio['marc'], 'MarcRecord')) {
      return array(NULL, array(new FieldError('marc', T("No MARC record set"))));
    }
    list($bibid, $errors) = parent::insert_el($biblio);
    if ($errors) {
      return array($bibid, $errors);
    }
    $this->marcq->put($bibid, $biblio['marc']);
    $this->db->unlock();
    return array($bibid, NULL);
  }
  function update_el($biblio) {
    $this->db->lock();
    if (!isset($biblio['bibid'])) {
      Fatal::internalError(T("No bibid set in biblio update"));
    }
    if (isset($biblio['marc']) and is_a($biblio['marc'], 'MarcRecord')) {
      $this->marcq->put($biblio['bibid'], $biblio['marc']);
    }
    $r = parent::update_el($biblio);
    $this->db->unlock();
    return $r;
  }
  function deleteOne($bibid) {
    $this->db->lock();
    $this->marcq->delete($bibid);
    parent::deleteOne($bibid);
    $this->db->unlock();
  }
  function deleteMatches($fields) {
    $this->db->lock();
    $rows = parent::getMatches($fields);
    while ($r = $rows->next()) {
      $this->deleteOne($r['bibid']);
    }
    $this->db->unlock();
  }
}
