<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	session_cache_limiter(null);

	$tab = "admin";
	$nav = "member_fields";
	$focus_form_name = "editfieldform";
	$focus_form_field = "description";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	if (isset($_GET["code"])){
		unset($_SESSION["postVars"]);
		unset($_SESSION["pageErrors"]);

		include_once(REL(__FILE__, "../model/MemberCustomFields.php"));
		$fields = new MemberCustomFields;
		$postVars = $fields->getOne($_GET["code"]);
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}
?>

<form name="editfieldform" method="post" action="../admin/member_fields_edit.php">
<input type="hidden" name="code" value="<?php echo H($postVars["code"]);?>" />
<table class="primary">
	<tr>
		<th colspan="2" nowrap="yes" align="left">
			<?php echo T("Edit Member Field"); ?>
		</th>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Code:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php echo H($postVars['code']); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Description:"); ?>
		</td>
		<td valign="top" class="primary">
			<?php printInputText("description",40,40,$postVars,$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onClick="self.location='../admin/member_fields_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>

</table>
</form>

<?php

	Page::footer();
