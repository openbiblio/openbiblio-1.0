<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
	$navLoc = new Localize(Settings::get('locale'),"navbars");

	include($ThemeDir."/header_top.php");
?>

<h1 class="staff_head">Instructional Media Center: Staff Interface</h1>

<!-- **************************************************************************************
		 * Left nav
		 **************************************************************************************-->
<?php
// cellspacing="0" cellpadding="0" works around IE's lack of
// support for CSS2's border-spacing property.
?>
<table id="staff_main" height="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td id="sidebar">
			<form method="get" action="../shared/logout.php">
			<input type="submit" value="<?php echo $navLoc->getText("logout");?>" class="navbutton">
			</form>
<?php
Nav::display($params['nav']);
?>
		</td>
		<td id="content">
<!-- **************************************************************************************
		 * beginning of main body
		 **************************************************************************************-->
