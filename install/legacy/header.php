<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
?>
<!DOCTYPE html >
<html lang="en">
<head>
<meta charset=\"UTF-8\" />
<style type="text/css">
	<?php include(REL(__FILE__, "../shared/base.css")); ?>
	<?php include(REL(__FILE__, "../themes/default/style.css")); ?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo T("OpenBiblio Install"); ?></title>
</head>
<body<?php
	if (isset($focus_form_name) && ($focus_form_name != "")) {
		if (preg_match('/^[a-zA-Z0-9_]+$/', $focus_form_name)
				&& preg_match('/^[a-zA-Z0-9_]+$/', $focus_form_field)) {
			echo 'onLoad="self.focus();document.'.$focus_form_name.".".$focus_form_field.'.focus()"';
		}
	} ?> >
<!-- **************************************************************************************
		 * OpenBiblio logo and black background with links and date
		 **************************************************************************************-->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr bgcolor="#bebdbe">
		<td align="left"><img src="../images/obiblio_logo.gif" width="170" height="35" border="0"></td>
		<td align="right" valign="top" width="100%"><font color="#ffffff">
		</td>
	</tr>
</table>

<!-- **************************************************************************************
		 * beginning of main body
		 **************************************************************************************-->
<font>
