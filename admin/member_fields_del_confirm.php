<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	$tab = "admin";
	$nav = "member_fields";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	#****************************************************************************
	#*  Checking for query string.  Go back to list if none found.
	#****************************************************************************
	if (!isset($_GET["code"])){
		header("Location: ../admin/member_fields_list.php");
		exit();
	}
	$code = $_GET["code"];
	$description = $_GET["desc"];

	#**************************************************************************
	#*  Show confirm page
	#**************************************************************************
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Custom Member Fields"); ?></h3>
<form name="delstaffform" method="post" action="../admin/member_fields_del.php?code=<?php echo HURL($code);?>&amp;desc=<?php echo HURL($description);?>">
<fieldset>
	<input type="hidden" id="dummy" value="">
<?php echo T('memberFieldsDelConfirmMsg', array('desc'=>$description)); ?>
</fieldset>
			<input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
			<input type="button" onclick="self.location='../admin/member_fields_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
</form>

<?php

	Page::footer();
