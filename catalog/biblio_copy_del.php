<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "view";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Copies.php"));


  $bibid = $_GET["bibid"];
  $copyid = $_GET["copyid"];
  $barcode = $_GET["barcode"];

  $copies = new Copies;
  $copies->deleteOne($copyid);

  $msg = T("biblioCopyDelSuccess", array("barcode"=>$barcode));
  header("Location: ../shared/biblio_view.php?bibid=".U($bibid)."&msg=".U($msg));
  exit();
