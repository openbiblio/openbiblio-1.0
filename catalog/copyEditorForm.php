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
<legend><?php echo T("Add New Copy"); ?></legend>
<table id="copyTbl" >
	<tbody class="unstriped">
	<tr>
		<td><label for="barcode_nmbr"><?php echo T("Barcode Number"); ?></label></td>
		<td>
			<input id="barcode_nmbr" name="barcode_nmbr" type="number" size="20" pattern="[0]{10}" title="zero-filled barcode" required aria-required="true" />
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
		<td><label for="copy_desc"><?php echo T("Description"); ?></label></td>
		<td><input id="copy_desc" name="copy_desc" type="text" size="40" /></td>
	</tr>
<?php // Not to be shown when in normal (non multisite mode)
if($_SESSION['multi_site_func'] > 0){
?>
	<tr>
		<td><label for="copy_site"><?php echo T("Location"); ?></label></td>
		<td><select id="copy_site" name="copy_site">to be filled in by server</span></td>
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
		while ($row = $rows->next()) {
			echo "<tr>";
			echo "<td nowrap=\"true\" valign=\"top\"><label for=\"custom_". $row["code"] . "\">" . T($row["description"]) . "</td>";
			echo "<td valign=\"top\" \">" . inputfield('text', 'custom_'.$row["code"], "",NULL) . "</td>";
			echo "</tr>";
		}					
	?>
	</tr>
	<tr>
		<td colspan="2">
			<input type="hidden"  name="bibid" value="" />
			<input type="hidden"  name="mode" value="" />
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
