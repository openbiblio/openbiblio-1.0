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


  $temp_return_page = "";
  if (isset($HTTP_GET_VARS["RET"])){
    $HTTP_SESSION_VARS["returnPage"] = $HTTP_GET_VARS["RET"];
  }

  $tab = "home";
  $nav = "";
  $focus_form_name = "loginform";
  $focus_form_field = "username";

  require_once("../shared/read_settings.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");

?>

<br>
<center>
<form name="loginform" method="POST" action="../shared/login.php">
<table class="border">
  <tr>
    <th colspan="2">Staff Login:</td>
  </tr>
  <tr>
    <td valign="top" class="noborder">
      Username:</font>
    </td>
    <td valign="top" class="noborder">
      <input type="text" name="username" size="20" maxlength="20"
      value="<?php if (isset($postVars["username"])) echo $postVars["username"]; ?>" >
      <font class="error"><?php if (isset($pageErrors["username"])) echo $pageErrors["username"]; ?></font>
    </td>
  </tr>
  <tr>
    <td valign="top" class="noborder">
      Password:</font>
    </td>
    <td valign="top" class="noborder">
      <input type="password" name="pwd" size="20" maxlength="20"
      value="<?php if (isset($postVars["pwd"])) echo $postVars["pwd"]; ?>" >
      <font class="error">
      <?php if (isset($pageErrors["pwd"])) echo $pageErrors["pwd"]; ?></font>
    </td>
  </tr>

  <tr>
    <td colspan="2" align="center" class="noborder">
      <input type="submit" value="  Login  ">
    </td>
  </tr>
</table>
</form>
</center>

<?php include("../shared/footer.php"); ?>
