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
  require_once("../shared/common.php");
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

  if (count($_POST) == 0) {
    header("Location: ../catalog/biblio_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $biblio = new Biblio();
  $biblio->setMaterialCd($_POST["materialCd"]);
  $biblio->setCollectionCd($_POST["collectionCd"]);
  $biblio->setCallNmbr1($_POST["callNmbr1"]);
  $biblio->setCallNmbr2($_POST["callNmbr2"]);
  $biblio->setCallNmbr3($_POST["callNmbr3"]);
  $biblio->setLastChangeUserid($_SESSION["userid"]);
  $biblio->setOpacFlg(isset($_POST["opacFlg"]));
  $_POST["callNmbr1"] = $biblio->getCallNmbr1();
  $_POST["callNmbr2"] = $biblio->getCallNmbr2();
  $_POST["callNmbr3"] = $biblio->getCallNmbr3();
  $indexes = $_POST["indexes"];
  foreach($indexes as $index) {
    $tag = $_POST["tags"][$index];
    $subfieldCd = $_POST["subfieldCds"][$index];
    $requiredFlg = $_POST["requiredFlgs"][$index];
    $value = $_POST["values"][$index];
    # echo "<br>index=".$index." tag=".$tag." subfieldCd=".$subfieldCd." value=".$value;
    $biblioFld = new BiblioField();
    $biblioFld->setTag($tag);
    $biblioFld->setSubfieldCd($subfieldCd);
    $biblioFld->setIsRequired($requiredFlg);
    $biblioFld->setFieldData($value);
    $_POST[$index] = $biblioFld->getFieldData();
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
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
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
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  $msg = $loc->getText("biblioNewSuccess");
  $msg = urlencode($msg);
  header("Location: ../shared/biblio_view.php?bibid=".$bibid."&msg=".$msg);
  exit();
?>
