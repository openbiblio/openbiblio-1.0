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

  $tab = "circulation";
  $nav = "account";
  $focus_form_name = "accttransform";
  $focus_form_field = "transactionTypeCd";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/get_form_vars.php");
  require_once("../classes/MemberAccountTransaction.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_GET_VARS) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $HTTP_GET_VARS["mbrid"];
  if (isset($HTTP_GET_VARS["msg"])) {
    $msg = "<font class=\"error\">".stripslashes($HTTP_GET_VARS["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelect("transaction_type_dm");
  $mbrClassifyDm = $dmQ->fetchRows();
  $dmQ->close();

  #****************************************************************************
  #*  Show transaction input form
  #****************************************************************************
  require_once("../shared/header.php");
?>

<?php echo $msg ?>

<form name="accttransform" method="POST" action="../circ/mbr_transaction.php">
<table class="primary">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrAccountLabel"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("mbrAccountTransTyp"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printSelect("transactionTypeCd","transaction_type_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("mbrAccountDesc"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("description",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("mbrAccountAmount"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("amount",12,12,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="primary" valign="top" align="center">
      <input type="submit" value="  <?php echo $loc->getText("circAdd"); ?>  " class="button">
    </td>
  </tr>
</table>
<input type="hidden" name="mbrid" value="<?php echo $mbrid;?>">
</form>

<?php 
  #****************************************************************************
  #*  Search database for member account info
  #****************************************************************************
  $transQ = new MemberAccountQuery();
  $transQ->connect();
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  if (!$transQ->query($mbrid)) {
    $transQ->close();
    displayErrorPage($transQ);
  }

?>

<h1><?php print $loc->getText("mbrAccountHead1"); ?></h1>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrAccountHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrAccountHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrAccountHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrAccountHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrAccountHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrAccountHdr6"); ?>
    </th>
  </tr>

<?php
  if ($transQ->getRowCount() == 0) {
?>
  <tr>
    <td class="primary" align="center" colspan="6">
      <?php print $loc->getText("mbrAccountNoTrans"); ?>
    </td>
  </tr>
<?php
  } else {
    $bal = 0;
    ?>
    <tr><td class="primary" colspan="5"><?php print $loc->getText("mbrAccountOpenBal"); ?></td><td class="primary"><?php echo moneyFormat($bal,2);?></td></tr>

    <?php
    while ($trans = $transQ->fetchRow()) {
      $bal = $bal + $trans->getAmount();
?>
  <tr>
    <td class="primary" valign="top" >
      <a href="../circ/mbr_transaction_del_confirm.php?mbrid=<?php echo $mbrid;?>&transid=<?php echo $trans->getTransid();?>"><?php echo $loc->getText("mbrAccountDel");?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo $trans->getCreateDt();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $trans->getTransactionTypeDesc();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $trans->getDescription();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo moneyFormat($trans->getAmount(),2);?>
    </td>
    <td class="primary" valign="top" >
      <?php echo moneyFormat($bal,2);?>
    </td>
  </tr>
<?php
    }
  }
  $transQ->close();

?>
</table>

<?php require_once("../shared/footer.php"); ?>
