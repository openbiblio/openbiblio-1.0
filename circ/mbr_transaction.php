<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "circulation";
  $nav = "account";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/MemberAccounts.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));


  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_POST["mbrid"];
  if (isset($_POST["name"])) {
      $mbrName = urlencode($_GET["name"]);
  } else {
      $mbrName = "";
  }

  $acct = new MemberAccounts;
  list($id, $errs) = $acct->insert_el(array(
    'mbrid'=>$mbrid,
    'transaction_type_cd'=>$_POST["transaction_type_cd"],
    'amount'=>trim($_POST["amount"]),
    'description'=>trim($_POST["description"]),
  ));
  if ($errs) {
    $url = "../circ/mbr_account.php?mbrid=".U($mbrid)."&name=".U($mbrName);
    FieldError::backToForm($url, $errs);
  }

  $msg = T("Transaction successfully completed.");
  header("Location: ../circ/mbr_account.php?mbrid=".$mbrid."&name=".$mbrName."&reset=Y&msg=".U($msg));
  exit();
