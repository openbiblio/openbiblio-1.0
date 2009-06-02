<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  #****************************************************************************
  #*  Checking for get vars.
  #****************************************************************************
  $bibid = $_GET["bibid"];
  $holdid = $_GET["holdid"];
  $mbrid = $_GET["mbrid"];
  if ($mbrid == "") {
    $tab = "cataloging";
    $nav = "holds";
    $returnNav = "../catalog/biblio_hold_list.php?bibid=".U($bibid);
  } else {
    $tab = "circulation";
    $nav = "view";
    $returnNav = "../circ/mbr_view.php?mbrid=".U($mbrid);
  }
  $restrictInDemo = TRUE;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Holds.php"));

  $holds = new Holds;
  $holds->deleteOne($holdid);

  $msg = T("Hold request was successfully deleted.");
  header("Location: ".$returnNav."&msg=".U($msg));
