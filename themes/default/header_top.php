<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	### following needed since this is included from within a class method -- Fred
	global $nav, $tab, $focus_form_name, $focus_form_field;

?>

<!DOCTYPE html >
<html lang="en">

<head>	
<!-- charset MUST be specified within first 512 char of page to be effective -->
<?php // change default code character set if specified
	if (Settings::get('charset') != "") { 
		echo "<meta charset=".H(Settings::get('charset'))." \" />";
	} else {
		echo "<meta charset=\"UTF-8\" />";
	}
?>

<title>
<?php
	// If the cookie contains a site id, we take this one, otherwise the default.
	// Adjusted, so that if 'library_name' contains a string, the site is put by default on 1.
	$libName  = Settings::get('library_name');
	if(empty($_SESSION['current_site'])) {
		if(isset($_COOKIE['OpenBiblioSiteID'])) {
			$_SESSION['current_site'] = $_COOKIE['OpenBiblioSiteID'];
		} elseif($_SESSION['multi_site_func'] > 0){
			$_SESSION['current_site'] = $_SESSION['multi_site_func'];
		} else {
			$_SESSION['current_site'] = 1;
		}
	}
	
	if($_SESSION['multi_site_func'] > 0){	
		$sit = new Sites;
		$lib = $sit->getOne($_SESSION['current_site']);
		$libName = $lib[name];				
	} 	
		
	echo $libName;
	if($params['title']) {
		echo ': '.H($params['title']);
	}		
?>
</title>
<meta name="description" content="OpenBiblio ver 1.0 (wip)">
<meta name="author" content="Luuk Jansen">
<meta name="author" content="Fred LaPlante">
<meta name="author" content="Micah Stetson">

<link rel="icon" href="../favicon.ico" type="image/x-icon" /-->
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" /> 

<!-- this line MUST precede all .css files - FL
		 Based on the browser in use, it places many conditional classes 
		 into the <body> tag for use by feature-specific CSS & JS statements.
		 It also deals with html5 support issues for IE browsers. 	 -->
<script src="../shared/modernizr-1.7.min.js"></script>

<link rel="stylesheet" href="../shared/base.css" />
<link rel="stylesheet" href="<?php echo H($params['theme_dir_url']) ?>/style.css" />

<!-- All other JavaScript is placed at the end of <body> (see footer.php) 
		 to match industry best practices and to improve overall performance -->

	<?php
	## ---------------------------------------------------------------------
	## --- added plugin support -- Fred -----------------------
	if (file_exists('custom_head.php')) {
		include ('custom_head.php');
	}
	## ---------------------------------------------------------------------
	?>

	</head>
	<body>
	

