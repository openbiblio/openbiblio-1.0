<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "deletedone";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Biblios.php"));
  require_once(REL(__FILE__, "../classes/Report.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));


  $bibid = $_GET["bibid"];
  $title = $_GET["title"];

  #**************************************************************************
  #*  Delete Bibliography
  #**************************************************************************
  $biblios = new Biblios();
  $biblios->deleteOne($bibid);

  if (isset($_REQUEST['rpt']) && $_REQUEST['rpt']) {
    $url = Report::link($_REQUEST['rpt'], T("Item, %title%, has been deleted.", array("title"=>$title)), $tab);
  } else {
    $url = '../catalog/index.php?msg='.U(T("Item, %title%, has been deleted.", array("title"=>$title)));
  }
  header('Location: '.$url);
