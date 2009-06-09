<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "checkin";
	$focus_form_name = "barcodesearch";
	$focus_form_field = "barcodeNmbr";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/History.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>


<!--**************************************************************************
		*  Javascript to post checkin form
		************************************************************************** -->
<script type="text/javascript">
<!--
function checkin(massCheckinFlg)
{
	document.checkinForm.massCheckin.value = massCheckinFlg;
	document.checkinForm.submit();
}
-->
</script>


<form name="barcodesearch" method="post" action="../circ/shelving_cart.php">
<table class="primary">
	<tr>
		<th valign="top" nowrap="yes" align="left">
			<?php echo T("Copy Check In"); ?>
		</th>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Barcode Number:"); ?>
			<?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
			<input type="hidden" name="mbrid" value="<?php echo $mbrid;?>" />
			<input type="submit" value="<?php echo T("Add to Shelving Cart"); ?>" class="button" />
		</td>
	</tr>
</table>
</form>

<?php
	if (isset($_GET["msg"])){
		echo '<p class="error">'.H($_GET["msg"]).'</p>';
	}
?>

<form name="checkinForm" method="post" action="../circ/checkin.php">
<input type="hidden" name="massCheckin" value="N">
<a href="javascript:checkin(N)"><?php echo T("Check in selected items"); ?></a> |
<a href="javascript:checkin(Y)"><?php echo T("Check in all items"); ?></a><br /><br />
<table class="primary">
	<tr>
		<th valign="top" colspan="5" nowrap="yes" align="left">
			<?php echo T("Current Shelving Cart List"); ?>
		</th>
	</tr>
	<tr>
		<th valign="top" nowrap="yes" align="left">
			&nbsp;
		</th>
		<th valign="top" nowrap="yes" align="left">
			<?php echo T("Date Scanned"); ?>
		</th>
		<th valign="top" nowrap="yes" align="left">
			<?php echo T("Barcode"); ?>
		</th>
		<th valign="top" nowrap="yes" align="left">
			<?php echo T("Title"); ?>
		</th>
	</tr>

<?php
	#****************************************************************************
	#*  Search database for biblio copy data
	#****************************************************************************
	$copies = new Copies;
	$scart = $copies->getShelvingCart();
	if ($scart->count() == 0) {
?>
		<td class="primary" align="center" colspan="4">
			<?php echo T("No copies are currently in shelving cart status."); ?>
		</td>
<?php
	} else {
		$history = new History;
		$biblios = new Biblios;
		while ($copy = $scart->next()) {
			$biblio = $biblios->getOne($copy['bibid']);
			$status = $history->getOne($copy['histid']);
?>
	<tr>
		<td class="primary" valign="top" align="center">
			<input type="checkbox" name="bibid=<?php echo HURL($copy['bibid']);?>&amp;copyid=<?php echo HURL($copy['copyid']);?>" value="copyid">
		</td>
		<td class="primary" valign="top" nowrap="yes">
			<?php echo $status['status_begin_dt'];?>
		</td>
		<td class="primary" valign="top" >
			<?php echo $copy['barcode_nmbr'];?>
		</td>
		<td class="primary" valign="top" >
			<?php echo H($biblio['marc']->getValue('245$a'));?>
		</td>
	</tr>
<?php
		}
	}
?>
</table>
<br />
<a href="javascript:checkin(N)"><?php echo T("Check in selected items"); ?></a> |
<a href="javascript:checkin(Y)"><?php echo T("Check in all items"); ?></a>
</form>


<?php

	Page::footer();

/*
	$endTm = getmicrotime();
	trigger_error ("Footer: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
*/
