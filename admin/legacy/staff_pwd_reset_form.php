<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "staff";
	$focus_form_name = "pwdresetform";
	$focus_form_field = "pwd";

	include(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));


	#****************************************************************************
	#*  Checking for query string flag to read data from database.
	#****************************************************************************
	if (isset($_GET["UID"])){
		$postVars["userid"] = $_GET["UID"];
	} else {
		require(REL(__FILE__, "../shared/get_form_vars.php"));
	}

?>

<form name="pwdresetform" method="post" action="../admin/staff_pwd_reset.php">
<input type="hidden" name="userid" value="<?php echo $postVars["userid"];?>" />
<table class="primary">
	<tr>
		<th colspan="2" valign="top" nowrap="yes" align="left">
			<?php echo T("Reset Staff Member Password"); ?>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Password:"); ?>
		</td>
		<td valign="top" class="primary">
			<input type="password" name="pwd" size="20" maxlength="20"
			value="<?php if (isset($postVars["pwd"])) echo $postVars["pwd"]; ?>" /><br />
			<span class="error">
			<?php if (isset($pageErrors["pwd"])) echo $pageErrors["pwd"]; ?></span>
		</td>
	</tr>
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Re-enter Password:"); ?>
		</td>
		<td valign="top" class="primary">
			<input type="password" name="pwd2" size="20" maxlength="20"
			value="<?php if (isset($postVars["pwd2"])) echo $postVars["pwd2"]; ?>" /><br />
			<span class="error">
			<?php if (isset($pageErrors["pwd2"])) echo $pageErrors["pwd2"]; ?></span>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="parent.location='../admin/staff_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>

</table>
</form>

<?php

	 ;
