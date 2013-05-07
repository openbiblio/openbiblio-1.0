<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "admin";
  $nav = "states";
  $focus_form_name = "showForm";
  $focus_form_field = "";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3 id="listHdr"><?php echo T('States'); ?></h3>

<div id="listDiv" style="display: none;">
<h5 id="updateMsg"></h5>

<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<legend id="listHdr"> </legend>
<table id="showList" name="showList">
	<thead>
  <tr>
    <th colspan="1">&nbsp;</th>
    <th><?php echo T("Code"); ?></th>
    <th><?php echo T("Description"); ?></th>
    <th><?php echo T("Default"); ?></th>
  </tr>
	</thead>
	<tbody class="striped">
	  <tr><td colspan="4"><?php echo T("No states have been defined."); ?> </td></tr>
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
<legend id="fieldsHdr"> </legend>
<table id="editTbl">
  <tbody>
  <tr>
    <td>
      <label for="code"><?php echo T("Code"); ?>:</label>
    </td>
    <td>
      <input id="code" name="code" type="text" size="20" maxlength="20" required />
			<span class="addOnly reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td>
      <label for="description"><?php echo T("Description"); ?>:</label>
    </td>
    <td>
      <input id="description" name="description" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>    
		</td>
  </tr>
  <tr>
    <td>
      <label for="default_flg"><?php echo T("Default (Y/N)"); ?>:</label>
    </td>
    <td>
      <input id="default_flg" name="default_flg" type="text" size="1" value="N"
				pattern="[Y,N]" required aria-required="true" />
			<span class="reqd">*</span>
    </td>
  </tr>
  <tr>
    <td><input type="hidden" id="cat" name="cat" value="states"></td>
    <td><input type="hidden" id="mode" name="mode" value=""></td>
  </tr>
  <tfoot>
  <tr>
    <td colspan="1" align="left">
			<input type="submit" id="addBtn" value="Add" />
			<input type="submit" id="updtBtn" value="Update" />
			<input type="button" id="cnclBtn" value="Cancel" />
    </td>
    <td colspan="1" align="right">
			<input type="submit" id="deltBtn" value="Delete" />
    </td>
  </tr>
  </tfoot>
  </tbody>

</table>
</fieldset>
</form>
</div>

<div id="msgDiv" style="display: none;"><fieldSet id="msgArea"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/ListJs.php"));
	require_once(REL(__FILE__, "statesJs.php"));
?>	

</body>
</html>
