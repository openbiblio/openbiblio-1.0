<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

		### following needed since this is included from within a class method -- Fred
		global $nav, $tab, $focus_form_name, $focus_form_field;
		
?>
<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<?php // code character set if specified
if (Settings::get('charset') != "") { ?>
<meta http-equiv="content-type" content="text/html; charset=<?php echo H(Settings::get('charset')); ?>" />
<?php } ?>

<link rel="stylesheet" type="text/css" href="../shared/base.css" />
<link rel="stylesheet" type="text/css" href="<?php echo H($params['theme_dir_url']) ?>/style.css" />

<title><?php echo H(Settings::get('library_name'));?></title>

<!-- jQuery kernal, needed for all that follows -->
<script src="../shared/jquery/jquery.js" type="text/javascript"></script>
<!-- home-grown add-ons to the jQuery library, feel free to add your own -->
<script src="../shared/jsLib.js" type="text/javascript"></script>

<script language="JavaScript">
<!--
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
	reStripe: function(e) {
		// re-stripe all tables so classed on all pages
	  $('table tbody.striped tr:even').addClass('altBG');
//	  $('table tbody.striped tr:odd').removeClass('altBG');
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

// hold off javascript until DOM is fully loaded, images, etc, may not all be loaded yet.
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
	

