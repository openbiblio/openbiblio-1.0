<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "staff";
	$focus_form_name = "newstaffform";
	$focus_form_field = "last_name";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3><?php echo T("Add New Staff Member");?></h3>

<form name="newstaffform" method="post" action="../admin/staff_new.php">
<fieldset>
<table class="primary">
	<tbody class="striped">
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Last Name:");?>
		</td>
		<td valign="top" class="primary">
			<?php printInputText("last_name",30,30,$postVars,$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("First Name:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php printInputText("first_name",30,30,$postVars,$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Login Username:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php printInputText("username",20,20,$postVars,$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Password:"); ?>
		</td>
		<td valign="top" class="primary">
			<input type="password" name="pwd" size="20" maxlength="20"
			value="<?php if (isset($postVars["pwd"])) echo $postVars["pwd"]; ?>" ><br />
			<?php if (isset($pageErrors["pwd"])) {
				echo '<span class="error">'.$pageErrors["pwd"].'</span>';
				} ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Re-enter Password:"); ?>
		</td>
		<td valign="top" class="primary">
			<input type="password" name="pwd2" size="20" maxlength="20"
			value="<?php if (isset($postVars["pwd2"])) echo $postVars["pwd2"]; ?>" ><br />
			<?php if (isset($pageErrors["pwd2"])) {
				echo '<span class="error">'.$pageErrors["pwd2"].'</span>';
				} ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Authorization:");?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputField('checkbox','circ_flg','CHECKED',NULL,$postVars["circ_flg"]); ?>
		  <label for="circ_flg"><?php echo T("Circ");?></label>

			<?php echo inputField('checkbox','circ_mbr_flg','CHECKED',NULL,$postVars["circ_mbr_flg"]); ?>
		  <label for="circ_mbr_flg"><?php echo T("Update Member");?></label>

			<?php echo inputField('checkbox','catalog_flg','CHECKED',NULL,$postVars["catalog_flg"]); ?>
		  <label for="catalog_flg"><?php echo T("Catalog");?></label>
			<br />

			<?php echo inputField('checkbox','admin_flg','CHECKED',NULL,$postVars["admin_flg"]); ?>
		  <label for="admin_flg"><?php echo T("Admin");?></label>

			<?php echo inputField('checkbox','tools_flg','CHECKED',NULL,$postVars["tools_flg"]); ?>
		  <label for="tools_flg"><?php echo T("Tools");?></label>

			<?php echo inputField('checkbox','reports_flg','CHECKED',NULL,$postVars["reports_flg"]); ?>
		  <label for="reports_flg"><?php echo T("Reports");?></label>
		</td>
	</tr>
	</tbody>
	
	<tfoot>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onClick="parent.location='../admin/staff_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
	
</table>
</fieldset>
</form>

<?php

	 ;
