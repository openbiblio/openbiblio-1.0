<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "materials";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3 id="listHdr"><?php echo T('Media Types'); ?></h3>

<div id="listDiv">
<h5 id="updateMsg"></h5>
<br />
<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList" name="showList"">
	<thead>
  	<tr>
			<th rowspan="2" valign="top"><?php echo T("Function"); ?></th>
			<th rowspan="2" valign="top" nowrap="yes"><?php echo T("Description"); ?></th>
			<th colspan="2" valign="top"><?php echo T("Checkout Limit"); ?></th>
			<th rowspan="2" valign="top"><?php echo T("Image File"); ?></th>
			<th rowspan="2" valign="top"><?php echo T("Default"); ?></th>
			<th rowspan="2" valign="top"><?php echo T("Item Count"); ?></th>
		</tr>
		<tr>
			<th valign="top"><?php echo T("Adult"); ?></th>
			<th><?php echo T("Juvenile"); ?></th>
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
	<legend><?php echo T('Edit Media Properties'); ?></legend>
	<ul id="editTbl">
    <li>
      <label for="description"><?php echo T("Description"); ?>:</label>
      <input id="description" name="description" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>    
		</li>
		<li>
	  <fieldset>
	  	<fieldset class="inlineFldSet">
			<label><?php echo T("Checkout Limit");?>:</label>
				<span class="note"><?php echo T("(enter 0 for unlimited)"); ?></span>
	  	</fieldset>
	  	<fieldset class="inlineFldSet">
	  		<label for="adult_checkout_limit"><?php echo T("Adult");?>:</th><br />
				<input id="adult_checkout_limit" name="adult_checkout_limit" type="mumber" size="2" min="0" max="99" required aria-required="true" />
				<span class="reqd">*</span>    
	  	</fieldset>
	  	<fieldset class="inlineFldSet">
	  		<label for="juvenile_checkout_limit"><?php echo T("Juvenile");?>:</th><br />
				<input id="juvenile_checkout_limit" name="juvenile_checkout_limit" type="mumber" size="2" min="0" max="99" required aria-required="true" />
				<span class="reqd">*</span>    
	  	</fieldset>
	  	<fieldset class="inlineFldSet" id="vertSep"></fieldset>
	  	<fieldset class="inlineFldSet">
      	<label for="default_flg"><?php echo T("DefaultY/N"); ?>:</label>
      	<input id="default_flg" name="default_flg" type="text" size="1" value="N"
					pattern="[Y,N]" required aria-required="true" />
				<span class="reqd">*</span>
			</fieldset>
	  </fieldset>
		</li>
		<li><label for="image_file"><?php echo T("Image File");?>:</label>
			<input id="image_file" name="image_file" type="text" size="40" maxlength="128" required aria-required="true" />
			<span class="reqd">*</span>    
		</li>
    <li>
			<input type="hidden" id="mode" name="mode" value="">
			<input type="hidden" id="code" name="code" value="">
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

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<p class="note">
	<?php echo T("Note:"); ?><br /><?php echo T('materialsListNoteMsg'); ?>
</p>

<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
	
	require_once(REL(__FILE__, "mediaJs.php"));
?>	
