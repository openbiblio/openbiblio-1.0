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

  $tab = "cataloging";
  $nav = "view";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../catalog/biblio_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $bibid=$HTTP_POST_VARS["bibid"];
  $copy = new BiblioCopy();
  $copy->setBibid($bibid);
  $copy->setCopyDesc($HTTP_POST_VARS["copyDesc"]);
  $HTTP_POST_VARS["copyDesc"] = $copy->getCopyDesc();
  $copy->setBarcodeNmbr($HTTP_POST_VARS["barcodeNmbr"]);
  $HTTP_POST_VARS["barcodeNmbr"] = $copy->getBarcodeNmbr();
  $validData = $copy->validateData();
  if (!$validData) {
    $pageErrors["barcodeNmbr"] = $copy->getBarcodeNmbrError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../catalog/biblio_copy_new_form.php?bibid=".$bibid);
    exit();
  }

  #**************************************************************************
  #*  Insert new bibliography copy
  #**************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if (!$copyQ->insert($copy)) {
    $copyQ->close();
    if ($copyQ->getDbErrno() == "") {
      $pageErrors["barcodeNmbr"] = $copyQ->getError();
      $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
      $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
      header("Location: ../catalog/biblio_copy_new_form.php?bibid=".$bibid);
      exit();
    } else {
      displayErrorPage($copyQ);
    }
  }
  $copyQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  $msg = $loc->getText("biblioCopyNewSuccess");
  $msg = urlencode($msg);
  header("Location: ../shared/biblio_view.php?bibid=".$bibid."&msg=".$msg);
  exit();
?>
