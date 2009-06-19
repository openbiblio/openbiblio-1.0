<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
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
<script language="JavaScript">
<!--
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
function confirmLink(e) {
		if (modified) {
			return confirm("<?php echo addslashes(T("This will discard any changes you've made on this page.  Are you sure?")) ?>");
		} else {
			return true;
		}
}
function init() {
<?php
if (isset($focus_form_name) && ($focus_form_name != "")) {
	echo 'self.focus();';
	echo 'document.'.$focus_form_name.'.'.$focus_form_field.'.focus();';
}
if (isset($confirm_links) and $confirm_links) {
?>
	elems = document.getElementsByTagName('a');
	for (i=0; i<elems.length; i++) {
		if (!elems[i].onclick) {
			elems[i].onclick = l=confirmLink;
			if (elems[i].captureEvents) elems[i].captureEvents(Event.CLICK);
		}
	}
<?php } ?>
}
-->
</script>
	
	<?php
	## ---------------------------------------------------------------------
	## --- added for Fred LaPlante's Lookup Function -----------------------
	if (file_exists('custom_head.php')) {
		include ('custom_head.php');
		// in this case, the local javascript is responsible for calling the core
		// code's init() routine at an appropriate time.
		echo "</head>\n";
		echo "<body>\n";
	} else {
		echo "</head>\n";
		echo "<body onload=\"init();\" >\n";
	}
	## ---------------------------------------------------------------------
	?>
	

