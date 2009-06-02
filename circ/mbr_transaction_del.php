<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "circulation";
  $nav = "account";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../classes/MemberAccountTransaction.php"));
  require_once(REL(__FILE__, "../classes/MemberAccountQuery.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));


  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_GET["mbrid"];
  $transid = $_GET["transid"];

  #**************************************************************************
  #*  Delete member transaction
  #**************************************************************************
  $transQ = new MemberAccountQuery();
  $transQ->connect();
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  $trans = $transQ->delete($mbrid,$transid);
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  $transQ->close();

  $msg = T("Transaction successfully deleted.");
  header("Location: ../circ/mbr_account.php?mbrid=".$mbrid."&reset=Y&msg=".U($msg));
  exit();
