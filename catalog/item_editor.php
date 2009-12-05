<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

?>
<fieldset>
<legend><?php echo T("Item"); ?></legend>
<table id="biblioFldTbl" class="primary">
	<tbody id="nonMarcBody">
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup>
			<label for="mediaType"><?php echo T("Type of Material:"); ?></label>
		</td>
		<td valign="top" class="primary">
				<span id="itemMediaTypes">to be filled by server</span>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup>
			<label for="collectionCd"><?php echo T("Collection:"); ?></label>
		</td>
		<td valign="top" class="primary">
			<span id="itemEditColls">to be filled by server</span>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary" valign="top">
			<label for="opacFlg"><?php echo T("Show in OPAC:"); ?></label>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('checkbox','opacFlg','Y',NULL,NULL); ?>
		</td>
	</tr>
	<tr>
		<td colspan="1" nowrap="true" class="primary">
			<b><?php echo T("Marc Fields:"); ?></b>
		<td colspan="1" nowrap="true" class="primary">
			<b><?php echo T("Local_Data"); ?></b>
		<td id="onlnColTitle" colspan="1" nowrap="true" class="primary filterable">
			<b><?php echo T("On-Line_Data"); ?></b>
		</td>
	</tbody>

	<tbody id="marcBody" class="striped">
	</tr>
	</tbody>
</table>
</fieldset>
