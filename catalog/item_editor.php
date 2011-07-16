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
		<td nowrap="true">
			<label for="materialCd"><?php echo T("Type of Material:"); ?></label>
		</td>
		<td valign="top">
				<span id="itemMediaTypes">to be filled by server</span>
		</td>
		<td rowspan="3" class="online filterable"><div id="onlineMsg"></div></td>
	</tr>
	<tr>
		<td nowrap="true">
			<label for="collectionCd"><?php echo T("Collection:"); ?></label>
		</td>
		<td valign="top">
			<span id="itemEditColls">to be filled by server</span>
		</td>
	</tr>
	<tr>
		<td nowrap="true" valign="top">
			<label for="opacFlg"><?php echo T("Show in OPAC:"); ?></label>
		</td>
		<td valign="top">
			<?php echo inputfield('checkbox','opacFlg','Y',NULL,'Y'); ?>
		</td>
	</tr>
	<tr>
		<td colspan="1" nowrap="true">
			<b><?php echo T("MARC Fields"); ?></b>
		<td colspan="1" nowrap="true">
			<b><?php echo T("Local Data"); ?></b>
		<td id="onlnColTitle" colspan="1" nowrap="true" class="filterable">
			<b><?php echo T("Online Data"); ?></b>
		</td>
	</tr>
	</tbody>

	<tbody id="marcBody" class="striped">
	  <!-- to be filled by server -->
	</tbody>
</table>
</fieldset>

<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
ie = {
	init: function () {
		ie.itemSubmitBtn = $('#itemSubmitBtn');
	  ie.itemSubmitBtnClr = ie.itemSubmitBtn.css('color');
	  $('tbody#marcBody input.reqd').bind('change',null,ie.validate);
	},

	disableItemSubmitBtn: function () {
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
		  var $fld = $('#'+this.id);
		  var testVal = $.trim($fld.val());  //assure no whitespace to mess things up
		  if ((testVal == 'undefined') || ((testVal == '') && (testVal == "<?php echo T('REQUIRED FIELD'); ?>"))) {
				$('label[for="'+this.id+'"]').addClass('error');
				$fld.addClass('error').val("<?php echo T('REQUIRED FIELD'); ?>");
				errs++;
			} else {
				$('label[for="'+this.id+'"]').removeClass('error');
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
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>

