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
		foreach (array('circ', 'circ_mbr', 'admin', 'reports', 'suspended') as $flg) {
			if ($postvars[$flg.'_flg'] == 'Y') {
				$postVars[$flg.'_flg'] = "CHECKED";
			} else {
				$postVars[$flg.'_flg'] = "";
			}
		}
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}

?>

<form name="editstaffform" method="post" action="../admin/staff_edit.php">
<input type="hidden" name="userid" value="<?php echo $postVars["userid"];?>">
<table class="primary">
	<tr>
		<th align="left" colspan="2" nowrap="yes">
			<?php echo T("Edit Staff Member Information"); ?>
		</th>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Last Name:"); ?>
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
			<?php echo T("Authorization:");?>
		</td>
		<td valign="top" class="primary">
			<input type="checkbox" name="circ_flg" value="CHECKED"
				<?php if (isset($postVars["circ_flg"])) echo $postVars["circ_flg"]; ?> />
			<?php echo T("Circ");?>
			<input type="checkbox" name="circ_mbr_flg" value="CHECKED"
				<?php if (isset($postVars["circ_mbr_flg"])) echo $postVars["circ_mbr_flg"]; ?> />
			<?php echo T("Update Member"); ?>
			<input type="checkbox" name="catalog_flg" value="CHECKED"
				<?php if (isset($postVars["catalog_flg"])) echo $postVars["catalog_flg"]; ?> />
			<?php echo T("Catalog");?>
			<input type="checkbox" name="admin_flg" value="CHECKED"
				<?php if (isset($postVars["admin_flg"])) echo $postVars["admin_flg"]; ?> />
			<?php echo T("Admin");?>
			<input type="checkbox" name="reports_flg" value="CHECKED"
				<?php if (isset($postVars["reports_flg"])) echo $postVars["reports_flg"]; ?> />
			<?php echo T("Reports"); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Suspended:"); ?>
		</td>
		<td valign="top" class="primary">
			<input type="checkbox" name="suspended_flg" value="CHECKED"
				<?php if (isset($postVars["suspended_flg"])) echo $postVars["suspended_flg"]; ?> />
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onClick="parent.location='../admin/staff_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
</table>
</form>

<?php

	Page::footer();
