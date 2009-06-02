<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  require_once(REL(__FILE__, "../classes/Cart.php"));
  $name = $_REQUEST['name'];
  $cart = getCart($name);
  if (isset($_REQUEST['id'])) {
    foreach ($_REQUEST['id'] as $id) {
      if (!$cart->contains($id)) {
        $cart->add($id);
      }
    }
  }
  if (isset($_REQUEST['tab'])) {
    $tab = $_REQUEST['tab'];
  } else {
    $tab = 'opac';
  }
  header("Location: ".$cart->viewURL()."?tab=".U($tab));
  exit();
