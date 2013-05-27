<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "collections";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Collections"); ?></h3>

<div id="listDiv" style="display: none;">
<h5 id="updateMsg"></h5>
<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList">
	<thead>
	  <tr>
	    <th colspan="1">&nbsp;</th>
			<th valign="top"><?php echo T("Code"); ?></th>
			<th valign="top"><?php echo T("Description"); ?></th>
			<th valign="top"><?php echo T("Type"); ?></th>
			<th valign="top"><?php echo T("Item<br />Count"); ?></th>
			<th valign="top"><?php echo T("Default"); ?></th>
		</tr>
	</thead>
	<tbody class="striped">
	  <tr><td colspan="4"><?php echo T("No collections have been defined."); ?> </td></tr>
	</tbody>
</table>
</fieldset>
<input type="submit" class="newBtn" value="<?php echo T("Add New"); ?>" />
</form>
</div>

<div id="editDiv" style="display: none;">
<form id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
	<legend id="editHdr"></legend>
	<ul id="editTbl">
    <li>
      <label for="description"><?php echo T("Description:"); ?></label>
      <input id="description" name="description" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>    
		</li>
		<li>
			<label for "onHand"><?php echo T("NumberOnHand"); ?>:</label><span id="onHand">0</span>
		</li>
    <li>
      <label for="type"><?php echo T("Collection Type"); ?>:</label>
      <select id="type" name="type" > </select>
    </li>
    <li>
      <label for="default_flg"><?php echo T("Default (Y/N)"); ?>:</label>
      <input id="default_flg" name="default_flg" type="text" size="1" value="N"
				pattern="[Y,N]" required aria-required="true" />
			<span class="reqd">*</span>
    </li>
    <li>
      <label for="days_due_back" class="circOnly"><?php echo T("Days Due Back:"); ?></label>
      <input id="days_due_back" name="days_due_back" class="circOnly" type="number" size="3" min="1" max="365" required aria-required="true" />
			<span class="reqd circOnly">*</span>    
		</li>
    <li>
      <label for="daily_late_fee" class="circOnly"><?php echo T("Daily Late Fee:"); ?></label>
      <input id="daily_late_fee" name="daily_late_fee" class="circOnly" type="number" size="5" min="0" max="99.99" required aria-required="true" />
      <!--select id="daily_late_fee" name="daily_late_fee" class="circOnly" > </select-->
			<span class="reqd circOnly">*</span>    
		</li>
    <li>
      <label for="restock_threshold" class="distOnly"><?php echo T("Restock amount:"); ?></label>
      <input id="restock_threshold" name="restock_threshold" class="distOnly" type="number" size="2" min="1" max="99" required aria-required="true" />
			<span class="reqd distOnly">*</span>    
		</li>
    <li>
			<input type="hidden" id="mode" name="mode" value="">
			<input type="hidden" id="cat" name="cat" value="collect">
			<input type="hidden" id="code" name="code" value="">
		</li>
	</ul>
	<ul id="btnRow">
    <li><input type="submit" id="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" /></li>
    <li><input type="submit" id="updtBtn" class="actnBtns" value="<?php echo T("Update"); ?>" /></li>
    <li><input type="button" id="cnclBtn" value="<?php echo T("Cancel"); ?>" /></li>
    <li><input type="submit" id="deltBtn" class="actnBtns" value="<?php echo T("Delete"); ?>" /></li>
	</ul>
</fieldset>
</form>

<div id="msgDiv" style="display: none;"><fieldSet id="msgArea"></fieldset></div>

<p class="note">
	<?php echo T("Note:");?><br /><?php echo T("collectionsListNoteMsg"); ?>
</p>
<br />
<p class="note circOnly">
	<?php echo T("Note:"); ?><br /><?php echo T("Setting zero days no checkout"); ?>
</p>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/ListJs.php"));
	require_once(REL(__FILE__, "collectionsJs.php"));
?>	
</body>
</html>
