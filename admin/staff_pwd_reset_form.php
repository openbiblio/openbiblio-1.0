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
  $nav = "staff";
  $focus_form_name = "pwdresetform";
  $focus_form_field = "pwd";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  include("../shared/header.php");


  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($HTTP_GET_VARS["UID"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);

    $userid = $HTTP_GET_VARS["UID"];
    $postVars["userid"] = $userid;
  } else {
    require("../shared/get_form_vars.php");
  }

?>

<form name="pwdresetform" method="POST" action="../admin/staff_pwd_reset.php">
<input type="hidden" name="userid" value="<?php echo $postVars["userid"];?>">
<table class="primary">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      Reset Staff Member Password:
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
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  Submit  " class="button">
      <input type="button" onClick="parent.location='../admin/staff_list.php'" value="  Cancel  " class="button">
    </td>
  </tr>

</table>
</form>

<?php include("../shared/footer.php"); ?>
