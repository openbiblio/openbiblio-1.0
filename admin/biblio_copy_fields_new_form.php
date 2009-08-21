<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	session_cache_limiter(null);

	$tab = "admin";
	$nav = "biblio copy_fields";
	$focus_form_name = "newfieldform";
	$focus_form_field = "code";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3><?php echo T("Add a custom biblio copy field"); ?></h3>
<br />
<form name="newfieldform" method="post" action="../admin/biblio_copy_fields_new.php">
<fieldset>
<table class="primary">
	<thead>
	<tr>
		<th nowrap="true" class="primary"><?php echo T("Code:"); ?></th>
		<th nowrap="true" class="primary"><?php echo T("Description:"); ?></th>
	</tr>
	</thead>
	
	<tr>
		<td nowrap="true" class="primary">
			<?php printInputText("code",10,10,$postVars,$pageErrors); ?>
		</td>
		<td valign="top" class="primary">
			<?php printInputText("description",40,40,$postVars,$pageErrors); ?>
		</td>
	</tr>
	
	<tfoot>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="self.location='../admin/biblio copy_fields_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
</table>
</fieldset>
</form>

<?php

	Page::footer();
