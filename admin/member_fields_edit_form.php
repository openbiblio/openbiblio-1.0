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
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>T("Custom Member Fields")));

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

<h3><?php echo T("Custom Member Fields"); ?></h3>

<form name="editfieldform" method="post" action="../admin/member_fields_edit.php">
<fieldset>
<legend><?php echo T("Edit Member Field"); ?></legend>
<input type="hidden" name="code" value="<?php echo H($postVars["code"]);?>" />
<table class="primary">
	<tbody class="unstriped">
	<tr>
		<th nowrap="true" class="primary">
			<?php echo T("Code:"); ?>
		</th>
		<td valign="top" class="primary">
			<?php echo H($postVars['code']); ?>
		</td>
	</tr>
	<tr>
		<th nowrap="true" class="primary">
			<?php echo T("Description:"); ?>
		</th>
		<td valign="top" class="primary">
			<?php echo inputField('text','description',$postVars[description],array('size'=>'40','maxlength'=>'40'));	?>
		</td>
	</tr>
	</tbody>
	
	<tfoot>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onClick="self.location='../admin/member_fields_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>

</table>
</fieldset>
</form>

<?php

	 ;
