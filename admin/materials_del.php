<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/MaterialTypes.php"));

  #****************************************************************************
  #*  Checking for query string.  Go back to material type list if none found.
  #****************************************************************************
  if (!isset($_GET["code"])){
    header("Location: ../admin/materials_list.php");
    exit();
  }
  $code = $_GET["code"];
  $description = $_GET["desc"];

  $mattypes = new MaterialTypes;
  $mattypes->deleteOne($code);

  $msg = T("Material type, %desc%, has been deleted.", array('desc'=>$description));
  header("Location: ../admin/materials_list.php?msg=".U($msg));
