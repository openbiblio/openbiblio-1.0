<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "staff";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Staff Members"); ?></h3>

<div id="listDiv" style="display: none;">

<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList" name="showList"">
	<thead>
	<tr>
		<th colspan="1" rowspan="2" valign="top"><?php echo T("Function"); ?></th>
		<th rowspan="2" valign="top" nowrap="yes"><?php echo T("LastName"); ?></th>
		<th rowspan="2" valign="top" nowrap="yes"><?php echo T("FirstName"); ?></th>
		<th rowspan="2" valign="top"><?php echo T("Userid"); ?></th>
		<th colspan="6"><?php echo T("Authorization"); ?></th>
		<th rowspan="2" valign="top"><?php echo T("Suspended"); ?></th>
		<th rowspan="2" valign="top"><?php echo T("Start Page"); ?></th>
	</tr>
	<tr>
		<th><?php echo T("Circ"); ?></th>
		<th><?php echo T("Member"); ?></th>
		<th><?php echo T("Catalog"); ?></th>
		<th><?php echo T("Reports"); ?></th>
		<th><?php echo T("Admin"); ?></th>
		<th><?php echo T("Tools"); ?></th>
	</tr>
	</thead>
	
	<tbody class="striped">
	  <tr><td colspan="4"><?php echo T("No staff has been defined."); ?> </td></tr>
	</tbody>
	<tfoot>
  	<tr>
  		<!-- spacer used to slightly seperate button from form body -->
    	<td><input type="hidden" id="xxx" name="xxx" value=""></td>
  	</tr>
	</tfoot>
</table>
</fieldset>
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
</form>
</div>

<div id="editDiv" style="display: none;">
<form id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
	<legend id="fieldsHdr"></legend>
	<ul id="editTbl">
        <li>
          <label for="last_name"><?php echo T("LastName"); ?>:</label>
          <input id="last_name" name="last_name" type="text" size="32" required aria-required="true" />
    			<span class="reqd">*</span>
    	</li>
        <li>
          <label for="first_name"><?php echo T("FirstName"); ?>:</label>
          <input id="first_name" name="first_name" type="text" size="32" />
    	</li>
        <li>
          <label for="username"><?php echo T("Username"); ?>:</label>
          <input id="username" name="username" type="text" size="32" required aria-required="true" />
    			<span class="reqd">*</span>
    	</li>
    	<li id="pwdFldSet">
    		<fieldset>
    			<ul>
      			    <li>
      			        <label for="xpwd1"><?php echo T("Password"); ?>:</label>
      			        <input type="password" id="xpwd1" name="pwd" size="20" required aria-required="true" />
    						<span class="reqd">*</span>
    				</li>
      			    <li>
      			        <label for="pwd2"><?php echo T("Re-enter"); ?>:</label>
      			        <input type="password" id="xpwd2" name="pwd2" size="20" required aria-required="true" />
    						<span class="reqd">*</span>
    				</li>
    			</ul>
    		</fieldset>
    	</li>
      	<li>
	        <label for="suspended_flg"><?php echo T("Suspended"); ?>:</label>
	    	<input id="suspended_flg" name="suspended_flg" type="checkbox" value="Y" />
    	</li>
      	<li>
			<label for="start_page"><?php echo T("Start Page"); ?>:</label>
			<select id="start_page" name="start_page" >
				<option value="admin">Admin</option>
				<option value="cataloging">Cataloging</option>
				<option value="circulation">Circulation</option>
				<option value="reports">Reports</option>
				<option value="research">Research</option>
				<option value="tools">Tools</option>
			</select>
    	</li>
    	<br />

    	<li>
    		<fieldset>
    			<legend><?php echo T("Authorization");?>:</legend>
    			<input id="circ_flg" name="circ_flg" type="checkbox" class="roles" value="Y" />
    			<label for="circ_flg"><?php echo T("Circ");?></label>

    			<input id="circ_mbr_flg" name="circ_mbr_flg" type="checkbox" class="roles" value="Y" />
    			<label for="circ_mbr_flg"><?php echo T("Update Member");?></label>

    			<input id="catalog_flg" name="catalog_flg" type="checkbox" class="roles" value="Y" />
    	  		<label for="catalog_flg"><?php echo T("Catalog");?></label>

    			<input id="admin_flg" name="admin_flg" type="checkbox" class="roles" value="Y" />
    	  		<label for="admin_flg"><?php echo T("Admin");?></label>

    			<input id="tools_flg" name="tools_flg" type="checkbox" class="roles" value="Y" />
    	  		<label for="tools_flg"><?php echo T("Tools");?></label>

    			<input id="reports_flg" name="reports_flg" type="checkbox" class="roles" value="Y" />
    	  		<label for="reports_flg"><?php echo T("Reports");?></label>
    		</fieldset>
    	</li>
        <li>
    		<input type="hidden" id="cat" name="cat" value="staff">
    		<input type="hidden" id="mode" name="mode" value="">
    		<input type="hidden" id="userid" name="userid" value="">
    	</li>
	</ul>
	<ul class="btnRow">
        <li><input type="submit" id="addBtn" class="actnBtns" value="<?php echo T("Add");?>" /></li>
        <li><input type="submit" id="updtBtn" class="actnBtns" value="<?php echo T("Update");?>" /></li>
        <li><input type="button" id="cnclBtn" value="<?php echo T("Cancel");?>" /></li>
        <li><input type="submit" id="deltBtn" class="actnBtns" value="<?php echo T("Delete");?>" /></li>
	</ul>
</fieldset>
</form>
</div>

<div id="pwdDiv" style="display: none;">
<form id="pwdChgForm" name="pwdChgForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
	<legend><?php echo T("Reset Password"); ?> for <span></span></legend>
	<ul id="editTbl">
        <li>
            <label for="pwdA"><?php echo T("Password"); ?>:</label>
            <input type="password" id="pwdA" name="pwd" size="20" required aria-required="true" />
    			<span class="reqd">*</span>
    	</li>
        <li>
            <label for="pwdB"><?php echo T("Password"); ?>:</label>
            <input type="password" id="pwdB" name="pwd2" size="20" required aria-required="true" />
    			<span class="reqd">*</span>
    	</li>
	</ul>
	<ul id="btnRow">
        <li><input type="submit" id="pwdChgBtn" value="<?php echo T("Set"); ?>" /></li>
        <li><input type="button" id="pwdCnclBtn" value="<?php echo T("Cancel"); ?>" /></li>
	</ul>
</fieldset>
</form>
</div>

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));

	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/staffJs6.php"));
?>
</body>
</html>
