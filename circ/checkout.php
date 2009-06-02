<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "circulation";
  $nav = "view";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Bookings.php"));

  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  $bookings = new Bookings;
  $err = $bookings->quickCheckout_e($_POST["barcodeNmbr"], array($_POST["mbrid"]));
  if ($err) {
    $pageErrors["barcodeNmbr"] = $err->toStr();
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/mbr_view.php?mbrid=".U($_POST["mbrid"]));
    exit();
  }

  header("Location: ../circ/mbr_view.php?mbrid=".U($_POST["mbrid"]));
