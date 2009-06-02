<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
?>
<html>
<head>
<style type="text/css">
  <?php include("../css/style.css"); ?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title>OpenBiblio Install</title>
</head>
<body bgcolor="#ffffff" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" <?php
  if (isset($focus_form_name) && ($focus_form_name != "")) {
    if (ereg('^[a-zA-Z0-9_]+$', $focus_form_name)
        && ereg('^[a-zA-Z0-9_]+$', $focus_form_field)) {
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
<font class="primary">
