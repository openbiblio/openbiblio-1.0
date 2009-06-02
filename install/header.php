<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
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
  echo 'onLoad="self.focus();document.'.$focus_form_name.".".$focus_form_field.'.focus()"';
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