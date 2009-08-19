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
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	require_once(REL(__FILE__, "../model/MemberAccounts.php"));
	require_once(REL(__FILE__, "../model/TransactionTypes.php"));


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
<h3><?php echo T("Member Account Transactions"); ?></h3>

<?php echo $msg ?>

<form name="accttransform" method="post" action="../circ/mbr_transaction.php">
<fieldset>
<legend><?php echo T("Add a Transaction"); ?></legend>
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Transaction Type:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php
				$transtypes = new TransactionTypes;
				echo inputfield('select', 'transaction_type_cd','' , NULL, $transtypes->getSelect());
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
</fieldset>
</form>

<?php
	#****************************************************************************
	#*  Search database for member account info
	#****************************************************************************
	$acct = new MemberAccounts;
	$transactions = $acct->getByMbrid($mbrid);

?>
<fieldset>
<legend><?php echo T("Transaction Activity"); ?></legend>
<table class="primary">
	<thead>
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
	</thead>
<?php
	if ($transactions->count() === 0) {
?>
	<tr>
		<td class="primary" align="center" colspan="6">
			<?php echo T("No transactions found."); ?>
		</td>
	</tr>
<?php
	} else {
		$bal = 0;
		?><tbody class="striped">
		<tr><td class="primary" colspan="5"><?php echo T("Opening Balance"); ?></td><td class="primary"><?php echo $LOC->moneyFormat($bal); ?></td></tr>

		<?php
		while (($trans = $transactions->next()) !== NULL) {
			$bal += $trans['amount'];
?>
	<tr>
		<td class="primary" valign="top" >
			<a href="../circ/mbr_transaction_del_confirm.php?mbrid=<?php echo HURL($mbrid);?>&amp;transid=<?php echo HURL($trans['transid']);?>"><?php echo T("del");?></a>
		</td>
		<td class="primary" valign="top" >
			<?php echo H($trans['create_dt']);?>
		</td>
		<td class="primary" valign="top" >
			<?php echo T('trans_type|'.H($trans['transaction_type_cd']));?>
		</td>
		<td class="primary" valign="top" >
			<?php echo H($trans['description']);?>
		</td>
		<td class="primary" valign="top" >
			<?php echo $LOC->moneyFormat($trans['amount']);?>
		</td>
		<td class="primary" valign="top" >
			<?php echo $LOC->moneyFormat($bal);?>
		</td>
	</tr>
<?php
		}
	}

?>
	</tbody>
</table>
</fieldset>
<?php

	Page::footer();
