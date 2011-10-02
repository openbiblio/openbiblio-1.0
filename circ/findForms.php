<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "searchform";
	$restrictToMbrAuth = TRUE;
	if ($_SESSION['mbrBarcode_flg'] == 'Y') {
		$focus_form_name = "barCdSrchForm";
		$focus_form_field = "searchByBarcd";
	} else {
		$focus_form_name = "nameSrchForm";
		$focus_form_field = "searchByName";
	}

	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3><?php echo T("Current Members"); ?></h3>

<!-- ------------------------------------------------------------------------ -->
<div id="searchDiv">
	<form id="barCdSrchForm" name="barCdSrchForm" action="">
	<fieldset>
	<legend><?php echo T("Find Member by Barcode"); ?></legend>
	<table>
		<tr>
			<td nowrap="true">
				<label for="searchByBarcd"><?php echo T("Barcode Number:");?></label>
				<input type="text" id="searchByBarcd" name="searchByBarcd" size="20" />
				<input type="submit" id="barCdSrchBtn" value="<?php echo T("Search"); ?>" />
			</td>
		</tr>
	</table>
	</fieldset>
	</form>
	
	<form id="nameSrchForm" name="nameSrchForm" action="">
	<fieldset>
	<legend><?php echo T("Find Member by Name"); ?></legend>
	<table>
		<tr>
			<td nowrap="true">
				<label for="searchByName"><?php echo T("Name Contains:");?></label>
				<input type="text" id="searchByName" name="searchByName" size="20" />
				<input type="submit" id="nameSrchBtn" value="<?php echo T("Search"); ?>" />
			</td>
		</tr>
	</table>
	</fieldset>
	</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="mbrDiv">
	<input type="button" class="gobkBtn" value="<?php echo T('Go Back'); ?>" />
	<fieldset id="identity">
		<legend><?php echo T("Member Information");?></legend>
		<label for="mbrName"><?php echo T("Name");?>:</label>
		<input type="text" readonly="readonly" id="mbrName" />
		<input type="button" value="<?php echo T("Details");?>" id="mbrDetlBtn" />
		<input type="button" value="<?php echo T("Account");?>" id="mbrAcntBtn" />
		<input type="button" value="<?php echo T("History");?>" id="mbrHistBtn" />
		<br />
		<label for="mbrSite"><?php echo T("Site");?>:</label>
		<input type="text" readonly id="mbrSite" />
		<br />
		<label for="MbrCardNo"><?php echo T("Card Number");?>:</label>
		<input type="text" readonly id="mbrCardNo" />
	</fieldset>
	
	<fieldset id="newLoans">
		<legend for="newBarcd"><?php echo T("Check Out");?></legend>
		<label><?php echo T("Barcode Number");?>:</label>
		<input type="text" id="ckOutBarcd" size="20" />
		<input type="button" value="<?php echo T("Check Out");?>" id="chkOutBtn" />
		<input type="text" readonly class="error" id="chkOutMsg" /> 
	</fieldset>
	
	<fieldset id="onLoan">
		<legend><?php echo T("Items Currently Checked Out");?></legend>
		<table id="chkOutList">
			<thead>
			<tr>
				<th class="center"><?php echo T("Checked Out");?></th>
				<th class="center"><?php echo T("Media");?></th>
				<th class="center"><?php echo T("Barcode");?></th>
				<th class="center"><?php echo T("Title");?></th>
				<th class="center"><?php echo T("Due Back");?></th>
				<th class="center"><?php echo T("Days Late");?><th>
			</tr>
			</thead>
			<tbody class="striped"></tbody>
		</table>
	</fieldset>
	
	<fieldset id="newBooking">
		<legend><?php echo T("Make Booking");?></legend>
			<select name="searchType">
				<option value="keyword">Keyword</option>
				<option value="title">Title</option>
				<option value="subject">Subject</option>
				<option value="series">Series</option>
				<option value="publisher">Publisher</option>
				<option value="callno" selected>Item Number</option>
			</select>
			<input type="text" name="bkSrchTxt" size="30" maxlength="256" />
			<input type="hidden" name="sortBy" value="default" />
			<!--input type="hidden" name="tab" value="circ" />
			<input type="hidden" name="lookup" value="Y" /-->
			<input type="button" value="Search" id="bkgBtn" />
	</fieldset>
	
	<fieldset id="newHolds">
		<legend><?php echo T("Place on Hold");?></legend>
			<label for="hldBarcdNmbr"><?php echo T("Barcode Number");?></label>
			<input type="text" id="holdBarcodeNmbr" size="20" />
			<!--a href="javascript:popSecondaryLarge('../opac/index.php?lookup=Y')"-->search<!--/a-->
			<input type="hidden" name="mbrid" value="" />
			<input type="hidden" name="classification" value="" />
			<input type="button" value="Hold" id="holdBtn" />
	</fieldset>
	
	<fieldset id="onHold">
		<legend><?php echo T("Items Currently On Hold");?></legend>
		<table id="holdList">
		<thead>
			<tr>
				<td>&nbsp;</td>
				<th class="center"><?php echo T("On Hold");?></th>
				<th class="center"><?php echo T("Barcode");?></th>
				<th class="center"><?php echo T("Title");?></th>
				<th class="center"><?php echo T("Status");?></th>
				<th class="center"><?php echo T("Due Back");?></th>
			</tr>
		</thead>
		<tbody class="striped"></tbody>
		</table>
	</fieldset>
	<input type="button" class="gobkBtn" value="<?php echo T('Go Back'); ?>" />
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="listDiv">
	<h5><?php echo T("Search Results"); ?></h5>
	<div id="results_found">
		<?php //echo T('biblioSearchMsg', array('nrecs'=>$rpt->count(), 'start'=>1, 'end'=>25)); ?>
	</div>
	<table>
	<thead>
	<tr>
		<td><input type="button" class="gobkBtn" value="<?php echo T('Go Back'); ?>" /></td>
		<td width="80%" align="right">
			<input type="button" class="goPrevBtn PgBtn" value="<?php echo T('Previous Page'); ?>">
			<span class="rsltQuan"></span>
			<input type="button" class="goNextBtn PgBtn" value="<?php echo T('Next Page'); ?>">
		</td>
	</tr>
	</thead>
	<tbody>
	<tr>
	  <td colspan="3">
			<fieldset>
				<span id="resultsArea"></span>
				<fieldset>
					<table id="listTbl">
						<tbody id="srchRslts" class="striped">
						</tbody>
					</table>
				</fieldset>
			</fieldset>
		</td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<td>
			<input type="button" class="gobkBtn" value="<?php echo T('Go Back'); ?>" />
		</td>
		<td>&nbsp;</td>
		<td width="80%" align="right">
			<input type="button" class="goPrevBtn PgBtn" value="<?php echo T('Previous Page'); ?>">
			<span class="rsltQuan"></span>
			<input type="button" class="goNextBtn PgBtn" value="<?php echo T('Next Page'); ?>">
		</td>
	</tr>
	</tfoot>
	</table>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="editDiv">
<form id="editForm" name="editForm" >
	<h5 class="note"> Fields marked with <span class="reqd">*</span> are required.</h5>
	<fieldset>
		<legend id="editHdr"></legend>
		<table>
		<tbody>
			<tr>
				<td><label for="siteid">Site</label></td>
				<td>
					<select name="siteid" id="siteid" >
						<option value="1" >Home</option>
						<option value="2"  selected="selected">LaPlante Library</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="barcd_nmbr">Card Number</label></td>
				<td><input type="text" name="barcd_nmbr" required size="20" max="20" id="barcd_nmbr" />
						<span class="reqd">*</span></td>
			</tr>
			<tr>
				<td><label for="last_name">Last Name</label></td>
				<td><input type="text" name="last_name" required size="20" max="20" id="last_name" />
						<span class="reqd">*</span></td>
			</tr>
			<tr>
				<td><label for="first_name">First Name</label></td>
				<td><input type="text" name="first_name" required size="20" max="20" id="first_name" />
						<span class="reqd">*</span></td>
			</tr>
			<tr>
				<td><label for="address1">Address Line 1</label></td>
				<td><input type="text" name="address1" size="40" max="128" id="address1" /></td>
			</tr>
			<tr>
				<td><label for="address2">Address Line 2</label></td>
				<td><input type="text" name="address2" size="40" max="128" id="address2" /></td>
			</tr>
			<tr>
				<td><label for="city">City</label></td>
				<td><input type="text" name="city" size="30" max="50" id="city" /></td>
			</tr>
			<tr>
				<td><label for="state">State</label></td>
				<td>
					<select name="state" id="state" >
						<option value="CA" >California</option>
						<option value="lin" >Lincoln</option>
						<option value="ME" >Maine</option>
						<option value="som" >Somerset</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="zip">Zip Code</label></td>
				<td><input type="text" name="zip" size="10" max="10" id="zip" /></td>
			</tr>
			<tr>
				<td><label for="zip_ext">Zip Code ext</label></td>
				<td><input type="text" name="zip_ext" size="10" max="10" id="zip_ext" /></td>
			</tr>
			<tr>
				<td><label for="home_phone">Home Phone</label></td>
				<td><input type="text" name="home_phone" required size="15" max="15" id="home_phone" />
						<span class="reqd">*</span></td>
			</tr>
			<tr>
				<td><label for="work_phone">Work Phone</label></td>
				<td><input type="text" name="work_phone" size="15" max="15" id="work_phone" /></td>
			</tr>
			<tr>
				<td><label for="email">Email Address</label></td>
				<td><input type="email" name="email" size="40" max="128" id="email" /></td>
			</tr>
			<tr>
				<td><label for="classification">Classification</label></td>
				<td>
					<select name="classification" id="classification" >
						<option value="4" >business</option>
						<option value="3" >Denied</option>
						<option value="1" >family</option>
						<option value="2" >friends</option>
					</select>
				</td>
			</tr>
		</tbody>
		<tbody id="customEntries">
		</tbody>
	
		<tfoot>
			<tr>
				<td colspan="2">
					<input type="hidden" id="mode" name="mode" value="updateMember">
					<input type="hidden" id="mbrid" name="mbrid" value="">
				</td>
			</tr>
			<tr>
				<td colspan="2" cl>
					<input type="submit" id="addMbrBtn" value="Add" />
					<input type="submit" id="updtMbrBtn" value="Update" />
					<input type="button" id="cnclMbrBtn" value="Cancel" />
					<input type="button" id="deltMbrBtn" value="Delete" />
				</td>
			</tr>
		</tfoot>
		</table>
	</fieldset>
</form>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="biblioDiv">
	<p id="rsltMsg" class="error"></p>
	<ul class="btnRow">
		<li><input type="button" class="gobkBiblioBtn" value="<?php echo T('Go Back'); ?>" /></li>
	</ul>
	
		<?php include(REL(__FILE__,"../catalog/itemDisplayForm.php")); ?>

	<ul class="btnRow">
		<li><input type="button" class="gobkBiblioBtn" value="<?php echo T('Go Back'); ?>"></li>
	</ul>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<!-- ------------------------------------------------------------------------ -->
<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
	
	//include_once(REL(__FILE__,'./mbrEditorJs.php'));
	include_once(REL(__FILE__,'../catalog/itemDisplayJs.php'));
	include_once(REL(__FILE__,'./findJs.php'));
?>	

<?php/*
INSERT INTO `openbibliowork`.`settings` (
`name` ,`position` ,`title` ,`type` ,`width` ,`type_data` ,`validator` ,`value` ,`menu`)
VALUES (
'mbr_barcode_width', '11', 'Member Card No Width', 'int', NULL , NULL , NULL , '13', 'admin'
);
*/
?>

