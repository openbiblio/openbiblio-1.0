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

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($HTTP_GET_VARS["UID"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);

    $userid = $HTTP_GET_VARS["UID"];
    $postVars["userid"] = $userid;
    include_once("../classes/Staff.php");
    include_once("../classes/StaffQuery.php");
    include_once("../functions/errorFuncs.php");
    $staffQ = new StaffQuery();
    $staffQ->connect();
    if ($staffQ->errorOccurred()) {
      $staffQ->close();
      displayErrorPage($staffQ);
    }
    $staffQ->execSelect($userid);
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
      Edit Staff Member Information:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Last Name:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("last_name",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      First Name:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("first_name",30,30,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Login Username:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("username",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Authorization:
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="circ_flg" value="CHECKED"
        <?php if (isset($postVars["circ_flg"])) echo $postVars["circ_flg"]; ?> >
      Circ
      <input type="checkbox" name="catalog_flg" value="CHECKED"
        <?php if (isset($postVars["catalog_flg"])) echo $postVars["catalog_flg"]; ?> >
      Catalog
      <input type="checkbox" name="admin_flg" value="CHECKED"
        <?php if (isset($postVars["admin_flg"])) echo $postVars["admin_flg"]; ?> >
      Admin
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Suspended:
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="suspended_flg" value="CHECKED"
        <?php if (isset($postVars["suspended_flg"])) echo $postVars["suspended_flg"]; ?> >
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  Submit  ">
      <input type="button" onClick="parent.location='../admin/staff_list.php'" value="  Cancel  ">
    </td>
  </tr>
</table>
</form>

<?php include("../shared/footer.php"); ?>
