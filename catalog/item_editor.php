<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

?>
<p class="note"><?php echo T("Fields marked are required"); ?></p>
<input type="button" class="button itemGobkBtn" value="<?php echo T('Go Back'); ?>" />

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
		<td colspan="2" nowrap="true" class="primary">
			<b><?php echo T("USMarc Fields:"); ?></b>
		</td>
	</tr>
	</tbody>

	<tbody id="marcBody" class="striped">
	</tbody>
</table>
</fieldset>

<input type="submit" id="itemSubmitBtn" value="<?php echo T("Submit"); ?>" class="button" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="<?php echo T("Go Back"); ?>" class="button itemGobkBtn" />
