<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "media";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3 id="listHdr"><?php echo T("List of Media Types"); ?></h3>

<div id="listDiv" style="display: none;">
<h5 id="updateMsg"></h5>
<form id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList" >
	<thead>
  	<tr>
			<th rowspan="2">&nbsp;</th>
			<th rowspan="2" nowrap="yes"><?php echo T("Code"); ?></th>
			<th rowspan="2" nowrap="yes"><?php echo T("Description"); ?></th>
			<th colspan="2"><?php echo T("Checkout Limit"); ?></th>
			<th rowspan="2"><?php echo T("Image File"); ?></th>
			<th rowspan="2"><?php echo T("Item Count"); ?></th>
			<th rowspan="2"><?php echo T("DisplayLines"); ?></th>
			<th rowspan="2"><?php echo T("Default"); ?></th>
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
	
<div id="editDiv" style="display: none;">
<form id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
	<legend id="editHdr"></legend>
	<ul id="editTbl">
    <li>
      <label for="description"><?php echo T("Description"); ?>:</label>
      <input id="description" name="description" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>    
		</li>

		<li>
		  <fieldset>
				<legend><?php echo T("Checkout Limit");?>:</legend>
		  	<fieldset class="inlineFldSet">
					<span class="note"><?php echo T("(enter 0 for unlimited)"); ?></span>
		  	</fieldset>
		  	<fieldset class="inlineFldSet">
		  		<label for="adult_checkout_limit"><?php echo T("Adult");?>:</label><br />
					<input id="adult_checkout_limit" name="adult_checkout_limit" type="mumber" size="2"
						pattern="[0-9]{1,2}" title="0-99" required aria-required="true" />
					<span class="reqd">*</span>
		  	</fieldset>
		  	<fieldset class="inlineFldSet">
		  		<label for="juvenile_checkout_limit"><?php echo T("Juvenile");?>:</label><br />
					<input id="juvenile_checkout_limit" name="juvenile_checkout_limit" type="mumber" size="2"
						pattern="[0-9]{1,2}" title="0-99" required aria-required="true" />
					<span class="reqd">*</span>  
		  	</fieldset>
		  	<fieldset class="inlineFldSet" id="vertSep"></fieldset>
		  	<fieldset class="inlineFldSet">
          <label><?php echo T("Default"); ?>:</label>
          <label for="default_Y">Y:<label>
          <input id="default_Y" name="default_flg" type="radio" value="Y" required aria-required="true" />
          <label for="default_N">N:</label>
          <input id="default_N" name="default_flg" type="radio" value="N" checked required aria-required="true" />
    			<span class="reqd">*</span>
				</fieldset>
		  </fieldset>
		</li>

		<li>
			<fieldset>
				<legend><?php echo T("Search Display Lines");?></legend>
				<label for="srch_disp_lines"><?php echo T("NumberOfLines");?>:</label>
				<input id="srch_disp_lines" name="srch_disp_lines" type="number" size="2"
					pattern="[1-9]{1,2}" min="1" max="19" title="0-19" required aria-required="true" />
				<span class="reqd">*</span>
		  </fieldset>
		</li>

		<li>
			<fieldset>
				<legend><?php echo T("Image File");?></legend>
				<label for="image_file"><?php echo T("CrntImageFile");?>:</label>
				<input id="image_file" name="crntImage_file" type="text" size="32" maxlength="128" readonly />
					<span class="reqd">*</span>
					<br />
				<label for="newImageFile"><?php echo T("NewImageFile");?>:</label>
				<input id="newImageFile" name="image_file" type="file" size="32" maxlength="128" />
			</fieldset>  
		</li>

    <li>
			<input type="hidden" id="mode" name="mode" value="">
			<input type="hidden" id="cat" name="cat" value="media">
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
</div>

<div id="msgDiv" style="display: none;">
	<fieldSet id="msgArea"></fieldset>
</div>

<p class="note">
	<?php echo T("Note"); ?>:<br /><?php echo T("mediaListNoteMsg"); ?>
</p>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/ListJs.php"));
	require_once(REL(__FILE__, "mediaJs.php"));
?>	
</body>
</html>
