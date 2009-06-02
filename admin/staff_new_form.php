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
  $focus_form_name = "newstaffform";
  $focus_form_field = "last_name";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");

?>

<form name="newstaffform" method="POST" action="../admin/staff_new.php">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      Add New Staff Member:
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
      Password:
    </td>
    <td valign="top" class="primary">
      <input type="password" name="pwd" size="20" maxlength="20"
      value="<?php if (isset($postVars["pwd"])) echo $postVars["pwd"]; ?>" ><br>
      <font class="error">
      <?php if (isset($pageErrors["pwd"])) echo $pageErrors["pwd"]; ?></font>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Re-enter Password:
    </td>
    <td valign="top" class="primary">
      <input type="password" name="pwd2" size="20" maxlength="20"
      value="<?php if (isset($postVars["pwd2"])) echo $postVars["pwd2"]; ?>" ><br>
      <font class="error">
      <?php if (isset($pageErrors["pwd2"])) echo $pageErrors["pwd2"]; ?></font>
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
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  Submit  ">
      <input type="button" onClick="parent.location='../admin/staff_list.php'" value="  Cancel  ">
    </td>
  </tr>

</table>
      </form>


<?php include("../shared/footer.php"); ?>
