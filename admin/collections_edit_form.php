<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "collections";
	$focus_form_name = "editcollectionform";
	$focus_form_field = "description";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	#****************************************************************************
	#*  Checking for query string flag to read data from database.
	#****************************************************************************
	if (isset($_GET["code"])){
		$code = $_GET["code"];
		$postVars["code"] = $code;
		include_once(REL(__FILE__, "../model/Collections.php"));
		$collections = new Collections;
		$coll = $collections->getOne($code);
		$t = $collections->getTypeData($coll);
		$postVars = array_merge($postVars, $t);
		$_SESSION['postVars'] = $postVars;
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}
?>

<script>
cef = {
	init: function () {
	  $("#type").bind('change',null,cef.switchType);
	  cef.switchType();
	},
	switchType: function () {
		var type = $('#type').val();
		$('.switchable').hide();
		$('tr.colltype_'+type).show();
	}
};
$(document).ready(cef.init);

</script>

<h3><?php echo T("Collections"); ?></h3>

<form name="editcollectionform" method="post" action="../admin/collections_edit.php">
<p class="note">
<?php echo T("Fields marked are required"); ?>
</p>
<fieldset>
<legend><?php echo T("Edit Collection:"); ?></legend>
	<?php echo inputfield('hidden','code',$postVars["code"]); ?>

<table class="primary">
	<tbody>
	<tr>
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Description:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text','description',$coll[description]); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			Type:
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('select', 'type','Circulated','',$collections->getTypeSelect()); ?>
		</td>
	</tr>
	<tr class="switchable colltype_Circulated">
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Days Due Back:");?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text', 'days_due_back',$t[days_due_back]); ?>
		</td>
	</tr>
	<tr class="switchable colltype_Circulated">
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Daily Late Fee:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text', 'daily_late_fee',$t[daily_late_fee]); ?>
		</td>
	</tr>
	<tr class="switchable colltype_Distributed">
		<td nowrap="true" class="primary">
			<sup>*</sup><?php echo T("Restock amount:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputfield('text', 'restock_threshold'); ?>
		</td>
	</tr>
	<tbody id="switchable">
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='../admin/collections_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>

</table>
</fieldset>
</form>
<p class="note">
<sup>*</sup><?php echo T("Note:"); ?><br />
<?php echo T("Setting zero days no checkout"); ?></p>

<?php

	 ;
