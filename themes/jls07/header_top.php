<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	### following needed since this is included from within a class method -- Fred
	global $nav, $tab, $focus_form_name, $focus_form_field;

?>

<!DOCTYPE html >

<?php // change default code character set if specified
	if (Settings::get('charset') != "") { 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".H(Settings::get('charset'))." \" />";
	} else {
		echo "<meta charset=\"UTF-8\" />";
	}
?>

<html lang="en" manifest="../cache.manifest">
<head>	

<!-- this line MUST precede all .css files - FL -->
<!-- it deals with html5 support issues for IE8 -->
<script src="../shared/html5shiv.js" type="text/javascript"></script>

<link rel="icon" href="../favicon.ico" type="image/x-icon" /-->
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" /> 

<link rel="stylesheet" type="text/css" href="../shared/base.css" />
<link rel="stylesheet" type="text/css" href="<?php echo H($params['theme_dir_url']) ?>/style.css" />

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

<script src="../shared/jquery/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="../shared/jsLib.js" type="text/javascript"></script>

<script language="JavaScript">


// main javascript functionality set in own namespace to avoid potential conflict
obib = {
	<?php
	echo "focusFormName:  '$focus_form_name',\n";
	echo "focusFormField:	'$focus_form_field',\n";
	if (isset($confirm_links) and $confirm_links) {
		echo "confirmLinks:		$confirm_links,\n";
	}
	?>

	init: function() {
		obib.reStripe();
	  // set focus to specified field in all pages
		if ((obib.focusFormName.length > 0) && (obib.focusFormField.length > 0)) {
		  $('#'+obib.focusFormField).focus();
		}
		
		// suggest this should be in code local to desired function unless widely used -- Fred
		// bind the confirmLink routine to all <a> tags on the current form
		if (obib.confirmLinks) {
			$('a').bind('click',null,obib.confirmLink);
		}
	},

	//-------------------------
	reStripe: function(which) {
		// re-stripe all tables so classed on all pages
	  	$('table tbody.striped tr:even').addClass('altBG');
	  	$('table tbody.striped tr:odd').removeClass('altBG');
	},
	reStripe2: function(tblName, oddEven) {
		// re-stripe specified table
		if (oddEven == 'even') {
			//console.log('striping even rows of table: '+tblName);
	  	$('#'+tblName+' tbody.striped tr:even').addClass('altBG');
	  	$('#'+tblName+' tbody.striped tr:odd').removeClass('altBG');
		}
		else if (oddEven == 'odd') {
			//console.log('striping odd rows of table: '+tblName);
	  	$('#'+tblName+' tbody.striped tr:even').addClass('altBG');
	  	$('#'+tblName+' tbody.striped tr:odd').removeClass('altBG');
	 }
	},
	//-------------------------
	confirmLink: function(e) {
		if (modified) {
			return confirm("<?php echo addslashes(T("This will discard any changes you've made on this page.  Are you sure?")) ?>");
		} else {
			return true;
		}
	}
}

// hold off javascript until DOM is fully loaded; images, etc, may not all be loaded yet.
$(document).ready(obib.init);

function popSecondary(url) {
		var SecondaryWin;
		SecondaryWin = window.open(url,"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
		self.name="main";
}
function popSecondaryLarge(url) {
		var SecondaryWin;
		SecondaryWin = window.open(url,"secondary","toolbar=yes,resizable=yes,scrollbars=yes,width=700,height=500");
		self.name="main";
}
function backToMain(URL) {
		var mainWin;
		mainWin = window.open(URL,"main");
		mainWin.focus();
		this.close();
}
var modified = false;

-->
</script>

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
	

