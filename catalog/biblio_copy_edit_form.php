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

  $tab = "cataloging";
  $nav = "editcopy";
  $focus_form_name = "editCopyForm";
  $focus_form_field = "barcodeNmbr";
  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  if (isset($HTTP_GET_VARS["bibid"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var
    #****************************************************************************
    $bibid = $HTTP_GET_VARS["bibid"];
    $copyid = $HTTP_GET_VARS["copyid"];

    #****************************************************************************
    #*  Read copy information
    #****************************************************************************
    $copyQ = new BiblioCopyQuery();
    $copyQ->connect();
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
    if (!$copy = $copyQ->query($bibid,$copyid)) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
    $postVars["bibid"] = $bibid;
    $postVars["copyid"] = $copyid;
    $postVars["barcodeNmbr"] = $copy->getBarcodeNmbr();
    $postVars["copyDesc"] = $copy->getCopyDesc();
    $postVars["statusCd"] = $copy->getStatusCd();

  } else {
    #**************************************************************************
    #*  load up post vars
    #**************************************************************************
    require("../shared/get_form_vars.php");
    $bibid = $postVars["bibid"];
    $copyid = $postVars["copyid"];
  }

  #**************************************************************************
  #*  disable status code drop down for shelving cart and out status codes
  #**************************************************************************
  $statusDisabled = FALSE;
  if (($postVars["statusCd"] == OBIB_STATUS_SHELVING_CART) or ($postVars["statusCd"] == OBIB_STATUS_OUT)) {
    $statusDisabled = TRUE;
  }

  require_once("../shared/header.php");
?>

<font class="small">
<?php echo $loc->getText("catalogFootnote",array("symbol"=>"</font>*<font class=\"small\">")); ?>
</font>

<form name="editCopyForm" method="POST" action="../catalog/biblio_copy_edit.php">
<table class="primary">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      <?php echo $loc->getText("biblioCopyEditFormLabel"); ?>:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <sup>*</sup> <?php echo $loc->getText("biblioCopyNewBarcode"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("barcodeNmbr",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioCopyNewDesc"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("copyDesc",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioCopyEditFormStatus"); ?>:
    </td>
    <td valign="top" class="primary">

<?php 
  #**************************************************************************
  #*  only show status codes for valid transitions
  #**************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelect("biblio_status_dm");
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  echo "<select name=\"statusCd\"";
  if ($disabled) {
    echo " disabled";
  }
  echo ">\n";
  while ($dm = $dmQ->fetchRow()) {
    #**************************************************************************
    #*  tranisitions to out, hold, and shelving cart are not allowed
    #**************************************************************************
    if (($dm->getCode() != OBIB_STATUS_OUT)
      and ($dm->getCode() != OBIB_STATUS_ON_HOLD)
      and ($dm->getCode() != OBIB_STATUS_SHELVING_CART)) {
      echo "<option value=\"".$dm->getCode()."\"";
      if (($postVars["statusCd"] == "") && ($dm->getDefaultFlg() == 'Y')) {
        echo " selected";
      } elseif ($postVars["statusCd"] == $dm->getCode()) {
        echo " selected";
      }
      echo ">".$dm->getDescription()."\n";
    }
  }
  echo "</select>\n";
  $dmQ->close();
?>


    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo $loc->getText("catalogSubmit"); ?>" class="button">
      <input type="button" onClick="parent.location='../shared/biblio_view.php?bibid=<?php echo $bibid; ?>'" value="<?php echo $loc->getText("catalogCancel"); ?>" class="button" >
    </td>
  </tr>

</table>
<input type="hidden" name="bibid" value="<?php echo $bibid;?>">
<input type="hidden" name="copyid" value="<?php echo $copyid;?>">
</form>


<?php include("../shared/footer.php"); ?>
