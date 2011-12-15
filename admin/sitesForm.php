<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "sites";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	//require_once(REL(__FILE__, "../classes/Report.php"));
	//require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	//require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	//require_once(REL(__FILE__, "../classes/Links.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"><?php echo T('Sites'); ?></h3>

<div id="listDiv">
<h5 id="updateMsg"></h5>
<br />
<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList" name="showList"">
	<thead>
  	<tr>
  	  <th colspan="1">&nbsp;</th>
 			<th><?php echo T("Name"); ?></th>
			<th><?php echo T("Code"); ?></th>
			<th><?php echo T("Location"); ?></th>
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
<input type="submit" class="newBtn" value="<?php echo T("Add New"); ?>" />
</form>
</div>

<div id="editDiv">
<form id="editForm" name="editForm">
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
      <input id="code" name="code" type="text" size="32" maxlength="20" />
    </li>
    <li>
      <label for="calendar"><?php echo T("Calendar"); ?>:</label>
      <!--input id="calendar" name="calendar" type="number" pattern="[1-9]{2}" min="1" max="99" 
					title="number 0-99" size="32" required aria-required="true" />
      <span class="reqd">*</span-->
      <select id="calendar" name="calendar" > </select>
		</li>
    <li>
      <label for="address1"><?php echo T("Address Line 1"); ?>:</label>
      <input id="address1" name="address1" type="text" size="32" />
    </li>
    <li>
      <label for="address2"><?php echo T("Address Line 2"); ?>:</label>
      <input id="address2" name="address2" type="text" size="32" />
    </li>
    <li>
      <label for="city"><?php echo T("City"); ?>:</label>
      <input id="city" name="city" type="text" size="32" />
    </li>
    <li>
      <label for="state"><?php echo T("State"); ?>:</label>
      <select id="state" name="state" > </select>
    </li>
    <li>
      <label for="zip"><?php echo T("Zip Code"); ?>:</label>
      <input id="zip" name="zip" type="text" size="32" />
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
      <label for="email"><?php echo T("Email"); ?>:</label>
      <input id="email" name="email" type="email" size="32" />
    </li>
    <li>
      <label for="delivery_note"><?php echo T("DeliveryNote"); ?>:</label>
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
    <li><input type="submit" id="addBtn" value="Add" /></li>
    <li><input type="submit" id="updtBtn" value="Update" /></li>
    <li><input type="button" id="cnclBtn" value="Cancel" /></li>
    <li><input type="submit" id="deltBtn" value="Delete" /></li>
	</ul>
</fieldset>
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/ListJs.php"));
	require_once(REL(__FILE__, "sitesJs.php"));
?>	
