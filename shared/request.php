<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$nav = "request";
	$tab = "opac";
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));

	$rpt = Report::create('biblio_cart');
	if (!$rpt) {
		Fatal::internalError(T("Unexpected error creating report"));
	}
	$rpt->init(array());
	if ($rpt->count() == 0) {
		header("Location: ../shared/req_cart.php");
		exit();
	}
	Page::header_opac(array('nav'=>$nav, 'title'=>''));
?>
<form method="post" action="../shared/request_send.php">
	<p style="color: #ff0000;"><?php echo T('requestFieldsReqd'); ?></p>
	<p>
	<table>
		<tr>
			<td align="right"><?php echo T("School"); ?><span style="color: #ff0000;">*</span></td>
			<td><?php echo inputfield("text", "school"); ?></td>
		</tr>
		<tr>
			<td align="right"><?php echo T("Name"); ?><span style="color: #ff0000;">*</span></td>
			<td><?php echo inputfield("text", "name"); ?></td>
		</tr>
		<tr>
			<td align="right"><?php echo T("Grade"); ?><span style="color: #ff0000;">*</span></td>
			<td><?php echo inputfield("text", "grade"); ?></td>
		</tr>
		<tr>
			<td align="right"><?php echo T("Patron #"); ?></td>
			<td><?php echo inputfield("text", "number"); ?></td>
		</tr>
	</table>
	</p>
<table class="primary">
	<tr>
		<th valign="top" nowrap="yes" align="left" colspan="4">
			<?php echo T("Item(s) Requested"); ?>
		</th>
	</tr>
	<tr>
		<td valign="middle" align="center" class="primary"><b><?php echo T("Item#"); ?></b></td>
		<td valign="middle" align="center" class="primary"><b><?php echo T("Title"); ?></b></td>
		<td valign="middle" align="center" class="primary"><b><?php echo T("Requested Delivery Date"); ?><br /><small>(mm/dd/yy)</small></b></td>
		<td valign="middle" align="center" class="primary"><b><?php echo T("Soonest Delivery<br />Date Available"); ?></b></td>
	</tr>
	<?php
		$rown=1;
		$class = array('primary', 'alt1');
		while ($row = $rpt->each()) {
			$bibid = $row['bibid'];
	?>

	<tr>
		<td class="<?php echo H($class[$rown%2]);?>">
			<span class="small"><?php echo H($row['callno']) ?></span></td>
		<td class="<?php echo H($class[$rown%2]);?>"><a href="../shared/biblio_view.php?bibid=<?php echo H($bibid);?>&amp;tab=<?php echo H($tab);?>"><?php
				echo H($row['title']);
			?></a></td>
		<td class="<?php echo H($class[$rown%2]);?>" align="center">
			<?php echo inputfield('text', "date[$bibid]"); ?></td>
		<td class="<?php echo H($class[$rown%2]);?>" align="center">
			<?php echo inputfield('checkbox', "soonest[$bibid]", '', NULL, 'Y'); ?>
			<input type="hidden" name="keys[]" value="<?php echo H($bibid);?>" />
		</td>
	</tr>
	<?php
			$rown++;
		}
	?>
	</table>
	<h3><?php echo T("I would like the Media Center staff to:"); ?></h3>
		<table>
			<tr>
				<td class="noborder" valign="top">
					<?php echo inputfield('checkbox', 'call', '', NULL, 'Y'); ?>
				</td>
				<td class="noborder" valign="top"><?php echo T('requestCallMe'); ?><br /><?php echo T("Phone:"); ?> <?php echo inputfield('text', "phone"); ?></td>
			</tr>
			<tr>
				<td class="noborder" valign="top">
					<?php echo inputfield('checkbox', 'confirm', '', NULL, 'Y'); ?>
				</td>
				<td class="noborder" valign="top"><?php echo T('requestMailMe'); ?><br /><?php echo T("Email"); ?>: <?php echo inputfield('text', "email"); ?></td>
			</tr>
			<tr>
				<td class="noborder" valign="top">
					<?php echo inputfield('checkbox', 'alternate', '', NULL, 'Y'); ?>
				</td>
				<td class="noborder" valign="top"><?php echo T('requestAltTitles'); ?></td>
			</tr>
		</table>
	<h3><?php echo T('requestOtherNotes'); ?></h3>
	<?php echo inputfield('textarea', 'notes', '', array('cols'=>'40', 'rows'=>'5')); ?>
	<p><input type="submit" value="<?php echo T("Submit Request"); ?>" class="button" /></p>
</form>

<?php

	Page::footer();
