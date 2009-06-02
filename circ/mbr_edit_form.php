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

  $tab = "circulation";
  $restrictToMbrAuth = TRUE;
  $nav = "edit";
  $focus_form_name = "editMbrform";
  $focus_form_field = "barcodeNmbr";
  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");

  if (isset($_GET["mbrid"])){
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var
    #****************************************************************************
    $mbrid = $_GET["mbrid"];

    #****************************************************************************
    #*  Search database
    #****************************************************************************
    $mbrQ = new MemberQuery();
    $mbrQ->connect();
    if ($mbrQ->errorOccurred()) {
      $mbrQ->close();
      displayErrorPage($mbrQ);
    }
    if (!$mbrQ->execSelect($mbrid)) {
      $mbrQ->close();
      displayErrorPage($mbrQ);
    }
    $mbr = $mbrQ->fetchMember();

    #**************************************************************************
    #*  load up post vars
    #**************************************************************************
    $postVars["mbrid"] = $mbrid;
    $postVars["barcodeNmbr"] = $mbr->getBarcodeNmbr();
    $postVars["lastName"] = $mbr->getLastName();
    $postVars["firstName"] = $mbr->getFirstName();
    $postVars["address1"] = $mbr->getAddress1();
    $postVars["address2"] = $mbr->getAddress2();
    $postVars["city"] = $mbr->getCity();
    $postVars["state"] = $mbr->getState();
    $postVars["zip"] = $mbr->getZip();
    $postVars["zipExt"] = $mbr->getZipExt();
    $postVars["homePhone"] = $mbr->getHomePhone();
    $postVars["workPhone"] = $mbr->getWorkPhone();
    $postVars["email"] = $mbr->getEmail();
    $postVars["classification"] = $mbr->getClassification();
    $postVars["schoolGrade"] = $mbr->getSchoolGrade();
    $postVars["schoolTeacher"] = $mbr->getSchoolTeacher();

    $_SESSION["postVars"] = $postVars;
    $mbrName = urlencode($mbr->getFirstName()." ".$mbr->getLastName());
  } else {
    require("../shared/get_form_vars.php");
    $mbrid = $postVars["mbrid"];
    $mbrName = urlencode($postVars["firstName"]." ".$postVars["lastName"]);
  }
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  $headerWording = $loc->getText("mbrEditForm");

  $cancelLocation = "../circ/mbr_view.php?mbrid=".$postVars["mbrid"]."&reset=Y";
?>

<form name="editMbrform" method="POST" action="../circ/mbr_edit.php">
<input type="hidden" name="mbrid" value="<?php echo $postVars["mbrid"];?>">
<?php include("../circ/mbr_fields.php"); ?>
<?php include("../shared/footer.php"); ?>
