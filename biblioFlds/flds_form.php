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
    <!--th class="colHead">&nbsp</th-->
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
	<tr>
		<!--td colspan="1">
			&nbsp;
		</td-->
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
	</tr>
	</tfoot>
</table>
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php include(REL(__FILE__,"../shared/footer.php")); ?>
