<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "tools";
  $nav = "biblioFlds";
  $focus_form_name = "workForm";
  $focus_form_field = "name";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h1 id="pageHdr" class="title"><?php echo T('BiblioFieldsEditor'); ?></h1>

<div id="typeChoice">
	<fieldset id="choiceTyp">
		<label for="typeList"><?php echo T('MatlTypeListLabel');?></label>
		<select id="typeList"></select>
		<hr id="topSeperator" width="75%" />

		<input type=button id="saveBtn" name="saveBtn"
					 value="<?php echo T("saveLayout"); ?>" class="button" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type=button id="configBtn" name="configBtn"
					 value="<?php echo T("configLayout"); ?>" class="button" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type=button id="goBackBtn" name="goBackBtn"
					 value="<?php echo T("goBack"); ?>" class="button" />
	</fieldset>
</div>

<div id="configDiv">
	<h3 id="configTitle"><?php echo T('BiblioFieldsConfig'); ?><span id="configName"></span></h3>

	<fieldset id="existingSpace" class="configArea">
	  <legend>Existing (drag to re-arange)</legend>
		<ul id="existing" class="connectedSortable">
			<li><?php echo T("waitForServer"); ?></li>
		</ul>
	</fieldset>
	
	<fieldset id="potentialSpace" class="configArea">
	  <legend>Available (select, then drag to add)</legend>
		<select id="marcBlocks"></select>
		<br />
		<select id="marcTags"></select>
		<br />
		<ul id="potential" class="connectedSortable">
			<!--li class="waitLabel"><?php //echo T("waitForServer"); ?></li-->
		</ul>
	</fieldset>
</div>

<div id="workDiv">
<form id="workForm" name="workForm" class="form">
<h5 id="updateMsg"></h5>
<table id="showList" name="showList" class="primary">
	<thead>
  <tr>
    <th class="colHead">&nbsp</th>
    <th class="colHead"><?php echo T("Seq"); ?></th>
    <th class="colHead"><?php echo T("Tag"); ?></th>
    <th class="colHead"><?php echo T("Label"); ?></th>
    <th class="colHead"><?php echo T("Type"); ?></th>
    <th class="colHead"><?php echo T("Reqd"); ?></th>
    <th class="colHead"><?php echo T("Rpts"); ?></th>
  </tr>
	</thead>
	<tbody id="fldSet" class="striped">
	  <!--to be generated and filled in by Javascript from Server-->
	</tbody>
	<tfoot>
  <tr>
  	<!-- placed here to slightly seperate any buttons from form body -->
    <td><input type="hidden" id="mode" name="mode" value=""></td>
  </tr>
	</tfoot>
</table>
</form>
</div>

<div id="editDiv">
<form id="editForm" name="tagForm" class="form">
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
			<!--input type="checkbox" id="required" name="required" class="primary fldData" size="4" value="1" /-->
      <?php echo inputfield('checkbox', 'required', '1', array('class'=>'primary fldData')); ?>
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
			<input type="button" id="editUpdtBtn" value="<?php echo T("Update"); ?>" class="button" />
			<input type="button" id="editCnclBtn" value="<?php echo T("goBack"); ?>" class="button" />
    </td>
    <td colspan="2" colspan="1" class="primary" align="right">
			<input type="button" id="editDeltBtn" value="<?php echo T("Delete"); ?>" class="button" />
    </td>
  </tr>
  </tfoot>
</table>
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<!-- load jQuery library(s) -->
<script>//console.log('loading ui ');</script>
<script type="text/javascript" src="../shared/jquery/jquery-ui-1.8.7.custom.min.js"></script>

<?php include(REL(__FILE__,"../shared/footer.php")); ?>
