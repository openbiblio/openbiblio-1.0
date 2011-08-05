<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "biblio/editstock";
	$focus_form_name = "editStockForm";
	$focus_form_field = "price";
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Stock.php"));


	if (isset($_GET["bibid"])){
		$bibid = $_GET["bibid"];
		$stock = new Stock;
		$stock_info = $stock->getOne($bibid);
		$_SESSION[postVars] = $stock_info;
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
		$bibid = $postVars["bibid"];
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<form name="editStockForm" method="post" action="../catalog/biblio_stock_edit.php">
<input type="hidden" name="bibid" value="<?php echo H($bibid) ?>" />
<table class="primary">
	<tr>
		<th colspan="2" nowrap="yes" align="left">
			<?php echo T("Edit Stock Info"); ?>
		</th>
	</tr>
	<tr>
		<td nowrap="true" class="primary" align="right">
			<?php echo T("Price:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield(text, price); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary" align="right">
			<?php echo T("Vendor:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield(text, vendor); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary" align="right">
			<?php echo T("Funding Source:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield(text, fund); ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='../shared/biblio_view.php?bibid=<?php echo HURL($bibid); ?>'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
</table>
</form>

<?php

	 ;
