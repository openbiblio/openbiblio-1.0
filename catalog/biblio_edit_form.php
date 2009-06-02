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

  session_cache_limiter(null);

  $tab = "cataloging";
  $nav = "edit";
  $helpPage = "biblioEdit";
  $focus_form_name = "editbiblioform";
  $focus_form_field = "materialCd";
  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  if (isset($HTTP_GET_VARS["bibid"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var
    #****************************************************************************
    $bibid = $HTTP_GET_VARS["bibid"];

    #****************************************************************************
    #*  Search database
    #****************************************************************************
    $biblioQ = new BiblioQuery();
    $biblioQ->connect();
    if ($biblioQ->errorOccurred()) {
      $biblioQ->close();
      displayErrorPage($biblioQ);
    }
    if (!$biblio = $biblioQ->query($bibid)) {
      $biblioQ->close();
      displayErrorPage($biblioQ);
    }
    $biblioFlds = $biblio->getBiblioFields();

    #**************************************************************************
    #*  load up post vars
    #**************************************************************************
    $postVars["bibid"] = $bibid;
    $postVars["collectionCd"] = $biblio->getCollectionCd();
    $postVars["materialCd"] = $biblio->getMaterialCd();
    $postVars["callNmbr1"] = $biblio->getCallNmbr1();
    $postVars["callNmbr2"] = $biblio->getCallNmbr2();
    $postVars["callNmbr3"] = $biblio->getCallNmbr3();
    if ($biblio->showInOpac()) {
      $postVars["opacFlg"] = "CHECKED";
    } else {
      $postVars["opacFlg"] = "";
    }
    $biblioFlds = $biblio->getBiblioFields();
    foreach ($biblioFlds as $key => $biblioFld) {
      // if we allow these tags with a fldid(added from marc screens), we'll get dup data.
      if ( !(($biblioFld->getTag() == 245) && ($biblioFld->getSubfieldCd() == "a") && ($biblioFld->getFieldid() != ""))
        && !(($biblioFld->getTag() == 245) && ($biblioFld->getSubfieldCd() == "b") && ($biblioFld->getFieldid() != ""))
        && !(($biblioFld->getTag() == 245) && ($biblioFld->getSubfieldCd() == "c") && ($biblioFld->getFieldid() != ""))
        && !(($biblioFld->getTag() == 100) && ($biblioFld->getSubfieldCd() == "a") && ($biblioFld->getFieldid() != ""))
        && !(($biblioFld->getTag() == 650) && ($biblioFld->getSubfieldCd() == "a") && ($biblioFld->getFieldid() != "")) ) {
        $postVars[$key] = $biblioFld->getFieldData();
        $fieldIds[$key] = $biblioFld->getFieldid();
      }
    }
    $HTTP_SESSION_VARS["postVars"] = $postVars;
//    $title = urlencode($postVars["callNmbr"]);
  } else {
    require("../shared/get_form_vars.php");
    $bibid = $postVars["bibid"];
//    $title = urlencode($postVars["callNmbr"]);
  }
  require_once("../shared/header.php");

  $cancelLocation = "../shared/biblio_view.php?bibid=".$postVars["bibid"];
  $headerWording="Edit";

?>

<form name="editbiblioform" method="POST" action="../catalog/biblio_edit.php">
<input type="hidden" name="bibid" value="<?php echo $postVars["bibid"];?>">
<?php include("../catalog/biblio_fields.php"); ?>
<?php include("../shared/footer.php"); ?>
