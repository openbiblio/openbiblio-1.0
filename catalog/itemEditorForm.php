<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
/**
 * HTML portion of the Biblio Item Editor module
 * @author Fred LaPlante
 */
?>

	<fieldset>
	<legend><?php echo T("Item"); ?></legend>
	<table id="biblioFldTbl" >
		<tbody id="nonMarcBody">
		<tr>
			<td><label for="itemMediaTypes"><?php echo T("Media Type"); ?>:</label></td>
			<td><select id="itemMediaTypes" name="materialCd">to be filled by server</select></td>
			<td rowspan="3" class="online filterable"><div id="onlineMsg"></div></td>
		</tr>
		<tr>
			<td><label for="itemEditColls"><?php echo T("Collection"); ?>:</label></td>
			<td><select id="itemEditColls" name="collectionCd">to be filled by server</select></td>
			<td><input id="editBibid" name="bibid" type="hidden" value="" /></td>
		</tr>
		<tr>
			<td><label for="opacFlg"><?php echo T("Show in OPAC"); ?>:</label></td>
			<td><?php echo inputfield('checkbox','opacFlg','Y',NULL,'Y'); ?></td>
			<td><input type="hidden" value="" /></td>
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

<?php
	include_once ("../shared/jsLibJs.php");
	include_once(REL(__FILE__,'itemEditorJs.php'));
?>
