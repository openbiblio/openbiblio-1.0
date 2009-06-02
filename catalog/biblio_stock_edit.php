<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/Stock.php"));


  if (count($_POST) == 0 or !isset($_POST['bibid'])) {
    header("Location: ../catalog/index.php");
    exit();
  }

  $stock = new Stock();
  $rows = $stock->getMatches(array('bibid'=>$_POST['bibid']));
  if ($rows->count() == 0) {
    $stock->insert(array('bibid'=>$_POST['bibid'], 'count'=>0));
  }
  $rec = $stock->getOne($_POST['bibid']);

  if (isset($_POST['add']) or isset($_POST['remove'])) {
    if (!isset($_POST['items']) or !is_numeric($_POST['items'])) {
      header("Location: ../shared/biblio_view.php?bibid=".U($rec['bibid'])."&msg=".U(T("Number of items is required.")));
      exit();
    }
    if (isset($_POST['add'])) {
      $rec['count'] += $_POST['items'];
    } else {
      $rec['count'] -= $_POST['items'];
    }
    if ($rec['count'] < 0) {
      header("Location: ../shared/biblio_view.php?bibid=".U($rec['bibid'])."&msg=".U(T("Insufficient stock")));
      exit();
    }
    $stock->update($rec);
  } else {
    $rec = array();
    $fields = array('bibid', 'price', 'vendor', 'fund');
    foreach ($fields as $f) {
      if (isset($_POST[$f])) {
        $rec[$f] = trim($_POST[$f]);
      }
    }
    $errors = $stock->update_el($rec);
    if ($errors) {
      FieldError::backToForm('../catalog/biblio_stock_edit_form.php', $errors);
    }
  }

  $msg = T("Stock info changed");
  header("Location: ../shared/biblio_view.php?bibid=".U($rec['bibid'])."&msg=".U($msg));
  exit();
