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

  $tab = "admin";
  $nav = "settings";
  $restrictInDemo = true;
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Settings.php");
  require_once("../classes/SettingsQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../admin/settings_edit_form.php?reset=Y");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $set = new Settings();
  $set->setLibraryName($_POST["libraryName"]);
  $_POST["libraryName"] = $set->getLibraryName();
  $set->setLibraryImageUrl($_POST["libraryImageUrl"]);
  $_POST["libraryImageUrl"] = $set->getLibraryImageUrl();
  $set->setUseImageFlg(isset($_POST["isUseImageSet"]));
  $set->setLibraryHours($_POST["libraryHours"]);
  $_POST["libraryHours"] = $set->getLibraryHours();
  $set->setLibraryPhone($_POST["libraryPhone"]);
  $_POST["libraryPhone"] = $set->getLibraryPhone();
  $set->setLibraryUrl($_POST["libraryUrl"]);
  $_POST["libraryUrl"] = $set->getLibraryUrl();
  $set->setOpacUrl($_POST["opacUrl"]);
  $_POST["opacUrl"] = $set->getOpacUrl();
  $set->setSessionTimeout($_POST["sessionTimeout"]);
  $_POST["sessionTimeout"] = $set->getSessionTimeout();
  $set->setItemsPerPage($_POST["itemsPerPage"]);
  $_POST["itemsPerPage"] = $set->getItemsPerPage();
  $set->setPurgeHistoryAfterMonths($_POST["purgeHistoryAfterMonths"]);
  $_POST["purgeHistoryAfterMonths"] = $set->getPurgeHistoryAfterMonths();
  $set->setBlockCheckoutsWhenFinesDue(isset($_POST["isBlockCheckoutsWhenFinesDue"]));
  $set->setLocale($_POST["locale"]);
  $_POST["locale"] = $set->getLocale();
  $set->setCharset($_POST["charset"]);
  $_POST["charset"] = $set->getCharset();
  $set->setHtmlLangAttr($_POST["htmlLangAttr"]);
  $_POST["htmlLangAttr"] = $set->getHtmlLangAttr();

  if (!$set->validateData()) {
    $pageErrors["sessionTimeout"] = $set->getSessionTimeoutError();
    $pageErrors["itemsPerPage"] = $set->getItemsPerPageError();
    $pageErrors["purgeHistoryAfterMonths"] = $set->getPurgeHistoryAfterMonthsError();
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/settings_edit_form.php");
    exit();
  }


  #**************************************************************************
  #*  Update domain table row
  #**************************************************************************
  $setQ = new SettingsQuery();
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

  header("Location: ../admin/settings_edit_form.php?reset=Y&updated=Y");
  exit();
?>