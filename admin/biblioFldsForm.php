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
<h1 id="pageHdr" class="title"><?php echo T('BiblioFieldsEditor'); ?></h1>

<div id="typeChoice">
	<fieldset id="choiceTyp">
		<label for="typeList"><?php echo T('MediaTypeListLabel');?></label>
		<select id="typeList"></select>
		<hr id="topSeperator" width="75%" />

		<input type=button id="saveBtn" name="saveBtn"
					 value="<?php echo T("LayoutSave"); ?>" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type=button id="configBtn" name="configBtn"
					 value="<?php echo T("LayoutConfig"); ?>" />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type=button id="goBackBtn" name="goBackBtn"
					 value="<?php echo T("Go Back"); ?>" />
	</fieldset>
</div>

<div id="configDiv">
	<h3 id="configTitle"><?php echo T('BiblioFieldsConfig'); ?><span id="configName"></span></h3>

	<fieldset id="existingSpace">
	  <legend>Existing (drag to re-arange)</legend>
		<ul id="existing" class="connectedSortable">
			<li><?php echo T("waitForServer"); ?></li>
		</ul>
	</fieldset>
	
	<fieldset id="potentialSpace">
	  <legend>Available (select, then drag to add)</legend>
		<select id="marcBlocks"></select>
		<br />
		<select id="marcTags"></select>
		<br />
		<ul id="potential" class="connectedSortable">
		</ul>
	</fieldset>
</div>

<div id="workDiv">
<form id="workForm" name="workForm">
<h5 id="updateMsg"></h5>
<table id="showList" name="showList">
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
<form id="editForm" name="tagForm">
<h5 id="reqdNote" class="reqd"><span class="reqd">*</span><?php echo T("lookup_rqdNote"); ?></h5>
<fieldset>
<table id="editTbl" border="1"">
	<thead>
  </thead>
  <tbody>
  <tr>
    <td><label for="tag"><?php echo T("Tag"); ?></td>
    <td>
      <input id="tag" name="tag" type="number" size="3" min="1" max="999" readonly />
      <input id="subfield_cd" name="subfield_cd" type="text" size="1" readonly />
		</td>
  </tr>
  <tr>
    <td><label for="label"><?php echo T("Label"); ?></td>
    </td>
    <td colspan="2">
      <input id="label" name="label" type="text" size="32" required aria-required="true" />
      <span class="reqd">*</span>
    </td>
  </tr>
  <tr>
    <td><label for="form_type"><?php echo T("Form Type Label"); ?></label></td>
    <td colspan="2">
    	<select id="form_type" name="form_type">
    		<option value="text">Single Line</option>
    		<option value="textarea">Multi Line</option>
    	</select>
      <span class="reqd">*</span>
		</td>
  </tr>
  <tr>
    <td><label for="required"><?php echo T("Required"); ?></td>
    <td colspan="2">
			<input id="required" name="required" type="checkbox" value="1" />
		</td>
  </tr>
  <tr>
    <td><label for="repeatable"><?php echo T("Repeatable"); ?></td>
    <td colspan="2">
      <input id="repeatable" name="repeatable" type="number" size="2" min="0" max="99" value="0" required aria-required="true" />
    </td>
  </tr>
   <tr>
    <td colspan="3">
			<input id="editMode" name="editMode" type="hidden" value="">
    	<input id="material_field_id" name="material_field_id" type="hidden" value="">
		</td>
  </tr>
  <tfoot>
  <tr>
    <td colspan="1">
			<input type="button" id="editUpdtBtn" value="<?php echo T("Update"); ?>" />
			<input type="button" id="editCnclBtn" value="<?php echo T("Go Back"); ?>" />
    </td>
    <td colspan="2" colspan="1" class="primary" align="right">
			<input type="button" id="editDeltBtn" value="<?php echo T("Delete"); ?>" />
    </td>
  </tr>
  </tfoot>
</table>
</fieldset>
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<!-- load javaScript -->
<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
	
	require_once(REL(__FILE__, "biblioFldsJs.php"));
?>	
	<script type="text/javascript" src="../shared/jquery/jquery-ui-1.8.7.custom.min.js"></script>
