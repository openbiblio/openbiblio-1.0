<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "sites/del";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));

	if (!isset($_REQUEST["siteid"])){
		header("Location: ../admin/sites_list.php");
		exit();
	} else {
		$siteid = $_REQUEST["siteid"];
	}

	$sites = new Sites;
	$site = $sites->getOne($siteid);

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<center>
<form name="delsiteform" method="post" action="../admin/sites_del.php">
<input type="hidden" name="siteid" value="<?php echo H($site['siteid']) ?>" />
<input type="hidden" name="name" value="<?php echo H($site['name']) ?>" />
<?php echo T('sitesDelConfirmMsg', array('name'=>H($site['name']))); ?><br /><br />
			<input type="submit" value="<?php echo T("Delete"); ?>" class="button" />
			<a class="small_button" href="../admin/sites_list.php"><?php echo T("Cancel"); ?></a>
</form>
</center>

<?php

	Page::footer();
