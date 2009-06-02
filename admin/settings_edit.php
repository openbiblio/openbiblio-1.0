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
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Settings.php");
  require_once("../classes/SettingsQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../admin/settings_edit_form.php?reset=Y");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $set = new Settings();
  $set->setLibraryName($HTTP_POST_VARS["libraryName"]);
  $HTTP_POST_VARS["libraryName"] = $set->getLibraryName();
  $set->setLibraryImageUrl($HTTP_POST_VARS["libraryImageUrl"]);
  $HTTP_POST_VARS["libraryImageUrl"] = $set->getLibraryImageUrl();
  $set->setUseImageFlg(isset($HTTP_POST_VARS["isUseImageSet"]));
  $set->setLibraryHours($HTTP_POST_VARS["libraryHours"]);
  $HTTP_POST_VARS["libraryHours"] = $set->getLibraryHours();
  $set->setLibraryPhone($HTTP_POST_VARS["libraryPhone"]);
  $HTTP_POST_VARS["libraryPhone"] = $set->getLibraryPhone();
  $set->setLibraryUrl($HTTP_POST_VARS["libraryUrl"]);
  $HTTP_POST_VARS["libraryUrl"] = $set->getLibraryUrl();
  $set->setOpacUrl($HTTP_POST_VARS["opacUrl"]);
  $HTTP_POST_VARS["opacUrl"] = $set->getOpacUrl();
  $set->setSessionTimeout($HTTP_POST_VARS["sessionTimeout"]);
  $HTTP_POST_VARS["sessionTimeout"] = $set->getSessionTimeout();
  $set->setItemsPerPage($HTTP_POST_VARS["itemsPerPage"]);
  $HTTP_POST_VARS["itemsPerPage"] = $set->getItemsPerPage();
  $set->setPurgeHistoryAfterMonths($HTTP_POST_VARS["purgeHistoryAfterMonths"]);
  $HTTP_POST_VARS["purgeHistoryAfterMonths"] = $set->getPurgeHistoryAfterMonths();
  $set->setBlockCheckoutsWhenFinesDue(isset($HTTP_POST_VARS["isBlockCheckoutsWhenFinesDue"]));
  $set->setLocale($HTTP_POST_VARS["locale"]);
  $HTTP_POST_VARS["locale"] = $set->getLocale();
  $set->setCharset($HTTP_POST_VARS["charset"]);
  $HTTP_POST_VARS["charset"] = $set->getCharset();
  $set->setHtmlLangAttr($HTTP_POST_VARS["htmlLangAttr"]);
  $HTTP_POST_VARS["htmlLangAttr"] = $set->getHtmlLangAttr();

  if (!$set->validateData()) {
    $pageErrors["sessionTimeout"] = $set->getSessionTimeoutError();
    $pageErrors["itemsPerPage"] = $set->getItemsPerPageError();
    $pageErrors["purgeHistoryAfterMonths"] = $set->getPurgeHistoryAfterMonthsError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
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
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  header("Location: ../admin/settings_edit_form.php?reset=Y&updated=Y");
  exit();
?>