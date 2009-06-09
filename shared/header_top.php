<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

?>
<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo H(Settings::get('html_lang_attr')); ?>" lang="<?php echo H(Settings::get('html_lang_attr')); ?>">
<head>
<?php // code character set if specified
if (Settings::get('charset') != "") { ?>
<meta http-equiv="content-type" content="text/html; charset=<?php echo H(Settings::get('charset')); ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="../css/style.php?themeid=<?php echo HURL(Settings::get('themeid')); ?>" />
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo H(Settings::get('library_name'));?></title>

<script type="text/javascript">
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
			return confirm("This will discard any changes you've made on this page.  Are you sure?");
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


</head>

<body bgcolor="<?php echo H(OBIB_PRIMARY_BG);?>" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" <?php
	if (isset($focus_form_name) && ($focus_form_name != "")) {
		if (ereg('^[a-zA-Z0-9_]+$', $focus_form_name)
				&& ereg('^[a-zA-Z0-9_]+$', $focus_form_field)) {
			echo 'onload="self.focus();document.'.$focus_form_name.".".$focus_form_field.'.focus()"';
		}
	} ?> onload="init()">

<!-- **************************************************************************************
		 * Library Name and hours
		 **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr bgcolor="<?php echo H(OBIB_TITLE_BG);?>">
		<td width="100%" class="title" valign="top">
			 <?php
				 if (OBIB_LIBRARY_IMAGE_URL != "") {
					 echo "<img align=\"middle\" src=\"".H(OBIB_LIBRARY_IMAGE_URL)."\" border=\"0\">";
				 }
				 if (!OBIB_LIBRARY_USE_IMAGE_ONLY) {
					 echo " ".H(OBIB_LIBRARY_NAME);
				 }
			 ?>
		</td>
		<td valign="top">
			<table class="primary" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="title" nowrap="yes"><?php echo T("Today's Date"); ?></td>
					<td class="title" nowrap="yes"><?php echo H(date(T("headerDateFormat")));?></td>
				</tr>
				<tr>
					<td class="title" nowrap="yes"><?php if (OBIB_LIBRARY_HOURS != "") echo T("Library Hours"); ?></td>
					<td class="title" nowrap="yes"><?php if (OBIB_LIBRARY_HOURS != "") echo H(OBIB_LIBRARY_HOURS);?></td>
				</tr>
				<tr>
					<td class="title" nowrap="yes"><?php if (OBIB_LIBRARY_PHONE != "") echo T("Library Phone"); ?></td>
					<td class="title" nowrap="yes"><?php if (OBIB_LIBRARY_PHONE != "") echo H(OBIB_LIBRARY_PHONE); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

