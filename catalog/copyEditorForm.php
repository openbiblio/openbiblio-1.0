<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));	
	require_once(REL(__FILE__, "../model/CopyStatus.php"));	
?>

	<p class="note"><?php echo T("Fields marked are required"); ?></p>
	<p id="editRsltMsg" class="error"></p>

	<form id="copyForm" name="copyForm" >
	<fieldset>
	<legend id="copyLegend"><?php echo T("Add New Copy"); ?></legend>
	<table id="copyTbl" >
		<tbody class="unstriped">
		<tr>
			<td><label for="copyBarcode_nmbr"><?php echo T("Barcode Number"); ?></label></td>
			<td>
				<input id="copyBarcode_nmbr" name="barcode_nmbr" type="number" size="20" title="zero-filled barcode" required aria-required="true" />
				<span class="reqd">*</span>
			</td>
		</tr>
		<tr>
			<td><label for="autobarco"><?php echo T("Auto Barcode"); ?></label></td>
			<td>
				<input id="autobarco" name="autobarco" type="checkbox" value="Y"
					<?php echo ($_SESSION['item_autoBarcode_flg']=='Y'?checked:''); ?> />
			</td>
		</tr>
		<tr>
			<td><label for="copyDesc"><?php echo T("Description"); ?></label></td>
			<td><input id="copyDesc" name="copy_desc" type="text" size="40" /></td>
		</tr>
	<?php // Not to be shown when in normal (non multisite mode)
	if($_SESSION['multi_site_func'] > 0){
	?>
		<tr>
			<td><label for="copySite"><?php echo T("Location"); ?></label></td>
			<td><select id="copySite" name="copy_site">to be filled in by server</span></td>
		</tr>
	<?php } ?>
		<tr>
			<td><label for="status_cd"><?php echo T("Status:");?></label></td>
			<td>
				</select>
				<?php
					$states = new CopyStatus;
					$state_select = $states->getSelect();
					// These should not be selectable
					unset($state_select[OBIB_STATUS_OUT]);
					unset($state_select[OBIB_STATUS_ON_HOLD]);
					unset($state_select[OBIB_STATUS_SHELVING_CART]);
					echo inputfield(select, status_cd, "in", null, $state_select);
				?>
			</td>
		</tr>
		<!-- Custom fields /-->
		<?php
			$BCQ = new BiblioCopyFields;
			$rows = $BCQ->getAll();
			//while ($row = $rows->fetch_assoc()) {
			while ($row = $rows->fetch_assoc()) {
				echo "<tr>";
				echo "<td nowrap=\"true\" valign=\"top\"><label for=\"copyCustom_". $row["code"] . "\">" . T($row["description"]) . "</td>";
				echo "<td valign=\"top\" \">" . inputfield('text', 'copyCustom_'.$row["code"], "",NULL) . "</td>";
				echo "</tr>";
			}
		?>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" id="copyBibid" name="bibid" value="" />
				<input type="hidden" id="copyMode" name="mode" value="" />
			</td>
		</tr>
		</tbody>

		<tfoot>
		<tr>
			<td align="center" colspan="2">
				<input type="submit" id="copySubmitBtn" value="<?php echo T("Submit"); ?>" />
				<input type="button" id="copyCancelBtn" value="<?php echo T("Cancel"); ?>" />
			</td>
		</tr>
		</tfoot>
	</table>
	</fieldset>
	</form>

<?php
	include_once(REL(__FILE__,'copyEditorJs.php'));
?>
