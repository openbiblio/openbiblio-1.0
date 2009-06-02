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

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $trans = new MemberAccountTransaction();
  $trans->setMbrid($mbrid);
  $trans->setCreateUserid($_SESSION["userid"]);
  $trans->setTransactionTypeCd($_POST["transactionTypeCd"]);
  $_POST["transactionTypeCd"] = $trans->getTransactionTypeCd();
  $trans->setAmount($_POST["amount"]);
  $_POST["amount"] = $trans->getAmount();
  $trans->setDescription($_POST["description"]);
  $_POST["description"] = $trans->getDescription();
  $validData = $trans->validateData();
  if (!$validData) {
    $pageErrors["amount"] = $trans->getAmountError();
    $pageErrors["description"] = $trans->getDescriptionError();
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/mbr_account.php?mbrid=".$mbrid."&name=".$mbrName);
    exit();
  }

  #**************************************************************************
  #*  Insert new member transaction
  #**************************************************************************
  $transQ = new MemberAccountQuery();
  $transQ->connect();
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  $trans = $transQ->insert($trans);
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  $transQ->close();

  $msg = T("Transaction successfully completed.");
  header("Location: ../circ/mbr_account.php?mbrid=".$mbrid."&name=".$mbrName."&reset=Y&msg=".U($msg));
  exit();
