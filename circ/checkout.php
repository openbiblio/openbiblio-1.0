<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "view";
  $restrictInDemo = true;
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/BiblioStatusHist.php");
  require_once("../classes/BiblioStatusHistQuery.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post or get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) != 0) {
      $_POST = $_GET;
  }
  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }
  $barcode = trim($_POST["barcodeNmbr"]);
  $mbrid = trim($_POST["mbrid"]);
  $mbrQ = new MemberQuery;
  $mbrQ->connect();
  $mbr = $mbrQ->get($mbrid);
  $mbrClassification = $mbr->getClassification();
  $mbrQ->close();

  if(isset($_POST["renewal"])) {
      $renewal = true;
  }
  else {
      $renewal = false;
  }

  #****************************************************************************
  #*  Make sure member does not have outstanding balance due
  #****************************************************************************
  if (OBIB_BLOCK_CHECKOUTS_WHEN_FINES_DUE) {
    $acctQ = new MemberAccountQuery();
    $acctQ->connect();
    if ($acctQ->errorOccurred()) {
      $acctQ->close();
      displayErrorPage($acctQ);
    }
    $balance = $acctQ->getBalance($mbrid);
    if ($acctQ->errorOccurred()) {
      $acctQ->close();
      displayErrorPage($acctQ);
    }
    $acctQ->close();
    if ($balance > 0) {
      $pageErrors["barcodeNmbr"] = $loc->getText("checkoutBalErr");
      $postVars["barcodeNmbr"] = $barcode;
      $_SESSION["postVars"] = $postVars;
      $_SESSION["pageErrors"] = $pageErrors;
      header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
      exit();
    }
  }

  #****************************************************************************
  #*  Edit input
  #****************************************************************************
  if (!ctypeAlnum(trim($barcode))) {
    $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr1");
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
    exit();
  }

  #****************************************************************************
  #*  Read copy record
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
  $foundError = false;
  if ($copyQ->getRowCount() == 0) {
    $foundError = true;
    $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr2");
  } else {

    $daysDueBack = $copyQ->getDaysDueBack($copy);
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }

    if ($copy->getStatusCd() == OBIB_STATUS_OUT) {
      //Item already checked out, let's see if it's a renewal
      if($renewal) {
        //Check to see if the renewal limit has been reached
        $reachedLimit = $copyQ->hasReachedRenewalLimit($mbrid,$mbrClassification,$copy);
        if ($copyQ->errorOccurred()) {
          $copyQ->close();
          displayErrorPage($copyQ);
        }
        if ($reachedLimit) {
          $foundError = TRUE;
          $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr7",array("barcode"=>$barcode));
        }
        else {
          if($copy->getDaysLate() > 0) {
            $foundError = TRUE;
            $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr8",array("barcode"=>$barcode));
          }
          else {
            //We can renew this item!
            $copy->setRenewalCount($copy->getRenewalCount() + 1);
          }
        }
      }
      else {
        //copy is already checked out by someone else
        $foundError = TRUE;
        $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr3",array("barcode"=>$barcode));
      }
    } else {

      //Not a renewal, clearing renewal count
      $copy->setRenewalCount(0);

      // check days due back
      // some collections will have days due back set to 0 so that those items can not be checked out.
      if ($daysDueBack <= 0) {
        $foundError = true;
        $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr4",array("barcode"=>$barcode));
      } else {
        // check to see if collection max has been reached
        $reachedLimit = $copyQ->hasReachedCheckoutLimit($mbrid,$mbrClassification,$copy->getBibid());
        if ($copyQ->errorOccurred()) {
          $copyQ->close();
          displayErrorPage($copyQ);
        }
        if ($reachedLimit) {
          $foundError = TRUE;
          $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr6");
        }
      }
    }
  }

  #**************************************************************************
  #*  return to member view if there are checkout errors to show
  #**************************************************************************
  if ($foundError == TRUE) {
    $copyQ->close();
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
    exit();
  }

  #**************************************************************************
  #*  Show hold edit if bibliography is currently on hold and
  #*  current member != first member in hold queue
  #**************************************************************************
  if ($copy->getStatusCd() == OBIB_STATUS_ON_HOLD) {
    // need to close copyQ connection so we can call hold functions
    $copyQ->close();
    // check copy hold queue
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
    // make sure hold still exists.  if not continue on with checkout
    if ($holdQ->getRowCount() > 0) {
      $holdAge = time() - strtotime($hold->getHoldBeginDt());
      $tooOld = false;
      $secondsPerDay = 86400;
      if (OBIB_HOLD_MAX_DAYS > 0 and $holdAge > OBIB_HOLD_MAX_DAYS*$secondsPerDay) {
        $tooOld = true;
      }
      if (!$tooOld and $mbrid != $hold->getMbrid()) {
        // show error if member who placed hold is not current member
        $holdQ->close();
        $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr5",array("barcode"=>$barcode));
        $postVars["barcodeNmbr"] = $barcode;
        $_SESSION["postVars"] = $postVars;
        $_SESSION["pageErrors"] = $pageErrors;
        header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
        exit();
      } else {
        // need to remove hold and continue on to checkout
        $holdQ->delete($hold->getBibid(),$hold->getCopyid(),$hold->getHoldid());
        if ($holdQ->errorOccurred()) {
          $holdQ->close();
          displayErrorPage($holdQ);
        }
        $holdQ->close();
      }
    }
    // need to reestablish copyQ connection so we can update status
    $copyQ->connect();
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
  }

  #**************************************************************************
  #*  Update copy status code
  #**************************************************************************
  // we need to also insert into status history table
  $copy->setStatusCd(OBIB_STATUS_OUT);
  $copy->setMbrid($_POST["mbrid"]);
  $copy->setDueBackDt($daysDueBack);
  if (!$copyQ->update($copy,true)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

  #**************************************************************************
  #*  Insert into biblio status history
  #**************************************************************************
  $hist = new BiblioStatusHist();
  $hist->setBibid($copy->getBibid());
  $hist->setCopyid($copy->getCopyid());
  $hist->setStatusCd($copy->getStatusCd());
  $hist->setStatusBeginDt($copy->getStatusBeginDt());
  $hist->setDueBackDt($copy->getDueBackDt());
  $hist->setMbrid($copy->getMbrid());
  $hist->setRenewalCount($copy->getRenewalCount());

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
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
?>
