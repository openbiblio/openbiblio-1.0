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
  $nav = "collections";
  $focus_form_name = "newcollectionform";
  $focus_form_field = "description";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");

?>

<form name="newcollectionform" method="POST" action="../admin/collections_new.php">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      Add New Collection:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Description:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("description",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <font class="small">*</font>Days Due Back:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("daysDueBack",2,2,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  Submit  ">
      <input type="button" onClick="parent.location='../admin/collections_list.php'" value="  Cancel  ">
    </td>
  </tr>

</table>
      </form>
<table><tr><td valign="top"><font class="small">*Note:</font></td>
<td><font class="small">Setting the days due back to zero 
makes the entire collection unavailable for checkout.<br></font>
</td></tr></table>


<?php include("../shared/footer.php"); ?>
