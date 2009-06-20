<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "admin";
  $nav = "biblioFlds";
  $focus_form_name = "workForm";
  $focus_form_field = "name";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h1><span id="pageHdr" class="title"><?php echo T('BiblioFieldsEditor'); ?></span></h1>

<div id='typeChoice'>
	<fieldset id='choiceTyp'>
		<label for="typeList"><?php echo T('MatlTypeListLabel');?></label>
		<select id='typeList'></select>
	</fieldset>
</div

<div id="workDiv">
<form id="workForm" name="workForm" class="form">
<h5 id="updateMsg"></h5>
<table id="showList" name="showList" class="primary striped">
	<thead>
  <tr>
    <th class="colHead">&nbsp</th>
    <th class="colHead"><?php echo T("SeqNo"); ?></th>
    <th class="colHead"><?php echo T("Tag"); ?></th>
    <th class="colHead"><?php echo T("Label"); ?></th>
    <th class="colHead"><?php echo T("Data Type"); ?></th>
    <th class="colHead"><?php echo T("Required"); ?></th>
    <th class="colHead"><?php echo T("Repeats"); ?></th>
  </tr>
	</thead>
	<tbody id="fldSet">
	  <!--to be generated and filled in by Javascript from Server-->
	</tbody>
	<tfoot>
  <tr>
  	<!-- acts as a spacer used to slightly seperate button from form body -->
    <td><input type="hidden" id="mode" name="mode" value=""></td>
  </tr>
	<!--tr>
		<td colspan="1">&nbsp;</td>
	  <td colspan="3" class="primary btnFld">
			<input type="button" id="updtBtn" value="<?php echo T("Update"); ?>" class="button" />
			<input type="button" id="newBtn" value="<?php echo T("AddNew"); ?>" class="button" />
		</td>
		<td colspan="1">
			&nbsp;
		</td>
		<td colspan="2">
			<input type="button" id="deltBtn" value="<?php echo T("Delete"); ?>" class="button" />
		</td>
	</tr-->
	</tfoot>
</table>
</form>
</div>

<div id="editDiv">
<form id="editForm" name="tagForm" class="form">
<!--h1><span id="editHdr" class="title"></span></h1-->
<h5 id="reqdNote" class="reqd"><sup>*</sup><?php echo T("lookup_rqdNote"); ?></h5>
<table id="editTbl" class="primary" border="1"">
	<thead>
  </thead>
  <tbody>
  <tr>
    <td class="primary lblFld"><label for="tag"><?php echo T("tagLabel"); ?></td>
    <td class="primary inptFld">
      <?php echo inputfield('text', 'tag', "", array('size'=>1,'readonly'=>'readonly')); ?>
      <?php echo inputfield('text', 'subfield_cd', "", array('size'=>1,'readonly'=>'readonly')); ?>
		</td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="label" class="reqd"><sup>*</sup><?php echo T("labelLabel"); ?>
    </td>
    </td>
    <td colspan="2" class="primary inptFld">
      <?php echo inputfield('text', 'label', "", array('size'=>32,'length'=>'50')); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="seq" class="reqd"><sup>*</sup><?php echo T("formTypeLabel"); ?></label>
    </td>
    <td colspan="2" valign="top" class="primary inptFld">
    	<select id="form_type" name="form_type" class="fldData">
    		<option value="text">Single Line</option>
    		<option value="textarea">Multi Line</option>
    	</select>
		</td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="required"><?php echo T("requiredLabel"); ?>
    </td>
    <td colspan="2" valign="top" class="primary inptFld">
			<input type="checkbox" id="required" name="required" class="primary fldData" size="4" value="1" />
		</td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="repeatable"><?php echo T("repeatableLabel"); ?>
    </td>
    <td colspan="2" class="primary inptFld">
      <?php echo inputfield('text', 'repeatable', "", array('size'=>2,'length'=>'4')); ?>
    </td>
  </tr>
   <tr>
    <td colspan="3">
			<input type="hidden" id="editMode" name="editMode" value="">
    	<input type="hidden" id="material_field_id" name="material_field_id" value="">
		</td>
  </tr>
  <tfoot>
  <tr>
    <td colspan="1" class="primary" align="left">
			<input type="button" id="editAddBtn" value="<?php echo T("Add New"); ?>" class="button" />
			<input type="button" id="editUpdtBtn" value="<?php echo T("Update"); ?>" class="button" />
			<input type="button" id="editCnclBtn" value="<?php echo T("Cancel"); ?>" class="button" />
    </td>
    <td colspan="2" colspan="1" class="primary" align="right">
			<input type="button" id="editDeltBtn" value="<?php echo T("Delete"); ?>" class="button" />
    </td>
  </tr>
  </tfoot>
  </tbody>

</table>
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php include(REL(__FILE__,"../shared/footer.php")); ?>
