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

  $tab = "circulation";
  $nav = "newconfirm";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../circ/mbr_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $mbr = new Member();
  $mbr->setBarcodeNmbr($HTTP_POST_VARS["barcodeNmbr"]);
  $HTTP_POST_VARS["barcodeNmbr"] = $mbr->getBarcodeNmbr();
  $mbr->setLastName($HTTP_POST_VARS["lastName"]);
  $HTTP_POST_VARS["lastName"] = $mbr->getLastName();
  $mbr->setFirstName($HTTP_POST_VARS["firstName"]);
  $HTTP_POST_VARS["firstName"] = $mbr->getFirstName();
  $mbr->setAddress1($HTTP_POST_VARS["address1"]);
  $HTTP_POST_VARS["address1"] = $mbr->getAddress1();
  $mbr->setAddress2($HTTP_POST_VARS["address2"]);
  $HTTP_POST_VARS["address2"] = $mbr->getAddress2();
  $mbr->setCity($HTTP_POST_VARS["city"]);
  $HTTP_POST_VARS["city"] = $mbr->getCity();
  $mbr->setState($HTTP_POST_VARS["state"]);
  $mbr->setZip($HTTP_POST_VARS["zip"]);
  $HTTP_POST_VARS["zip"] = $mbr->getZip();
  $mbr->setZipExt($HTTP_POST_VARS["zipExt"]);
  $HTTP_POST_VARS["zipExt"] = $mbr->getZipExt();
  $mbr->setHomePhone($HTTP_POST_VARS["homePhone"]);
  $HTTP_POST_VARS["homePhone"] = $mbr->getHomePhone();
  $mbr->setWorkPhone($HTTP_POST_VARS["workPhone"]);
  $HTTP_POST_VARS["workPhone"] = $mbr->getWorkPhone();
  $mbr->setClassification($HTTP_POST_VARS["classification"]);
  $mbr->setSchoolGrade($HTTP_POST_VARS["schoolGrade"]);
  $HTTP_POST_VARS["schoolGrade"] = $mbr->getSchoolGrade();
  $mbr->setSchoolTeacher($HTTP_POST_VARS["schoolTeacher"]);
  $HTTP_POST_VARS["schoolTeacher"] = $mbr->getSchoolTeacher();
  $validData = $mbr->validateData();
  if (!$validData) {
    $pageErrors["barcodeNmbr"] = $mbr->getBarcodeNmbrError();
    $pageErrors["lastName"] = $mbr->getLastNameError();
    $pageErrors["firstName"] = $mbr->getFirstNameError();
    $pageErrors["zip"] = $mbr->getZipError();
    $pageErrors["zipExt"] = $mbr->getZipExtError();
    $pageErrors["schoolGrade"] = $mbr->getSchoolGradeError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../circ/mbr_new_form.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new library member
  #**************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  if ($mbrQ->errorOccurred()) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  if (!$mbrQ->insert($mbr)) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  $mbrQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
Member, <?php echo $mbr->getFirstName();?> <?php echo $mbr->getLastName();?>, has been added.<br><br>
<a href="../circ/index.php">return to circulation summary</a>

<?php require_once("../shared/footer.php"); ?>
