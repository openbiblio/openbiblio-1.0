<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "cataloging";
  $nav = "editcopy";
  $helpPage = "biblioCopyEdit";
  $focus_form_name = "editCopyForm";
  $focus_form_field = "barcodeNmbr";
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
  if (isset($_GET["bibid"])){
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var
    #****************************************************************************
    $bibid = $_GET["bibid"];
    $copyid = $_GET["copyid"];

    #****************************************************************************
    #*  Read copy information
    #****************************************************************************
    $copyQ = new BiblioCopyQuery();
    $copyQ->connect();
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
    if (!$copy = $copyQ->doQuery($bibid,$copyid)) {
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
<?php echo $loc->getText("catalogFootnote",array("symbol"=>"*")); ?>
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
  $dms = $dmQ->get("biblio_status_dm");
  $dmQ->close();
  echo "<select name=\"statusCd\"";
  if ($disabled) {
    echo " disabled";
  }
  echo ">\n";
  foreach ($dms as $dm) {
    #**************************************************************************
    #*  tranisitions to out, hold, and shelving cart are not allowed
    #**************************************************************************
    if (($dm->getCode() != OBIB_STATUS_OUT)
      and ($dm->getCode() != OBIB_STATUS_ON_HOLD)
      and ($dm->getCode() != OBIB_STATUS_SHELVING_CART)) {
      echo "<option value=\"".H($dm->getCode())."\"";
      if (($postVars["statusCd"] == "") && ($dm->getDefaultFlg() == 'Y')) {
        echo " selected";
      } elseif ($postVars["statusCd"] == $dm->getCode()) {
        echo " selected";
      }
      echo ">".H($dm->getDescription())."</option>\n";
    }
  }
  echo "</select>\n";
?>


    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo $loc->getText("catalogSubmit"); ?>" class="button">
      <input type="button" onClick="self.location='../shared/biblio_view.php?bibid=<?php echo HURL($bibid); ?>'" value="<?php echo $loc->getText("catalogCancel"); ?>" class="button" >
    </td>
  </tr>

</table>
<input type="hidden" name="bibid" value="<?php echo H($bibid);?>">
<input type="hidden" name="copyid" value="<?php echo H($copyid);?>">
</form>


<?php include("../shared/footer.php"); ?>
