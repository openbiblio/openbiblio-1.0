<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	### following needed since this is included from within a class method -- Fred
	global $Locale, $CharSet, $nav, $tab, $focus_form_name, $focus_form_field, $doing_install;
?>
	
<!DOCTYPE html >
<!-- there are many lines here with obscure comments. For more info see http://html5boilerplate.com/ -->

<!-- language is set by user (default is 'en') -->
<html lang="<?php echo $Locale; ?>" class="no-js <?php echo ($doing_install?'obInstall':'no-obInstall'); ?>" >

<head>
  <!-- charset MUST be specified within first 1024 char of file start to be effective -->
  <meta charset="<?php echo $CharSet; ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- select browser-top icon -->
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="../apple-touch-icon.png">

  <!-- build title using library's name (or current-site name) from database -->
  <title>
    <?php
    if (!isset($doing_install) or !$doing_install) {
    	// If the cookie contains a site id, we take this one, otherwise the default.
    	// Adjusted, so that if 'library_name' contains a string, the site is put by default on 1.
    	$libName  = Settings::get('library_name');
    	if(empty($_SESSION['current_site'])) {
    		if(isset($_COOKIE['OpenBiblioSiteID'])) {
    			$site = new Sites;
    			$exists_in_db = $site->maybeGetOne($_COOKIE['OpenBiblioSiteID']);
    			if ($exists_in_db['siteid'] != $_COOKIE['OpenBiblioSiteID']) {
                    $_COOKIE['OpenBiblioSiteID'] = 1;
    			}
        			$_SESSION['current_site'] = $_COOKIE['OpenBiblioSiteID'];
    		} elseif($_SESSION['multi_site_func'] > 0){
    			$_SESSION['current_site'] = $_SESSION['multi_site_func'];
    		} else {
    			$_SESSION['current_site'] = 1;
    		}
    	}

    	if($_SESSION['multi_site_func'] > 0){
    		$sit = new Sites;
    		$lib = $sit->maybeGetOne($_SESSION['current_site']);
		    if ($lib['siteid'] != $_SESSION['current_site']) {
    			$lib = $sit->getOne(1);
		    }
    		$libName = $lib[name];
    	}

    	echo $libName;
		//    	if($params['title']) {
		//    		echo ': '.H($params['title']);
		//    	}
    }
    ?>
  </title>

  <meta name="description" content="OpenBiblio ver 1.0a">
  <meta name="author" content="Luuk Jansen">
  <meta name="author" content="Fred LaPlante">
  <meta name="author" content="Jane Sandberg">
  <meta name="author" content="Micah Stetson">
  <meta name="tester" content="Neil Redgate">
  <meta name="tester" content="Charlie Tudor">

  <!-- this line MUST precede all .css & JS files - FL
  		 Based on the browser in use, it places many conditional classes
  		 into the <body> tag for use by feature-specific CSS & JS statements.
  		 It also deals with html5 support issues for older IE browsers. 	 -->
  <script src="../shared/modernizr-2.6.2.min.js"></script>

  <!-- *********************************************************************** -->
  <!-- prefixFree.js adds appropriate vendor prefixes to CSS as needed -->
  <!-- this is considered to be temporary until the use of prefixes ends -->
  <!--script src="../shared/prefixfree.min.js"></script> <!-- review yearly -->
  <!-- *********************************************************************** -->

  <!-- we place these JS files here because several JS modules loaded in line -->
  <!-- depend on them being in place. -->
  <!--[if lt IE 9]><script src="../shared/jquery/jquery-1.10.2.min.js"></script><!--<![endif]-->
  <!--[if gt IE 8]><!-->
  <script src="../shared/jquery/jquery-3.2.1.min.js"></script>
  <!--<![endif]-->

  <!-- All other JavaScript is placed at the end of <body> (see footer.php)
  		 to match industry best practices and to improve overall performance -->

  <!-- This style sheet resets all browsers to a common default style -->
  <link rel="stylesheet" href="../shared/normalize.css" />

  <!-- This style sheet is specific to the jQuery UI library -->
  <link rel="stylesheet" href="../shared/jquery/jquery-ui.min.css" />

  <!-- OpenBiblio style is set here using appropriate Theme folder -->
  <link rel="stylesheet" href="<?php echo H($params['theme_dir_url']) ?>/style.css" />
