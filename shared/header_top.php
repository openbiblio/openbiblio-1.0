<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	### following needed since this is included from within a class method -- Fred
	global $CharSet, $nav, $tab, $focus_form_name, $focus_form_field, $doing_install;

?>
?>
	
<!DOCTYPE html >
<!-- there are many lines here with obscure comments. For more info see http://html5boilerplate.com/ -->

<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->  
<html lang="en" class="no-js <?php echo ($doing_install?'obInstall':'no-obInstall'); ?>" >
<!--<![endif]-->

<head>	
<!-- charset MUST be specified within first 1024 char of file start to be effective -->
<meta charset="<?php echo $CharSet; ?>" />

<title>
<?php
if (!isset($doing_install) or !$doing_install) {
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
}
?>
</title>

<meta name="description" content="OpenBiblio ver 1.0a">
<meta name="author" content="Luuk Jansen">
<meta name="author" content="Fred LaPlante">
<meta name="author" content="Micah Stetson">
<meta name="tester" content="Neil Redgate">

<!-- Mobile viewport optimized: j.mp/bplateviewport -->
<meta name="viewport" content="width=device-width">

<!--link rel="icon" href="../favicon.ico" type="image/x-icon" /-->
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" /> 

<!-- this line MUST precede all .css & JS files - FL
		 Based on the browser in use, it places many conditional classes 
		 into the <body> tag for use by feature-specific CSS & JS statements.
		 It also deals with html5 support issues for IE browsers. 	 -->
<script src="../shared/modernizr-2.6.2.min.js"></script>

<!-- prefixFree.js adds appropriate vendor prefixes to CSS as needed -->
<script src="../shared/prefixfree.min.js"></script>
  
<!-- All other JavaScript is placed at the end of <body> (see footer.php) 
		 to match industry best practices and to improve overall performance -->

<!-- This style sheet resets all browsers to a common default style -->
<link rel="stylesheet" href="../shared/base3.css" />

<!-- OpenBiblio style is set here using appropriate Theme folder -->
<link rel="stylesheet" href="<?php echo H($params['theme_dir_url']) ?>/style.css" />

	

