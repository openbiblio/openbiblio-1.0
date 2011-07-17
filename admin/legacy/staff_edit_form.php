<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	session_cache_limiter(null);

	$tab = "admin";
	$nav = "staff";
	$focus_form_name = "editstaffform";
	$focus_form_field = "last_name";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	if (isset($_GET["UID"])){
		$postVars["userid"] = $_GET["UID"];
		include_once(REL(__FILE__, "../model/Staff.php"));
		$staff = new Staff;
		$postVars = $staff->getOne($postVars["userid"]);
		foreach (array('circ', 'circ_mbr', 'admin', 'tools', 'reports', 'suspended', 'catalog') as $flg) {
			$flag = $flg.'_flg';
			if ($postVars[$flag] == 'Y') {
				$postVars[$flag] = "CHECKED";
			} else {
				$postVars[$flag] = "";
			}
		}
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}
?>
<h3><?php echo T("Edit Staff Member Information"); ?></h3>

<form name="editstaffform" method="post" action="../admin/staff_edit.php">
<fieldset>
<input type="hidden" name="userid" value="<?php echo $postVars["userid"];?>">
<table class="primary">
	<tr>
		<th align="left" colspan="2" nowrap="yes">
			
		</th>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Last Name:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php $attrs = array('size'=>30, 'maxLength'=>30); ?>
			<?php echo inputField('text','last_name',$postVars['last_name'],NULL,$attrs); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("First Name:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputField('text','first_name',$postVars['first_name'],NULL,$attrs); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Login Username:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputField('text','username',$postVars['username'],NULL,$attrs); ?>
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
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Suspended:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo inputField('checkbox','suspended_flg','CHECKED',NULL,$postVars["suspended_flg"]); ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onClick="parent.location='../admin/staff_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<?php

	 ;
