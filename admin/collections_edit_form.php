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
  $focus_form_name = "editcollectionform";
  $focus_form_field = "description";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  require_once("../shared/header.php");

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($HTTP_GET_VARS["code"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);

    $code = $HTTP_GET_VARS["code"];
    $postVars["code"] = $code;
    include_once("../classes/Dm.php");
    include_once("../classes/DmQuery.php");
    include_once("../functions/errorFuncs.php");
    $dmQ = new DmQuery();
    $dmQ->connect();
    if ($dmQ->errorOccurred()) {
      $dmQ->close();
      displayErrorPage($dmQ);
    }
    $dmQ->execSelect("collection_dm",$code);
    if ($dmQ->errorOccurred()) {
      $dmQ->close();
      displayErrorPage($dmQ);
    }
    $dm = $dmQ->fetchRow();
    $postVars["description"] = $dm->getDescription();
    $postVars["daysDueBack"] = $dm->getDaysDueBack();
    $postVars["dailyLateFee"] = $dm->getDailyLateFee();
    $dmQ->close();
  } else {
    require("../shared/get_form_vars.php");
  }
?>

<form name="editcollectionform" method="POST" action="../admin/collections_edit.php">
<input type="hidden" name="code" value="<?php echo $postVars["code"];?>">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      <? echo $loc->getText("adminCollections_edit_formEditcollection"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <? echo $loc->getText("adminCollections_edit_formDescription"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("description",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <font class="small">*</font><? echo $loc->getText("adminCollections_edit_formDaysdueback"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("daysDueBack",2,2,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary"><? echo $loc->getText("adminCollections_edit_formDailyLateFee"); ?>
      <font class="small">*</font>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("dailyLateFee",7,7,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  <? echo $loc->getText("adminSubmit"); ?>  " class="button">
      <input type="button" onClick="parent.location='../admin/collections_list.php'" value="  <? echo $loc->getText("adminCancel"); ?>  " class="button">
    </td>
  </tr>

</table>
      </form>
<table><tr><td valign="top"><font class="small"><? echo $loc->getText("adminCollections_edit_formNote"); ?></font></td>
<td><font class="small"><? echo $loc->getText("adminCollections_edit_formNoteText"); ?><br></font>
</td></tr></table>

<?php include("../shared/footer.php"); ?>
