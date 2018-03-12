<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "sites";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"><?php echo T("Sites"); ?></h3>

<div id="listDiv" style="display: none;">
	<form role="form" id="showForm" name="showForm">
	<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
	<fieldset>
	<table id="showList" name="showList"">
		<thead>
	  	<tr>
	  	  <th colspan="1">&nbsp;</th>
	 			<th><?php echo T("Name"); ?></th>
				<th><?php echo T("Code"); ?></th>
				<th><?php echo T("Location"); ?></th>
				<th><?php echo T("Holdings"); ?></th>
			</tr>
		</thead>
		<tbody class="striped">
		  <tr><td colspan="4"><?php echo T("No sites have been defined."); ?> </td></tr>
		</tbody>
		<tfoot>
	  	<tr>
	  		<!-- spacer used to slightly seperate button from form body -->
	    	<td><input type="hidden" id="xxx" name="xxx" value=""></td>
	  	</tr>
		</tfoot>
	</table>
	</fieldset>
	<div class="btnRow gridded">
		<input type="submit" class="newBtn col1" value="<?php echo T("Add New"); ?>" />
		<input type="button" id="mergeBtn" class="col5" value="<?php echo T("Merge Sites"); ?>" />
	</div>
	</form>
</div>

<div id="extraDiv" style="display: none">
	<form role="form" id="mergeForm" name="mergeForm">
	<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
	<fieldset>
		<legend id="mergeHdr"><?php echo T("Merge Sites"); ?></legend>
		<p class="note">
			<?php echo T("note: Gaining and loosing sites MUST be different."); ?>
		</p>
	    <label for="fmSite"><?php echo T("Losing Site"); ?>:</label>
	    <select id="fmSite" name="fmSite" > </select>

	    <label for="toSite"><?php echo T("Gaining Site"); ?>:</label>
	    <select id="toSite" name="toSite" > </select>

		<br />
		<label for="limit"><?php echo T("Maximum to transfer"); ?>:</label>
		<input id="limit" type="number" name="limit" />
	</fieldset>
	<div class="btnRow gridded">
		<input type="submit" id="mergeSiteBtn" class="actnBtns col1" value="<?php echo T("Merge"); ?>" />
		<input type="button" id="cnclBtn" class="col5" value="<?php echo T("Go Back"); ?>" /></div>
	</div>
	</form>
</div>

<div id="editDiv" style="display: none;">
	<form role="form" id="editForm" name="editForm">
	<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
	<fieldset>
		<legend id="editHdr"></legend>
		<ul id="editTbl">
	    <li>
	      <label for="name"><?php echo T("Name"); ?>:</label>
	      <input id="name" name="name" type="text" size="32" required aria-required="true" />
				<span class="reqd">*</span>
			</li>
	    <li>
	      <label for="code"><?php echo T("Code"); ?>:</label>
	      <input id="code" name="code" class="addOnly" type="text" size="32" maxlength="20" required aria-required="true" />
				<span class="reqd">*</span>
	    </li>
	    <li>
	      <label for="calendar"><?php echo T("Calendar"); ?>:</label>
	      <!--input id="calendar" name="calendar" type="number" pattern="[1-9]{2}" min="1" max="99"
						title="number 0-99" size="32" required aria-required="true" />
	      <span class="reqd">*</span-->
	      <select id="calendar" name="calendar" > </select>
			</li>
	    <li>
	      <label for="address1"><?php echo T("AddressLine1"); ?>:</label>
	      <input id="address1" name="address1" type="text" size="32" />
	    </li>
	    <li>
	      <label for="address2"><?php echo T("AddressLine2"); ?>:</label>
	      <input id="address2" name="address2" type="text" size="32" />
	    </li>
	    <li>
	      <label for="city"><?php echo T("City"); ?>:</label>
	      <input id="city" name="city" type="text" size="32" required aria-required="true" />
				<span class="reqd">*</span>
	    </li>
	    <li>
	      <label for="state"><?php echo T("State"); ?>:</label>
	      <select id="state" name="state" > </select>
	    </li>
	    <li>
	      <label for="zip"><?php echo T("ZipCode"); ?>:</label>
	      <input id="zip" name="zip" type="text" size="32" />
	    </li>
	    <li>
	      <label for="country"><?php echo T("Country"); ?>:</label>
	      <input id="country" name="country" type="text" size="32" />
	    </li>
	    <li>
	      <label for="phone"><?php echo T("Phone"); ?>:</label>
	      <input id="phone" name="phone" type="tel" size="32" />
	    </li>
	    <li>
	      <label for="fax"><?php echo T("Fax"); ?>:</label>
	      <input id="fax" name="fax" type="tel" size="32" />
	    </li>
	    <li>
	      <label for="email"><?php echo T("EmailAddress"); ?>:</label>
	      <input id="email" name="email" type="email" size="32" />
	    </li>
	    <li>
	      <label for="delivery_note"><?php echo T("DeliveryNote"); ?>:</label>
				<span class="reqd">*</span>
	      <br />
	      <textarea id="delivery_note" name="delivery_note" cols="45" required aria-required="true" /></textarea>
	    </li>
	    <li>
			<input type="hidden" id="cat" name="cat" value="sites">
			<input type="hidden" id="mode" name="mode" value="">
			<input type="hidden" id="siteid" name="siteid" value="">
		</li>
		</ul>
		<ul id="btnRow">
	    <li><input type="submit" id="addBtn"  name="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" /></li>
	    <li><input type="submit" id="updtBtn" name="updtBtn" class="actnBtns" value="<?php echo T("Update"); ?>" /></li>
	    <li><input type="button" id="cnclBtn" name="cnclBtn" class="cnclBtn" value="<?php echo T("Go Back"); ?>" /></li>
	    <li><input type="submit" id="deltBtn" name="deltBtn" class="actnBtns" value="<?php echo T("Delete"); ?>" /></li>
		</ul>
	</fieldset>
	</form>
</div>

<?php
  	require_once(REL(__FILE__,'../shared/footer.php'));
	
	//require_once(REL(__FILE__, "../classes/AdminJs.php"));
	//require_once(REL(__FILE__, "sitesJs.php"));
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/sitesJs6.php"));
?>
</body>
</html>
