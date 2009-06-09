<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/CopyStates.php"));
	require_once(REL(__FILE__, "../model/History.php"));
	require_once(REL(__FILE__, "../model/Bookings.php"));

	function showCopyInfo($bibid, $typeinfo) {
?>
		<a href="../catalog/biblio_copy_new_form.php?bibid=<?php echo H($bibid); ?>">
			<?php echo T("Add New Copy"); ?></a><br />
		<?php
		$copyCols=7;

		$copies = new Copies;
		$bcopies = $copies->getMatches(array('bibid'=>$bibid));
		$copy_states = new CopyStates;
		$states = $copy_states->getSelect();
?>

<h1><?php echo T("Copy Information"); ?></h1>
<table class="primary">
	<tr>
		<th colspan="2" nowrap="yes">
			<?php echo T("Function"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Barcode"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Description"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Status"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Status Dt"); ?>
		</th>
		<th align="left" nowrap="yes">
			<?php echo T("Due Back"); ?>
		</th>
	</tr>
	<?php
		if ($bcopies->count() == 0) { ?>
			<tr>
				<td valign="top" colspan="<?php echo $copyCols; ?>" class="primary" colspan="2">
					<?php echo T("No copies have been created."); ?>
				</td>
			</tr>
		<?php } else {
			$row_class = "primary";
			$history = new History;
			$bookings = new Bookings;
			while ($copy = $bcopies->next()) {
				$status = $history->getOne($copy['histid']);
				$booking = $bookings->getByHistid($copy['histid']);
	?>
		<tr>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<a href="../catalog/biblio_copy_edit_form.php?bibid=<?php echo HURL($copy['bibid']); ?>&amp;copyid=<?php echo HURL($copy['copyid']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("edit"); ?></a>
			</td>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<a href="../catalog/biblio_copy_del_confirm.php?bibid=<?php echo HURL($copy['bibid']); ?>&amp;copyid=<?php echo HURL($copy['copyid']); ?>" class="<?php echo H($row_class); ?>"><?php echo T("del"); ?></a>
			</td>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<?php echo H($copy['barcode_nmbr']);?>
			</td>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<?php echo H($copy['copy_desc']); ?>
			</td>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<?php echo H($states[$status['status_cd']]); ?>
			</td>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<?php echo H($status['status_begin_dt']); ?>
			</td>
			<td valign="top" class="<?php echo H($row_class); ?>">
				<?php
					if ($booking) {
						echo '<a href="../circ/booking_view.php?bookingid='.HURL($booking['bookingid']).'">';
						echo H($booking['due_dt']);
						echo '</a>';
					}
				?>
			</td>
		</tr>
	<?php
				# swap row color
				if ($row_class == "primary") {
					$row_class = "alt1";
				} else {
					$row_class = "primary";
				}
			}
		} ?>
	<tr><td colspan="<?php echo H($copyCols); ?>" align="right" style="padding: 4px">
		<a href="../catalog/biblio_copy_new_form.php?reset=Y&amp;bibid=<?php echo HURL($bibid) ?>" class="small_button">
			<?php echo T("Add New"); ?>
		</a>
	</td></tr>
</table>
<?php
	}
