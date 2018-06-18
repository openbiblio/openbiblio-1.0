<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
ini_set('display_errors', 1);

	include(REL(__FILE__,"../../model/OpenHours.php"));
	include(REL(__FILE__,"../../shared/header_top.php"));
	$open_hours = new OpenHours;

	## ---------------------------------------------------------------------
	## --- added plugin support -- Fred -----------------------
	if (file_exists('custom_head.php')) {
		include ('custom_head.php');
	}
	## ---------------------------------------------------------------------


?>
	</head>
	<body>

    <!--[if lt IE 10]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

<?php
	/**************************************************************************************
	 * Left navigation area
	 **************************************************************************************/

if ($tab != 'help') {
?>
<?php if ($tab == 'opac') { ?>
	<!-- defines a SVG sprite for later use in menu -->
	<svg style="display:none">
		<symbol id="navicon" viewbox="0 0 20 20">
			<path d="m0-0v4h20v-4h-20zm0 8v4h20v-4h-20zm0 8v4h20v-4h-20z" fill="currentColor" />
		</symbol>
	</svg>
<?php } ?>


<aside id="sidebar">
    <div id="skiptocontent"><a href="#content" class="warning"><?php echo T("Skip to main content") ?></a></div>

	<header class="notForInstall">

		<h3 class="theHead">
		<?php if (($tab != 'opac') && ((!isset($doing_install) or !$doing_install))) {
				if (Settings::get('library_image_url') != "") {
					echo '<img id="logo"'.' src="'.Settings::get("library_image_url").'" />';
				}
			}
		?>

		<?php if ($tab == 'opac') { ?>
			<!-- this button allows user to expand menu. Intended for phone & tablet users -->
			<span>
				<button id="menuBtn" aria-expanded="false">
					<svg><use xlink:href=#navicon></use></svg>
				</button>
			</span>
		<?php } ?>

		<!-- Libname is defined in header_top.php -->
		<span id=\"library_name\" ><?php echo $libName; ?></span>

		<?php
			if ($tab != "opac") {
				//	echo "<br />" . T("Staff Interface");
				if (Settings::get('show_lib_info') == 'Y') {
					echo "<hr class=\"hdrSpacer\"> \n";
					//echo "<div id=\"library_hours\">". Settings::get('library_hours') . "</div> \n";
					echo $open_hours->displayOpenHours();
					echo "<hr class=\"hdrSpacer\"> \n";
					echo "<div id=\"library_phone\">". Settings::get('library_phone') ."</div> \n";
				}
			}
		?>
		</h3>
		
		<?php if ($tab != 'opac') { ?>
			<form role="form" id="logoutForm" class="notForInstall" method="get" action="../shared/logout.php">
				<input type="submit" id="logoutBtn" value="<?php echo T("Logout") ?>" />
			</form>
		<?php } ?>
	</header>

	<?php if ($tab != 'opac') { ?>
		<hr class="notForInstall hdrSpacer" />

		<nav class="notForInstall">
			<?php Nav::display($params['nav']); ?>
		</nav>
	<?php } else 
		include("../opac/nav.php");
	?>

	<?php if ($tab != 'opac') { ?>
		<hr class="notForInstall hdrSpacer" />
		<footer>
		  <div id="obLogo">
				<!--a href="http://obiblio.sourceforge.net/"-->
				<a href="https://bitbucket.org/mstetson/obiblio-1.0-wip/">
					<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" alt="Powered by OpenBiblio" />
				</a>
				<br />
			</div>

			OpenBiblio Version: <?php echo H(OBIB_CODE_VERSION);?><br />
			For <a href="../COPYRIGHT.html">Legal Info</a>.
		</footer>
	<?php } ?>
</aside>
<?php } ?>

<!-- **************************************************************************************
	 * beginning of main body
	 **************************************************************************************-->
<main id="content" role="main" aria-role="main">
    <!--[if lt IE 10]>
      <p class="chromeframe">You are using an <strong>outdated</strong> browser.<br /> 
			Please <a href="http://browsehappy.com/">upgrade your browser</a> 
			or <a href="http://www.google.com/chromeframe/?redirect=true">
			activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->
<?php
if (isset($params['title']) && $params['title'] != '') {
	# $params['title'] should be coming from the translation system, allow HTML
	echo '<h3>'.$params['title'].'</h3>';
}
if (isset($_REQUEST['msg'])) {
	echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
}
