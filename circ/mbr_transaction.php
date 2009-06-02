<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

  $tab = "circulation";
  $nav = "account";
  $restrictInDemo = true;
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/MemberAccountTransaction.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

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

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  $msg = $loc->getText("mbrTransactionSuccess");
  $msg = urlencode($msg);
  header("Location: ../circ/mbr_account.php?mbrid=".$mbrid."&name=".$mbrName."&reset=Y&msg=".$msg);
  exit();
?>