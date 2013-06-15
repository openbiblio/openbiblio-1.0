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

<h3><?php echo T("Item Check-In"); ?></h3>

<!-- ------------------------------------------------------------------------ -->
<div id="ckinDiv">
	<form id="chekinForm" name="chekinForm" >
	<fieldset>
		<legend><?php echo T("Check In"); ?></legend>
		<label for="barcodeNmbr"><?php echo T("Barcode Number").":"; ?></label>
		<input type="text" id="barcodeNmbr" name="barcodeNmbr" size="18" />
		<input type="hidden" id="ckinMode" name="mode" value="doItemCheckin">
		<input type="submit" id="addToCrtBtn" value="<?php echo T("Add to Shelving Cart"); ?>" />
		<br />
		<input type="text" readonly id="ckinTitle" size="100" />
	</fieldset>
	</form>

	<form id="shelvingForm" name="shelvingForm" >
	<fieldset>
		<legend><?php echo T("Shelving Cart Contents"); ?></legend>
		<button class="shelvItemBtn"><?php echo T("Shelve selected items"); ?></button>
		<button class="markAllBtn"><?php echo T("Mark all"); ?></button>
		<button class="clerAllBtn"><?php echo T("Clear all"); ?></button>

		<fieldset>
			<table id="shelvingList">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th><?php echo T("Checked In"); ?></th>
						<th><?php echo T("Barcode"); ?></th>
						<th><?php echo T("Title"); ?></th>
					</tr>
				</thead>
				<tbody class="striped" ><!-- filled in by server --></tbody>
			</table>
			<input type="hidden" name="mode" value="doShelveItem">
		</fieldset>

		<button class="shelvItemBtn"><?php echo T("Shelve selected items"); ?></button>
		<button class="markAllBtn" ><?php echo T("Mark all"); ?></button>
		<button class="clerAllBtn" ><?php echo T("Clear all"); ?></button>
	</fieldset>
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<!-- ------------------------------------------------------------------------ -->
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	//include_once(REL(__FILE__,'./mbrEditorJs.php'));
	include_once(REL(__FILE__,'../circ/checkinJs.php'));
?>	

</body>
</html>
