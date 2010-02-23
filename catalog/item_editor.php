<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
ie = {
	init: function () {
		ie.itemSubmitBtn = $('#itemSubmitBtn');
	  $('tbody#marcBody .reqd').bind('change',null,ie.validate);
	},

	disableItemSubmitBtn: function () {
	  ie.itemSubmitBtnClr = ie.itemSubmitBtn.css('color');
	  ie.itemSubmitBtn.css('color', '#888888');
		ie.itemSubmitBtn.disable();
	},
	enableItemSubmitBtn: function () {
	  ie.itemSubmitBtn.css('color', ie.itemSubmitBtnClr);
		ie.itemSubmitBtn.enable();
	},

	validate: function () {
		// verify all 'required' fields are populated
		var errs = 0;
		$('tbody#marcBody .reqd').each(function () {
		  var $fld = $(this);
		  var reqdVal = $fld.val();
		  if ((reqdVal == '') || (reqdVal == "<?php echo T('REQUIRED FIELD'); ?>")) {
				$fld.val('REQUIRED FIELD');
				$fld.addClass('error')
				errs++;
			} else {
				$fld.removeClass('error')
			}
		});
		if (errs > 0) {
		  ie.disableItemSubmitBtn()
			return false;
		} else {
		  ie.enableItemSubmitBtn()
		  return true;
		}
	}
};
$(document).ready(ie.init);

</script>

<?php

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
		<td rowspan="3" class="online filterable"><div id="onlineMsg"></div></td>
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
			<b><?php echo T("MARC Fields:"); ?></b>
		<td colspan="1" nowrap="true" class="primary">
			<b><?php echo T("Local Data"); ?></b>
		<td id="onlnColTitle" colspan="1" nowrap="true" class="primary filterable">
			<b><?php echo T("Online Data"); ?></b>
		</td>
	</tr>
	</tbody>

	<tbody id="marcBody" class="striped">
	</tbody>
</table>
</fieldset>
