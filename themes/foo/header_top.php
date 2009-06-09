<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
// code html tag with language attribute if specified.
echo "<html";
if (Settings::get('html_lang_attr') != "") {
	echo " lang=\"".H(Settings::get('html_lang_attr'))."\"";
}
echo ">\n"; ?>
<head>
<?php // code character set if specified
if (Settings::get('charset') != "") { ?>
<meta http-equiv="content-type" content="text/html; charset=<?php echo H(Settings::get('charset')); ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="../css/style.php?themeid=<?php echo HURL(Settings::get('themeid')); ?>" />
<meta name="description" content="OpenBiblio Library Automation System">
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

<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" <?php
	if (isset($focus_form_name) && ($focus_form_name != "")) {
		if (ereg('^[a-zA-Z0-9_]+$', $focus_form_name)
				&& ereg('^[a-zA-Z0-9_]+$', $focus_form_field)) {
			echo 'onLoad="self.focus();document.'.$focus_form_name.".".$focus_form_field.'.focus()"';
		}
	} ?> onLoad="init()">

<!-- **************************************************************************************
		 * Library Name and hours
		 **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr> 
		<td width="100%" class="title" valign="top">
			 <?php
				 if (Settings::get('library_image_url') != "") {
					 echo "<img align=\"middle\" src=\"".H(Settings::get('library_image_url'))."\" border=\"0\">";
				 }
				 if (Settings::get('use_image_flg') != "Y") {
					 echo " ".H(Settings::get('library_name'));
				 }
			 ?>
		</td>
		<td valign="top">
			<table class="primary" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="title" nowrap="yes"><font class="small"><?php echo T("Today's Date"); ?></font></td>
					<td class="title" nowrap="yes"><font class="small"><?php echo H(date(T("headerDateFormat"))); ?></font></td>
				</tr>
				<tr>
					<td class="title" nowrap="yes"><font class="small"><?php if (Settings::get('library_hours') != "") echo T("Library Hours"); ?></font></td>
					<td class="title" nowrap="yes"><font class="small"><?php if (Settings::get('library_hours') != "") echo H(Settings::get('library_hours')); ?></font></td>
				</tr>
				<tr>
					<td class="title" nowrap="yes"><font class="small"><?php if (Settings::get('library_phone') != "") echo T("Library Phone"); ?></font></td>
					<td class="title" nowrap="yes"><font class="small"><?php if (Settings::get('library_phone') != "") echo H(Settings::get('library_phone') ); ?></font></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

