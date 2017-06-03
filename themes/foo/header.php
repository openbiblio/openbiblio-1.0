<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	include(REL(__FILE__, "header_top.php"));
?>

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
			<form role="form" method="get" action="../shared/logout.php">
			<input type="submit" value="<?php echo T("Logout"); ?>" class="navbutton">
			</form>
<?php
	Nav::display($params['nav']);
?>
		</td>
		<td id="content">
<!-- **************************************************************************************
		 * beginning of main body
		 **************************************************************************************-->
