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

<h3><?php echo T('Circulation'); ?></h3>
<form name="barcodesearch" method="post" action="../circ/shelving_cart.php">
<fieldset>
<legend><?php echo T("Copy Check In"); ?></legend>
<table class="primary">
	<tr>
		<td nowrap="true" class="primary">
			<label for="barcodeNmbr"><?php echo T("Barcode Number:"); ?></label>
			<?php echo inputfield('text',"barcodeNmbr",NULL,array('size'=>'18','maxlength'=>'18')); ?>
			<?php echo inputfield('hidden',"mbrid",$mbrid); ?>
			<input type="submit" value="<?php echo T("Add to Shelving Cart"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<?php
	if (isset($_GET["msg"])){
		echo '<p class="error">'.H($_GET["msg"]).'</p>';
	}
?>

<form name="checkinForm" method="post" action="../circ/checkin.php">
<input type="hidden" name="massCheckin" value="N">

<fieldset>
<legend><?php echo T("Current Shelving Cart List"); ?></legend>
<a href="javascript:checkin('N')"><?php echo T("Check in selected items"); ?></a> |
<a href="javascript:checkin('Y')"><?php echo T("Check in all items"); ?></a><br />
	<table>
	<thead>
	<tr>
		<th valign="top" nowrap="yes" align="left">&nbsp;</th>
		<th valign="top" nowrap="yes" align="left"><?php echo T("Date Scanned"); ?></th>
		<th valign="top" nowrap="yes" align="left"><?php echo T("Barcode"); ?></th>
		<th valign="top" nowrap="yes" align="left"><?php echo T("Title"); ?></th>
	</tr>
	</thead>
<?php
	#****************************************************************************
	#*  Search database for biblio copy data
	#****************************************************************************
	
	// Seems like a better way would be to test just inside the fieldset
	// and print this message instead of the table and associated buttons
	// -- Fred
	
	$copies = new Copies;
	$scart = $copies->getShelvingCart();
	if ($scart->count() == 0) {
?>
		<tbody class="unStriped">
		<td class="primary" align="center" colspan="4">
			<?php echo T("No copies are currently in shelving cart status."); ?>
		</td>
		</tbody>
		<tbody class="striped">
<?php
	} else {
		$history = new History;
		$biblios = new Biblios;
		while ($copy = $scart->next()) {
			$biblio = $biblios->getOne($copy['bibid']);
			$status = $history->getOne($copy['histid']);
?>
{}
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
		echo "</tbody>";
	}
?>
</table>

<a href="javascript:checkin('N')"><?php echo T("Check in selected items"); ?></a> |
<a href="javascript:checkin('Y')"><?php echo T("Check in all items"); ?></a>
</fieldset>
</form>


<?php

	 ;

/*
	$endTm = getmicrotime();
	trigger_error ("Footer: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
*/
