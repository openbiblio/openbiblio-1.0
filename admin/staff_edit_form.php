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

  $tab = "admin";
  $nav = "staff";
  $focus_form_name = "editstaffform";
  $focus_form_field = "last_name";

  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  require_once("../shared/header.php");

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($_GET["UID"])){
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);

    $postVars["userid"] = $_GET["UID"];
    include_once("../classes/Staff.php");
    include_once("../classes/StaffQuery.php");
    include_once("../functions/errorFuncs.php");
    $staffQ = new StaffQuery();
    $staffQ->connect();
    if ($staffQ->errorOccurred()) {
      $staffQ->close();
      displayErrorPage($staffQ);
    }
    $staffQ->execSelect($postVars["userid"]);
    if ($staffQ->errorOccurred()) {
      $staffQ->close();
      displayErrorPage($staffQ);
    }
    $staff = $staffQ->fetchStaff();
    $postVars["last_name"] = $staff->getLastName();
    $postVars["first_name"] = $staff->getFirstName();
    $postVars["username"] = $staff->getUsername();
    if ($staff->hasCircAuth()) {
      $postVars["circ_flg"] = "CHECKED";
    } else {
      $postVars["circ_flg"] = "";
    }
    if ($staff->hasCircMbrAuth()) {
      $postVars["circ_mbr_flg"] = "CHECKED";
    } else {
      $postVars["circ_mbr_flg"] = "";
    }
    if ($staff->hasCatalogAuth()) {
      $postVars["catalog_flg"] = "CHECKED";
    } else {
      $postVars["catalog_flg"] = "";
    }
    if ($staff->hasAdminAuth()) {
      $postVars["admin_flg"] = "CHECKED";
    } else {
      $postVars["admin_flg"] = "";
    }
    if ($staff->hasReportsAuth()) {
      $postVars["reports_flg"] = "CHECKED";
    } else {
      $postVars["reports_flg"] = "";
    }
    if ($staff->isSuspended()) {
      $postVars["suspended_flg"] = "CHECKED";
    } else {
      $postVars["suspended_flg"] = "";
    }
    $staffQ->close();
  } else {
    require("../shared/get_form_vars.php");
  }

?>

<form name="editstaffform" method="POST" action="../admin/staff_edit.php">
<input type="hidden" name="userid" value="<?php echo $postVars["userid"];?>">
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      <? echo $loc->getText("adminStaff_edit_formHeader"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminStaff_edit_formLastname"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("last_name",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminStaff_edit_formFirstname"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("first_name",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminStaff_edit_formLogin"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("username",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminStaff_edit_formAuth"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="circ_flg" value="CHECKED"
        <?php if (isset($postVars["circ_flg"])) echo $postVars["circ_flg"]; ?> >
      <? echo $loc->getText("adminStaff_edit_formCirc"); ?>
      <input type="checkbox" name="circ_mbr_flg" value="CHECKED"
        <?php if (isset($postVars["circ_mbr_flg"])) echo $postVars["circ_mbr_flg"]; ?> >
      <? echo $loc->getText("adminStaff_edit_formUpdatemember"); ?>
      <input type="checkbox" name="catalog_flg" value="CHECKED"
        <?php if (isset($postVars["catalog_flg"])) echo $postVars["catalog_flg"]; ?> >
      <? echo $loc->getText("adminStaff_edit_formCatalog"); ?>
      <input type="checkbox" name="admin_flg" value="CHECKED"
        <?php if (isset($postVars["admin_flg"])) echo $postVars["admin_flg"]; ?> >
      <? echo $loc->getText("adminStaff_edit_formAdmin"); ?>
      <input type="checkbox" name="reports_flg" value="CHECKED"
        <?php if (isset($postVars["reports_flg"])) echo $postVars["reports_flg"]; ?> >
      <? echo $loc->getText("adminStaff_edit_formReports"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminStaff_edit_formSuspended"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="suspended_flg" value="CHECKED"
        <?php if (isset($postVars["suspended_flg"])) echo $postVars["suspended_flg"]; ?> >
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  <? echo $loc->getText("adminSubmit"); ?>  " class="button">
      <input type="button" onClick="parent.location='../admin/staff_list.php'" value="  <? echo $loc->getText("adminCancel"); ?>  " class="button">
    </td>
  </tr>
</table>
</form>

<?php include("../shared/footer.php"); ?>
