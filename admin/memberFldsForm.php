<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	$tab = "admin";
	$nav = "memberFields";

	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"></h3>

<div id="listDiv" style="display: none;">
<h5 id="updateMsg"></h5>
<form role="form" id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList" name="showList"">
	<thead>
	<tr>
		<th><?php echo T("Function"); ?></th>
		<th><?php echo T("Code"); ?></th>
		<th><?php echo T("Description"); ?></th>
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

<div id="editDiv" style="display: none;">
<form role="form" id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
	<legend id="fieldsHdr"> </legend>
	<ul id="editTbl">
    <li>
      <label for="code"><?php echo T("Code"); ?>:</label>
      <input id="code" name="code" class="addOnly" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>
		</li>
    <li>
      <label for="description"><?php echo T("Description"); ?>:</label>
      <input id="description" name="description" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>    
		</li>
    <li>
			<input type="hidden" id="mode" name="mode" value="">
			<input type="hidden" id="cat" name="cat" value="mbrFlds">
			<input type="hidden" id="default_flg" name="default_flg" value="Y">
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
</div>

<div id="msgDiv" style="display: none;"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/memberFldsJs6.php"));
?>	
</body>
</html>
