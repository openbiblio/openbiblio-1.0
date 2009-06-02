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
  $nav = "newconfirm";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioField.php");
  require_once("../classes/BiblioQuery.php");
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
  $biblio = new Biblio();
  $biblio->setMaterialCd($HTTP_POST_VARS["materialCd"]);
  $biblio->setCollectionCd($HTTP_POST_VARS["collectionCd"]);
  $biblio->setCallNmbr1($HTTP_POST_VARS["callNmbr1"]);
  $biblio->setCallNmbr2($HTTP_POST_VARS["callNmbr2"]);
  $biblio->setCallNmbr3($HTTP_POST_VARS["callNmbr3"]);
  $biblio->setLastChangeUserid($HTTP_SESSION_VARS["userid"]);
  $biblio->setOpacFlg(isset($HTTP_POST_VARS["opacFlg"]));
  $HTTP_POST_VARS["callNmbr1"] = $biblio->getCallNmbr1();
  $HTTP_POST_VARS["callNmbr2"] = $biblio->getCallNmbr2();
  $HTTP_POST_VARS["callNmbr3"] = $biblio->getCallNmbr3();
  $indexes = $HTTP_POST_VARS["indexes"];
  foreach($indexes as $index) {
    $tag = $HTTP_POST_VARS["tags"][$index];
    $subfieldCd = $HTTP_POST_VARS["subfieldCds"][$index];
    $requiredFlg = $HTTP_POST_VARS["requiredFlgs"][$index];
    $value = $HTTP_POST_VARS["values"][$index];
    # echo "<br>index=".$index." tag=".$tag." subfieldCd=".$subfieldCd." value=".$value;
    $biblioFld = new BiblioField();
    $biblioFld->setTag($tag);
    $biblioFld->setSubfieldCd($subfieldCd);
    $biblioFld->setIsRequired($requiredFlg);
    $biblioFld->setFieldData($value);
    $HTTP_POST_VARS[$index] = $biblioFld->getFieldData();
    $biblio->addBiblioField($index,$biblioFld);
  }
  $validData = $biblio->validateData();
  if (!$validData) {
    $pageErrors["callNmbr1"] = $biblio->getCallNmbrError();
    $biblioFlds = $biblio->getBiblioFields();
    foreach($indexes as $index) {
      if ($biblioFlds[$index]->getFieldDataError() != "") {
        $pageErrors[$index] = $biblioFlds[$index]->getFieldDataError();
      }
    }
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../catalog/biblio_new_form.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new bibliography
  #**************************************************************************
  $biblioQ = new BiblioQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$bibid = $biblioQ->insert($biblio)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  $biblioQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  $msg = $loc->getText("biblioNewSuccess");
  $msg = urlencode($msg);
  header("Location: ../shared/biblio_view.php?bibid=".$bibid."&msg=".$msg);
  exit();
?>
