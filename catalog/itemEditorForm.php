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
		<td><label for="materialCd"><?php echo T("Media Type:"); ?></label></td>
		<td><span id="itemMediaTypes">to be filled by server</span></td>
		<td rowspan="3" class="online filterable"><div id="onlineMsg"></div></td>
	</tr>
	<tr>
		<td><label for="collectionCd"><?php echo T("Collection:"); ?></label></td>
		<td><span id="itemEditColls">to be filled by server</span></td>
	</tr>
	<tr>
		<td><label for="opacFlg"><?php echo T("Show in OPAC:"); ?></label></td>
		<td><?php echo inputfield('checkbox','opacFlg','Y',NULL,'Y'); ?></td>
	</tr>
	<tr>
		<td colspan="1"><b><?php echo T("MARC Fields"); ?></b></td>
		<td colspan="1"><b><?php echo T("Local Data"); ?></b></td>
		<td id="onlnColTitle" colspan="1" class="filterable">
			<b><?php echo T("Online Data"); ?></b>
		</td>
	</tr>
	</tbody>

	<tbody id="marcBody" class="striped">
	  <!-- to be filled by server -->
	</tbody>
</table>
</fieldset>

