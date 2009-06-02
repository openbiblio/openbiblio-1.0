<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "bulk_delete";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Copies.php"));
  require_once(REL(__FILE__, "../model/Biblios.php"));


  function getForm($vars) {
    foreach (array_keys($vars) as $k) {
      if (isset($_REQUEST[$k])) {
        $vars[$k] = $_REQUEST[$k];
      }
    }
    return $vars;
  }

  $copies = new Copies;
  $biblios = new Biblios;

  $form = getForm(array(
    'posted' => false,
    'confirmed' => false,
    'del_items' => false,
    'del_copyids' => array(),
    'del_bibids' => array(),
    'barcodes' => '',
  ));
  if (!$form['posted']) {
    showForm(array('del_items'=>1, 'barcodes'=>''));
  } else {
    if ($form['confirmed']) {
      foreach ($form['del_copyids'] as $copyid) {
        $copies->deleteOne($copyid);
      }
      foreach ($form['del_bibids'] as $bibid) {
        $biblios->deleteOne($bibid);
      }
      $msg = T("%copy% copies and %item% items deleted.", array('copy'=>count($form['del_copyids']), 'item'=>count($form['del_bibids'])));
      header("Location: ../catalog/biblio_bulk_del.php?msg=".U($msg));
    } else {
      doConfirm($form['barcodes'], $form['del_items']);
    }
  }

  function doConfirm($barcode_list, $del_items) {
    global $copies;
    $barcodes = array();
    foreach (explode("\n", $barcode_list) as $b) {
      if (trim($b) != "") {
        $barcodes[] = trim($b);
      }
    }
    list($del_copyids, $bibids, $errors) = $copies->lookupBulk_el($barcodes);
    if ($errors) {
      showForm(array('del_items'=>$del_items, 'barcodes'=>$barcode_list), $errors);
      exit(0);
    }
    if ($del_items) {
      $del_bibids = $copies->lookupNoCopies($bibids, $del_copyids);
    } else {
      $del_bibids = array();
    }
    showConfirm($del_copyids, $del_bibids);
  }

  function showConfirm($del_copyids, $del_bibids) {
    global $tab, $nav;
    Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
    echo '<center><form method="post" action="../catalog/biblio_bulk_del.php">';
    echo '<p>'.T('biblioBulkDelWantToDel', array('copy'=>H(count($del_copyids)), 'item'=>H(count($del_bibids)))).'</p>';
    echo '<input type="hidden" name="posted" value="1" />';
    echo '<input type="hidden" name="confirmed" value="1" />';
    foreach ($del_copyids as $id) {
      echo '<input type="hidden" name="del_copyids[]" value="'.H($id).'" />';
    }
    foreach ($del_bibids as $id) {
      echo '<input type="hidden" name="del_bibids[]" value="'.H($id).'" />';
    }
    echo '<input type="submit" class="button" value="'.T("Delete").'" />  ';
    echo '<a href="../catalog/biblio_bulk_del.php" class="small_button">'.T("Cancel").'</a>';
    echo '</form></center>';
    Page::footer();
  }
  function showForm($vars, $errors=array()) {
    global $tab, $nav;
    $focus_form_name=bulk_delete;
    $focus_form_field=barcodes;
    Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
    echo '<h2>'.T("Bulk Delete").'</h2>';
    if (isset($_REQUEST['msg'])) {
      echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
    }
    echo '<p>'.T("Enter barcodes to delete below, one per line.").'</p>';
    foreach ($errors as $e) {
      echo '<p class="error">'.H($e->toStr()).'</p><br />';
    }
    echo '<form name="bulk_delete" method="post" action="../catalog/biblio_bulk_del.php">';
    echo '<input type="hidden" name="posted" value="1" />';
    echo '<textarea name="barcodes" rows="12">'.H($vars['barcodes']).'</textarea>';
    echo '<p><input type="checkbox" name="del_items" value="1" ';
    if ($vars['del_items']) {
      echo 'checked="checked" ';
    }
    echo  '/>'.T("Delete items if all copies are deleted.").'</p>';
    echo '<input type="submit" class="button" value="'.T("Submit").'" />';
    echo '</form>';
    Page::footer();
  }
