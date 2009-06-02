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
  $nav = "settings";
  $focus_form_name = "editsettingsform";
  $focus_form_field = "libraryName";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($HTTP_GET_VARS["reset"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);

    include_once("../classes/Settings.php");
    include_once("../classes/SettingsQuery.php");
    include_once("../functions/errorFuncs.php");
    $setQ = new SettingsQuery();
    $setQ->connect();
    if ($setQ->errorOccurred()) {
      $setQ->close();
      displayErrorPage($setQ);
    }
    $setQ->execSelect();
    if ($setQ->errorOccurred()) {
      $setQ->close();
      displayErrorPage($setQ);
    }
    $set = $setQ->fetchRow();
    $postVars["libraryName"] = $set->getLibraryName();
    $postVars["libraryImageUrl"] = $set->getLibraryImageUrl();
    if ($set->isUseImageSet()) {
      $postVars["isUseImageSet"] = "CHECKED";
    } else {
      $postVars["isUseImageSet"] = "";
    }
    $postVars["libraryHours"] = $set->getLibraryHours();
    $postVars["libraryPhone"] = $set->getLibraryPhone();
    $postVars["libraryUrl"] = $set->getLibraryUrl();
    $postVars["opacUrl"] = $set->getOpacUrl();
    $postVars["sessionTimeout"] = $set->getSessionTimeout();
    $postVars["itemsPerPage"] = $set->getItemsPerPage();
    $setQ->close();
  } else {
    require("../shared/get_form_vars.php");
  }

  #****************************************************************************
  #*  Display update message if coming from settings_edit with a successful update.
  #****************************************************************************
  if (isset($HTTP_GET_VARS["updated"])){
?>
  <font class="error">Data has been updated.</font>
<?php
  }
?>

<form name="editsettingsform" method="POST" action="../admin/settings_edit.php">
<input type="hidden" name="code" value="<?php echo $postVars["code"];?>">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      Edit Library Settings:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Library Name:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryName",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Library Image URL:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryImageUrl",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Use Image in Place<br>of Name:
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="isUseImageSet" value="CHECKED"
        <?php if (isset($postVars["isUseImageSet"])) echo $postVars["isUseImageSet"]; ?> >
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Library Hours:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryHours",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Library Phone:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryPhone",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Library URL:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryUrl",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      OPAC URL:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("opacUrl",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Session Timeout:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("sessionTimeout",3,3,$postVars,$pageErrors); ?>minutes
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Search Results:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("itemsPerPage",2,2,$postVars,$pageErrors); ?>items per page
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  Update  ">
    </td>
  </tr>

</table>
      </form>


<?php include("../shared/footer.php"); ?>
