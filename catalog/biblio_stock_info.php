<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../model/Stock.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));

	function showStockInfo($bibid, $typedata) {
		$stock = new Stock;
		$rows = $stock->getMatches(array('bibid'=>$bibid));
		if ($rows->count() == 0) {
			$stock->insert(array('bibid'=>$bibid, 'count'=>0));
		}
		$bibstock = $stock->getOne($bibid);
?>

<table class="primary">
	<tr>
		<th align="left" nowrap="yes">
			<?php echo T("In Stock"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Vendor"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Funding Source"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Price"); ?>
		</th>
	</tr>
	<tr>
		<td align="right" class="primary">
			<?php
				if ($typedata['restock_threshold'] > $bibstock['count']) {
					echo '<span style="color: #ff0000;">'.H($bibstock['count']).'</span>';
				} else {
					echo H($bibstock['count']);
				}
			?>
		</td>
		<td class="primary">
			<?php echo H($bibstock['vendor']); ?>
		</td>
		<td class="primary">
			<?php echo H($bibstock['fund']); ?>
		</td>
		<td class="primary">
			<?php echo H($bibstock['price']); ?>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="right" style="padding: 4px">
			<a class="small_button" href="../catalog/biblio_stock_edit_form.php?bibid=<?php echo HURL($bibid) ?>">
				<?php echo T("Edit"); ?>
			</a>
		</td>
	</tr>
</table>
<p>
<form name="stockedit" method="post" action="../catalog/biblio_stock_edit.php">
<input type="hidden" name="bibid" value="<?php echo H($bibid) ?>" />
<table class="primary">
	<tr>
		<th valign="top" nowrap="nowrap" align="left">
			<?php echo T("Change Stock:"); ?>
		</th>
	</tr>
	<tr>
		<td nowrap="nowrap" class="primary">
			<input type="text" name="items" size="10" />
			<input type="submit" name="add" value="<?php echo T("Add"); ?>" class="button" />
			<input type="submit" name="remove" value="<?php echo T("Remove"); ?>" class="button" />
		</td>
	</tr>
</table>
</form>
</p>
<?php
	}
