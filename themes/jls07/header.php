<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	include(REL(__FILE__,"header_top.php"));
?>

<!-- **************************************************************************************
		 * Left nav
		 **************************************************************************************-->
<?php
// cellspacing="0" cellpadding="0" works around IE's lack of
// support for CSS2's border-spacing property.
?>
<aside id="sidebar">
	<header>
		<h3 class="staff_head">
			<?php 
		if (Settings::get('library_image_url') != "") {
			echo '<img id="logo"'.' src="'.H(Settings::get("library_image_url")).'" border="0" /><br />';
		}
				// Libname is defined in header_top.php	
				echo $libName . ":<br />" . T("Staff Interface");
				if (Settings::get('show_lib_info') == 'Y') {
					echo "<hr style=\"width:25%\" \n";
					echo "<div id=\"library_hours\">". T(Settings::get('library_hours')) . "</div> \n";
					echo "<hr style=\"width:25%\"> \n";
					echo "<div id=\"library_phone\">". H(Settings::get('library_phone')) ."</div> \n";
				}
			?>
		</h3>
		<br />
		<form method="get" action="../shared/logout.php">
			<input type="submit" value="<?php echo T("Logout") ?>" />
		</form>
	</header>
	<hr />
	
	<nav>
		<?php Nav::display($params['nav']); ?>
	</nav>
	<hr />
	
	<footer>
		<a href="http://obiblio.sourceforge.net/">
			<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" alt="Powered by OpenBiblio" />
		</a>
		<br />
		Version: <?php echo H(OBIB_CODE_VERSION);?><br />
		OpenBiblio is free software, copyright by its authors.<br />
		Get <a href="../COPYRIGHT.html">more information</a>.
	</footer>
</aside>

<!-- **************************************************************************************
		 * beginning of main body
		 **************************************************************************************-->
<div id="content">
<?php
if (isset($params['title']) && $params['title'] != '') {
	# $params['title'] should be coming from the translation system, allow HTML
	echo '<h3>'.$params['title'].'</h3>';
}
if (isset($_REQUEST['msg'])) {
	echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
}

