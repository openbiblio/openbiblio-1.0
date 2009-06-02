<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "checkin";
  $restrictInDemo = true;
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/BiblioStatusHist.php");
  require_once("../classes/BiblioStatusHistQuery.php");
  require_once("../classes/MemberAccountTransaction.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../circ/checkin_form.php?reset=Y");
    exit();
  }
  $barcode = trim($_POST["barcodeNmbr"]);

  #****************************************************************************
  #*  Edit input
  #****************************************************************************
  if (!ctypeAlnum($barcode)) {
    $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr1");
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/checkin_form.php");
    exit();
  }
  
  #****************************************************************************
  #*  Ready copy record
  #****************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if (!$copy = $copyQ->queryByBarcode($barcode)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }

  #****************************************************************************
  #*  Edit results
  #****************************************************************************
  $foundError = FALSE;
  if ($copyQ->getRowCount() == 0) {
    $foundError = true;
    $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr2");
  }

  if ($foundError == true) {
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/checkin_form.php");
    exit();
  }

  #****************************************************************************
  #*  Get daily late fee
  #****************************************************************************
  $dailyLateFee = $copyQ->getDailyLateFee($copy);
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }

  $copyQ->close();
  $saveMbrid = $copy->getMbrid();
  $saveDaysLate = $copy->getDaysLate();

  #**************************************************************************
  #*  Check hold list to see if someone has the copy on hold
  #**************************************************************************
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $hold = $holdQ->getFirstHold($copy->getBibid(),$copy->getCopyid());
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $holdQ->close();

  #**************************************************************************
  #*  Update copy status code
  #**************************************************************************
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if ($holdQ->getRowCount() > 0) {
    $copy->setStatusCd(OBIB_STATUS_ON_HOLD);
  } else {
    $copy->setStatusCd(OBIB_STATUS_SHELVING_CART);
  }
  $copy->setMbrid("");
  $copy->setDueBackDt("");
  if (!$copyQ->update($copy,true)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

  #**************************************************************************
  #*  Insert into biblio status history
  #**************************************************************************
  if ($saveMbrid != "") {
    $hist = new BiblioStatusHist();
    $hist->setBibid($copy->getBibid());
    $hist->setCopyid($copy->getCopyid());
    $hist->setStatusCd($copy->getStatusCd());
    $hist->setDueBackDt($copy->getDueBackDt());
    $hist->setMbrid($saveMbrid);

    $histQ = new BiblioStatusHistQuery();
    $histQ->connect();
    if ($histQ->errorOccurred()) {
      $histQ->close();
      displayErrorPage($histQ);
    }
    $histQ->insert($hist);
    if ($histQ->errorOccurred()) {
      $histQ->close();
      displayErrorPage($histQ);
    }
    $histQ->close();

    #**************************************************************************
    #*  Calc late fee if any
    #**************************************************************************
    if (($saveDaysLate > 0) and ($dailyLateFee > 0)) {
      $fee = $dailyLateFee * $saveDaysLate;
      $trans = new MemberAccountTransaction();
      $trans->setMbrid($saveMbrid);
      $trans->setCreateUserid($_SESSION["userid"]);
      $trans->setTransactionTypeCd("+c");
      $trans->setAmount($fee);
      $trans->setDescription($loc->getText("shelvingCartTrans",array("barcode" => $barcode)));

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
    }
  }

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  if ($holdQ->getRowCount() > 0) {
    header("Location: ../circ/hold_message.php?barcode=".U($barcode));
  } else {
    header("Location: ../circ/checkin_form.php");
  }
?>
    
