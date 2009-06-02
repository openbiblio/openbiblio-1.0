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

  /****************************************************************************
   * theme related constants.
   ****************************************************************************
   */
  $tab = "home";

  define("OBIB_TITLE_BG",$HTTP_POST_VARS["titleBg"]);
  define("OBIB_TITLE_FONT_FACE",$HTTP_POST_VARS["titleFontFace"]);
  define("OBIB_TITLE_FONT_SIZE",$HTTP_POST_VARS["titleFontSize"]);
  define("OBIB_TITLE_FONT_BOLD",isset($HTTP_POST_VARS["titleFontBold"]));
  define("OBIB_TITLE_ALIGN",$HTTP_POST_VARS["titleAlign"]);
  define("OBIB_TITLE_FONT_COLOR",$HTTP_POST_VARS["titleFontColor"]);

  define("OBIB_PRIMARY_BG",$HTTP_POST_VARS["primaryBg"]);
  define("OBIB_PRIMARY_FONT_FACE",$HTTP_POST_VARS["primaryFontFace"]);
  define("OBIB_PRIMARY_FONT_SIZE",$HTTP_POST_VARS["primaryFontSize"]);
  define("OBIB_PRIMARY_FONT_COLOR",$HTTP_POST_VARS["primaryFontColor"]);
  define("OBIB_PRIMARY_LINK_COLOR",$HTTP_POST_VARS["primaryLinkColor"]);
  define("OBIB_PRIMARY_ERROR_COLOR",$HTTP_POST_VARS["primaryErrorColor"]);

  define("OBIB_ALT1_BG",$HTTP_POST_VARS["alt1Bg"]);
  define("OBIB_ALT1_FONT_FACE",$HTTP_POST_VARS["alt1FontFace"]);
  define("OBIB_ALT1_FONT_SIZE",$HTTP_POST_VARS["alt1FontSize"]);
  define("OBIB_ALT1_FONT_COLOR",$HTTP_POST_VARS["alt1FontColor"]);
  define("OBIB_ALT1_LINK_COLOR",$HTTP_POST_VARS["alt1LinkColor"]);

  define("OBIB_ALT2_BG",$HTTP_POST_VARS["alt2Bg"]);
  define("OBIB_ALT2_FONT_FACE",$HTTP_POST_VARS["alt2FontFace"]);
  define("OBIB_ALT2_FONT_SIZE",$HTTP_POST_VARS["alt2FontSize"]);
  define("OBIB_ALT2_FONT_COLOR",$HTTP_POST_VARS["alt2FontColor"]);
  define("OBIB_ALT2_LINK_COLOR",$HTTP_POST_VARS["alt2LinkColor"]);
  define("OBIB_ALT2_FONT_BOLD",isset($HTTP_POST_VARS["alt2FontBold"]));

  define("OBIB_BORDER_COLOR",$HTTP_POST_VARS["borderColor"]);
  define("OBIB_BORDER_WIDTH",$HTTP_POST_VARS["borderWidth"]);
  define("OBIB_PADDING",$HTTP_POST_VARS["tablePadding"]);

?>

<html>
<head>
<style type="text/css">
  <?php include("../css/style.php");?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo $HTTP_POST_VARS["themeName"]; ?> Theme Preview</title>

</head>
<body bgcolor="<?php echo OBIB_PRIMARY_BG;?>" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onLoad="self.focus()">


<!-- **************************************************************************************
     * Library Name and hours
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo OBIB_TITLE_BG;?>">
    <td width="100%" class="title" valign="top">
      Library Title
    </td>
    <td class="title" valign="top" nowrap="yes"><font class="small"><a href="javascript:window.close()"><font color="<?php echo OBIB_TITLE_FONT_COLOR?>">Close Window</font></a>&nbsp;&nbsp;</font></td>
  </tr>
</table>
<!-- **************************************************************************************
     * Tabs
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>" colspan="3"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>" colspan="3"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>" colspan="3"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>" colspan="3"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
  </tr>
  <tr bgcolor="<?php echo OBIB_TITLE_BG;?>">
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td width="2000" bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

  </tr>
  <tr bgcolor="<?php echo OBIB_TITLE_BG;?>">
      <td  bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab1" nowrap="yes"> Home </td>
      <td  bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab2" nowrap="yes"> <a href="#" class="tab">Circulation</a> </td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab2" nowrap="yes"> <a href="#" class="tab">Cataloging</a> </td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab2" nowrap="yes"> <a href="#" class="tab">Admin</a> </td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td width="2000" bgcolor="<?php echo OBIB_TITLE_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>

  </tr>
  <tr bgcolor="<?php echo OBIB_BORDER_COLOR;?>">
    <td colspan="3" <?php if ($tab == "home") { print " bgcolor='".OBIB_ALT1_BG."'"; } ?>><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td colspan="3" <?php if ($tab == "circulation") { print " bgcolor='".OBIB_ALT1_BG."'"; } ?>><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td colspan="3" <?php if ($tab == "cataloging") { print " bgcolor='".OBIB_ALT1_BG."'"; } ?>><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td colspan="3" <?php if ($tab == "admin") { print " bgcolor='".OBIB_ALT1_BG."'"; } ?>><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td><img src="../images/shim.gif" width="1" height="1" border="0"></td>
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
        &raquo; Theme Preview<br>
        <a href="#" class="alt1">Sample Link</a><br>
      </font>
    <br><br><br><br>
    <a href="http://obiblio.sourceforge.net/"><img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0"></a>
    </td>
    <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td bgcolor="<?php echo OBIB_PRIMARY_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
    <td height="100%" width="100%" valign="top">
      <font class="primary">
      <br>
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->

This is a preview of the <?php echo $HTTP_POST_VARS["themeName"]; ?> theme.

<h1>Sample List:</h1>
<table class="primary">
  <tr>
    <th valign="top">
      Table Heading
    </th>
  </tr>
  <tr>
    <td valign="top" class="primary">
      Sample data row 1
    </td>
  </tr>
  <tr>
    <td valign="top" class="alt1">
      Sample data row 2
    </td>
  </tr>
  <tr>
    <td valign="top" class="primary">
      Sample data row 3
    </td>
  </tr>
</table>
<br>
<a href="#">sample link</a><br>
<font class="error">sample error</font><br />
<form>
<input type="text" name="sampleInput" size="40" maxlength="40" value="Sample Input" ><br />
<input type="button" value="Sample Button" class="button">
</form>

<!-- **************************************************************************************
     * Footer
     **************************************************************************************-->
<br><br><br>
</font>
<font face="Arial, Helvetica, sans-serif" size="1" color="<?php echo OBIB_PRIMARY_FONT_COLOR;?>">
<center>
  <br><br>
  Powered by OpenBiblio<br>
  Copyright &copy; 2002 <a href="http://dave.stevens.name">Dave Stevens</a><br>
  under the
  <a href="../shared/copying.html">GNU General Public License</a>
</center>
<br>
</font>
    </td>
  </tr>
</table>
</body>
</html>