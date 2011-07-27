<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "integrity";

	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Check Database Integrity"); ?></h3>

<form method="post" action="../admin/integrity_check.php">
<fieldset>
	<p><?php echo T('integrityMsg');?></p>
	<input type="submit" value="<?php echo T("Check Now"); ?>" />
</fieldset>
</form>

<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
?>	
