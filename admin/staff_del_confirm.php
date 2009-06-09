<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "staff";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	#****************************************************************************
	#*  Checking for query string.  Go back to staff list if none found.
	#****************************************************************************
	if (!isset($_GET["UID"])){
		header("Location: ../admin/staff_list.php");
		exit();
	}
	$uid = $_GET["UID"];
	$last_name = $_GET["LAST"];
	$first_name = $_GET["FIRST"];

	#**************************************************************************
	#*  Show confirm page
	#**************************************************************************
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delstaffform" method="post" action="../admin/staff_del.php?UID=<?php echo $uid;?>&LAST=<?php echo $last_name;?>&FIRST=<?php echo $first_name;?>">
<?php echo T('staffDelConfirmMsg', array('name'=>$first_name.' '.$last_name)); ?><br /><br />
			<input type="submit" value="<?php echo T("Delete"); ?>" class="button">
			<input type="button" onClick="parent.location='../admin/staff_list.php'" value="<?php echo T("Cancel"); ?>" class="button">
</form>
</center>

<?php

	Page::footer();
