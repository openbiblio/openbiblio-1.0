<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "checkin";
	$focus_form_name = "barcodesearch";
	$focus_form_field = "barcodeNmbr";

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3><?php echo T('Item Check-In'); ?></h3>

<!-- ------------------------------------------------------------------------ -->
<div id="ckinDiv">
	<form id="chekinForm" name="chekinForm" >
	<fieldset>
		<legend><?php echo T("Check In"); ?></legend>
		<table class="primary">
		<tr>
			<td nowrap="true" class="primary">
				<label for="barcodeNmbr"><?php echo T("Barcode Number:"); ?></label>
				<input type="text" id="barcodeNmbr" name="barcodeNmbr" size="18" />
				<input type="hidden" id="ckinMode" name="mode" value="doShelfItem">
				<input type="submit" id="addToCrtBtn" value="<?php echo T("Add to Shelving Cart"); ?>" />
			</td>
		</tr>
		</table>
	</fieldset>
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<!-- ------------------------------------------------------------------------ -->
<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
	
	//include_once(REL(__FILE__,'./mbrEditorJs.php'));
	include_once(REL(__FILE__,'./checkinJs.php'));
?>	
