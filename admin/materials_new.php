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
  $nav = "materials";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Dm.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../admin/materials_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $dm = new Dm();
  $dm->setDescription($HTTP_POST_VARS["description"]);
  $HTTP_POST_VARS["description"] = $dm->getDescription();
  $dm->setAdultCheckoutLimit($HTTP_POST_VARS["adultCheckoutLimit"]);
  $HTTP_POST_VARS["adultCheckoutLimit"] = $dm->getAdultCheckoutLimit();
  $dm->setJuvenileCheckoutLimit($HTTP_POST_VARS["juvenileCheckoutLimit"]);
  $HTTP_POST_VARS["juvenileCheckoutLimit"] = $dm->getJuvenileCheckoutLimit();
  $dm->setImageFile($HTTP_POST_VARS["imageFile"]);
  $HTTP_POST_VARS["imageFile"] = $dm->getImageFile();

  if (!$dm->validateData()) {
    $pageErrors["description"] = $dm->getDescriptionError();
    $pageErrors["adultCheckoutLimit"] = $dm->getAdultCheckoutLimitError();
    $pageErrors["juvenileCheckoutLimit"] = $dm->getJuvenileCheckoutLimitError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../admin/materials_new_form.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new domain table row
  #**************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  if (!$dmQ->insert("material_type_dm",$dm)) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->close();

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
<? echo $loc->getText("admin_materials_delMaterialType"); ?><?php echo $dm->getDescription();?><br><br>
<a href="../admin/materials_list.php"><? echo $loc->getText("admin_materials_Return"); ?></a>

<?php require_once("../shared/footer.php"); ?>
