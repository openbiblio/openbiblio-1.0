<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
	require_once(REL(__FILE__, "../../model/Members.php"));
	require_once(REL(__FILE__, "../../model/OpenHours.php"));
	require_once(REL(__FILE__, "header_top.php"));
	$open_hours = new OpenHours;
?>

<aside id="sidebar">
	<header>
		<h3 class="theHead">
				<?php
				if (Settings::get('library_image_url') != "") {
					echo '<img id="logo"'.' src="'.Settings::get("library_image_url").'" border="0" /><br />';
				}
				?>
				<!-- Libname is defined in header_top.php -->	
				<?php 
					//echo "$libName:<br />".T("OPAC Interface"); 
					echo "$libName:<br />"; 
				?>
		</h3>
		<div id="library_hours"><?php //echo Settings::get('library_hours'); ?><?php echo $open_hours->displayOpenHours(); ?></div>
		<hr style="width:25%">
		<div id="library_phone"><?php echo Settings::get('library_phone') ?></div>
	</header>
	<hr />
	
	<nav>
		<?php Nav::display($nav); ?>
	</nav>
	<hr />
	
	<footer id="footer">
		<a id="obLogo" href="http://obiblio.sourceforge.net/">
			<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" />
		</a>
		<br />
		Powered by OpenBiblio version <?php echo H(OBIB_CODE_VERSION);?><br />
		OpenBiblio is free software, copyright by its authors.<br />
		Get <a href="../COPYRIGHT.html">more information</a>.
	</footer>
</aside>

<!-- **************************************************************************************
		 * beginning of main body
		 **************************************************************************************-->
<div id="content">
	<?php
		if (isset($_GET['msg'])) {
			echo "<div class=\"msg\">".H($_GET['msg'])."</div>";
		}
