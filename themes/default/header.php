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

if ($tab != 'help') {
?>

<aside id="sidebar">
	<header class="notForInstall">
		<h3 class="theHead">
			<?php 
			if (!isset($doing_install) or !$doing_install) {
				if (Settings::get('library_image_url') != "") {
					echo '<img id="logo"'.' src="'.Settings::get("library_image_url").'" />';
				}
				// Libname is defined in header_top.php	
				echo "<span id=\"library_name\" > $libName </span>";
				//if ($tab != "opac") 
				//	echo "<br />" . T("Staff Interface");
				if (Settings::get('show_lib_info') == 'Y') {
					echo "<hr class=\"hdrSpacer\"> \n";
					echo "<div id=\"library_hours\">". Settings::get('library_hours') . "</div> \n";
					echo "<hr class=\"hdrSpacer\"> \n";
					echo "<div id=\"library_phone\">". Settings::get('library_phone') ."</div> \n";
				}
			}
			?>
		</h3>
		
		<?php if ($tab != 'opac') { ?>
		<form class="notForInstall" method="get" action="../shared/logout.php">
			<input type="submit" value="<?php echo T("Logout") ?>" />
		</form>
		<?php } ?>
	</header>
	<hr class="notForInstall hdrSpacer" />
	
	<nav class="notForInstall">
		<?php Nav::display($params['nav']); ?>
	</nav>
	
	<hr class="notForInstall hdrSpacer" />
	
	<footer>
	  <div id="obLogo">
			<a href="http://obiblio.sourceforge.net/">
				<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" alt="Powered by OpenBiblio" />
			</a>
			<br />
		</div>
		
		OpenBiblio Version: <?php echo H(OBIB_CODE_VERSION);?><br />
		For <a href="../COPYRIGHT.html">Legal Info</a>.
	</footer>
</aside>
<?php } ?>

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

