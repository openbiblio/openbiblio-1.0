<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "circulation";
  $nav = "mbr/account";
  $focus_form_name = "accttransform";
  $focus_form_field = "transactionTypeCd";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../functions/formatFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../shared/get_form_vars.php"));
  require_once(REL(__FILE__, "../model/TransactionTypes.php"));
  require_once(REL(__FILE__, "../classes/MemberAccountTransaction.php"));
  require_once(REL(__FILE__, "../classes/MemberAccountQuery.php"));


  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_GET["mbrid"];
  if (isset($_GET["msg"])) {
    $msg = "<p class=\"error\">".stripslashes($_GET["msg"])."</p><br /><br />";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Show transaction input form
  #****************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<?php echo $msg ?>

<form name="accttransform" method="post" action="../circ/mbr_transaction.php">
<table class="primary">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php echo T("Add a Transaction"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo T("Transaction Type:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php
        $transtypes = new TransactionTypes;
        echo inputfield('select', 'transactionTypeCd','' , NULL, $transtypes->getSelect());
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo T("Description:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("description",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo T("Amount:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("amount",12,12,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="primary" valign="top" align="center">
      <input type="submit" value="<?php echo T("Add"); ?>" class="button" />
    </td>
  </tr>
</table>
<input type="hidden" name="mbrid" value="<?php echo $mbrid;?>" />
</form>

<?php
  #****************************************************************************
  #*  Search database for member account info
  #****************************************************************************
  $transQ = new MemberAccountQuery();
  $transactions = $transQ->getByMbrid($mbrid);

?>

<h1><?php echo T("Member Account Transactions"); ?></h1>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Function"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Date"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Trans Type"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Description"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Amount"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Balance"); ?>
    </th>
  </tr>

<?php
  if (empty($transactions)) {
?>
  <tr>
    <td class="primary" align="center" colspan="6">
      <?php echo T("No transactions found."); ?>
    </td>
  </tr>
<?php
  } else {
    $bal = 0;
    ?>
    <tr><td class="primary" colspan="5"><?php echo T("Opening Balance"); ?></td><td class="primary"><?php echo moneyFormat($bal,2); ?></td></tr>

    <?php
    foreach ($transactions as $trans) {
      $bal = $bal + $trans->getAmount();
?>
  <tr>
    <td class="primary" valign="top" >
      <a href="../circ/mbr_transaction_del_confirm.php?mbrid=<?php echo $mbrid;?>&transid=<?php echo $trans->getTransid();?>"><?php echo T("del");?></a>
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

?>
</table>

<?php

  Page::footer();
