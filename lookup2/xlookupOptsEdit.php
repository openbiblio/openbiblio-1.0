<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "lookup";
  $restrictInDemo = true;
  require_once("../shared/logincheck.php");

  require_once("../classes/LookupOpts.php");
  require_once("../classes/LookupQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../admin/lookup_edit_form.php?reset=Y");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $set = new LookupOpts();
  $set->setProtocol($_POST["protocol"]);
  $_POST["protocol"] = $set->getProtocol();
  $set->setMaxHits($_POST["maxHits"]);
  $_POST["maxHits"] = $set->getMaxHits();
  $set->setKeepDashes(isset($_POST["keepDashes"]));
  $_POST["keepDashes"] = $set->getKeepDashes();
  $set->setCallNmbrType($_POST["callNmbrType"]);
  $_POST["callNmbrType"] = $set->getCallNmbrType();
  $set->setAutoDewey($_POST["autoDewey"]);
  $_POST["autoDewey"] = $set->getAutoDewey();
  $set->setDefaultDewey($_POST["defaultDewey"]);
  $_POST["defaultDewey"] = $set->getDefaultDewey();
  $set->setAutoCutter($_POST["autoCutter"]);
  $_POST["autoCutter"] = $set->getAutoCutter();
  $set->setCutterType($_POST["cutterType"]);
  $_POST["cutterType"] = $set->getCutterType();
  $set->setCutterWord($_POST["cutterWord"]);
  $_POST["cutterWord"] = $set->getCutterWord();
  $set->setAutoCollect($_POST["autoCollect"]);
  $_POST["autoCollect"] = $set->getAutoCollect();
  $set->setFictionName($_POST["fictionName"]);
  $_POST["fictionName"] = $set->getFictionName();
  $set->setFictionCode($_POST["fictionCode"]);
  $_POST["fictionCode"] = $set->getFictionCode();
  $set->setFictionLoC($_POST["fictionLoC"]);
  $_POST["fictionLoC"] = $set->getFictionLoC();
  $set->setFictionDew($_POST["fictionDew"]);
  $_POST["fictionDew"] = $set->getFictionDew();

  if (!$set->validateData()) {
    //$pageErrors["sessionTimeout"] = $set->getSessionTimeoutError();
    //$pageErrors["itemsPerPage"] = $set->getItemsPerPageError();
    //$pageErrors["purgeHistoryAfterMonths"] = $set->getPurgeHistoryAfterMonthsError();
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/lookup_edit_form.php");
    exit();
  }


  #**************************************************************************
  #*  Update domain table row
  #**************************************************************************
  $setQ = new LookupQuery();
  $setQ->connect();
  if ($setQ->errorOccurred()) {
    $setQ->close();
    displayErrorPage($setQ);
  }
  if (!$setQ->update($set)) {
    $setQ->close();
    displayErrorPage($setQ);
  }
  $setQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  header("Location: ../admin/lookup_edit_form.php?reset=Y&updated=Y");
  exit();
?>
