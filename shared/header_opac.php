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

// code html tag with language attribute if specified.
echo "<html";
if (OBIB_HTML_LANG_ATTR != "") {
  echo " lang=\"".OBIB_HTML_LANG_ATTR."\"";
}
echo ">\n";

// code character set if specified
if (OBIB_CHARSET != "") { ?>
<META http-equiv="content-type" content="text/html; charset=<?php echo OBIB_CHARSET; ?>">
<?php } ?>

<style type="text/css">
  <?php include("../css/style.php");?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo OBIB_LIBRARY_NAME;?></title>

<script language="JavaScript">
<!--
function popSecondary(url) {
    var SecondaryWin;
    SecondaryWin = window.open(url,"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
}
function returnLookup(formName,fieldName,val) {
    window.opener.document.forms[formName].elements[fieldName].value=val;
    window.opener.focus();
    this.close();
}
-->
</script>


</head>
<body bgcolor="<?php echo OBIB_PRIMARY_BG;?>" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" <?php
  if (isset($focus_form_name) && ($focus_form_name != "")) {
  echo 'onLoad="self.focus();document.'.$focus_form_name.".".$focus_form_field.'.focus()"';
} ?> >


<!-- **************************************************************************************
     * Library Name and hours
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo OBIB_TITLE_BG;?>">
    <td width="100%" class="title" valign="top">
       <?php
         if (OBIB_LIBRARY_IMAGE_URL != "") {
           echo "<img align=\"middle\" src=\"".OBIB_LIBRARY_IMAGE_URL."\" border=\"0\">";
         }
         if (!OBIB_LIBRARY_USE_IMAGE_ONLY) {
           echo " ".OBIB_LIBRARY_NAME;
         }
       ?>
    </td>
    <td valign="top">
      <table class="primary" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td class="title" nowrap="yes"><font class="small">today's date:</font></td>
          <td class="title" nowrap="yes"><font class="small"><?php print date("m.d.Y");?></font></td>
        </tr>
        <tr>
          <td class="title" nowrap="yes"><font class="small"><?php if (OBIB_LIBRARY_HOURS != "") echo "library hours:";?></font></td>
          <td class="title" nowrap="yes"><font class="small"><?php if (OBIB_LIBRARY_HOURS != "") echo OBIB_LIBRARY_HOURS;?></font></td>
        </tr>
        <tr>
          <td class="title" nowrap="yes"><font class="small"><?php if (OBIB_LIBRARY_PHONE != "") echo "library phone:";?></font></td>
          <td class="title" nowrap="yes"><font class="small"><?php if (OBIB_LIBRARY_PHONE != "") echo OBIB_LIBRARY_PHONE;?></font></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- **************************************************************************************
     * Tabs
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
  </tr>
</table>
<!-- **************************************************************************************
     * Left nav
     **************************************************************************************-->
<table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo OBIB_ALT1_BG;?>">
    <td colspan="6"><img src="../images/shim.gif" width="1" height="15" border="0"></td>
  </tr>
  <tr>
    <td bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="10" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="140" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="10" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="10" height="1" border="0"></td>
  </tr>
  <tr>
    <td bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td valign="top" bgcolor="<?php echo OBIB_ALT1_BG;?>">
      <font  class="alt1">
      <?php include("../navbars/opac.php"); ?>
      </font>
    <br><br><br><br>
    </td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_PRIMARY_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td height="100%" width="100%" valign="top">
      <font class="primary">
      <br>
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->