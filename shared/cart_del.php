<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  require_once(REL(__FILE__, "../classes/Cart.php"));
  $cart = getCart($_REQUEST['name']);
  if (isset($_REQUEST['id'])) {
    foreach ($_REQUEST['id'] as $id) {
      $cart->remove($id);
    }
  } elseif ($_REQUEST['clear'] == 'yes') {
    $cart->clear();
  }
  if (isset($_REQUEST['tab'])) {
    $tab = $_REQUEST['tab'];
  } else {
    $tab = 'opac';
  }
  header("Location: ".$cart->viewURL()."?tab=".U($tab));
  exit();
