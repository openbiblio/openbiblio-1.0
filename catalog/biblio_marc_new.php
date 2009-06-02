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

  require_once("../classes/BiblioField.php");
  require_once("../classes/BiblioFieldQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../catalog/index.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $bibid=$HTTP_POST_VARS["bibid"];
  $fld = new BiblioField();
  $fld->setBibid($bibid);
  $fld->setTag($HTTP_POST_VARS["tag"]);
  $HTTP_POST_VARS["tag"] = $fld->getTag();
  $fld->setSubfieldCd($HTTP_POST_VARS["subfieldCd"]);
  $HTTP_POST_VARS["subfieldCd"] = $fld->getSubfieldCd();
  if (isset($HTTP_POST_VARS["ind1Cd"])) {
    $fld->setInd1Cd("Y");
  } else {
    $fld->setInd1Cd("N");
  }
  if (isset($HTTP_POST_VARS["ind2Cd"])) {
    $fld->setInd2Cd("Y");
  } else {
    $fld->setInd2Cd("N");
  }
  $fld->setFieldData($HTTP_POST_VARS["fieldData"]);
  $fld->setIsRequired(true);
  $HTTP_POST_VARS["fieldData"] = $fld->getFieldData();
  $validData = $fld->validateData();
  if (!$validData) {
    $pageErrors["fieldData"] = $fld->getFieldDataError();
    $pageErrors["tag"] = $fld->getTagError();
    $pageErrors["subfieldCd"] = $fld->getSubfieldCdError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../catalog/biblio_marc_new_form.php?bibid=".$bibid);
    exit();
  }

  #**************************************************************************
  #*  Insert new bibliography field
  #**************************************************************************
  $fieldQ = new BiblioFieldQuery();
  $fieldQ->connect();
  if ($fieldQ->errorOccurred()) {
    $fieldQ->close();
    displayErrorPage($fieldQ);
  }
  if (!$fieldQ->insert($fld)) {
    $fieldQ->close();
    displayErrorPage($fieldQ);
  }
  $fieldQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  $msg = $loc->getText("biblioMarcNewSuccess");
  $msg = urlencode($msg);
  header("Location: ../catalog/biblio_marc_list.php?bibid=".$bibid."&msg=".$msg);
  exit();
?>
