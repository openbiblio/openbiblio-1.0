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

  /****************************************************************************
   * Reading general settings
   ****************************************************************************
   */
  include_once("../classes/Settings.php");
  include_once("../classes/SettingsQuery.php");
  $setQ = new SettingsQuery();
  $setQ->connect();
  if ($setQ->errorOccurred()) {
    $setQ->close();
    displayErrorPage($setQ);
  }
  $setQ->execSelect();
  if ($setQ->errorOccurred()) {
    $setQ->close();
    displayErrorPage($setQ);
  }
  $set = $setQ->fetchRow();
  $setQ->close();

  /****************************************************************************
   * general settings constants
   ****************************************************************************
   */
  define("OBIB_LOCALE",$set->getLocale());


  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,"admin");

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
<title><?php echo $HTTP_POST_VARS["themeName"]; ?> <? echo $loc->getText("adminTheme_preview_Themepreview"); ?></title>

</head>
<body bgcolor="<?php echo OBIB_PRIMARY_BG;?>" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onLoad="self.focus()">


<!-- **************************************************************************************
     * Library Name and hours
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo OBIB_TITLE_BG;?>">
    <td width="100%" class="title" valign="top">
      <? echo $loc->getText("adminTheme_preview_Librarytitle"); ?>
    </td>
    <td class="title" valign="top" nowrap="yes"><font class="small"><a href="javascript:window.close()"><font color="<?php echo OBIB_TITLE_FONT_COLOR?>"><? echo $loc->getText("adminTheme_preview_CloseWindow"); ?></font></a>&nbsp;&nbsp;</font></td>
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
      <td class="tab1" nowrap="yes"> <? echo $loc->getText("adminTheme_preview_Home"); ?> </td>
      <td  bgcolor="<?php echo OBIB_ALT1_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab2" nowrap="yes"> <a href="#" class="tab"><? echo $loc->getText("adminTheme_preview_Circulation"); ?></a> </td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab2" nowrap="yes"> <a href="#" class="tab"><? echo $loc->getText("adminTheme_preview_Cataloging"); ?></a> </td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td bgcolor="<?php echo OBIB_BORDER_COLOR;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td  bgcolor="<?php echo OBIB_ALT2_BG;?>"><img src="../images/shim.gif" width="1" height="1" border="0"></td>
      <td class="tab2" nowrap="yes"> <a href="#" class="tab"><? echo $loc->getText("adminTheme_preview_Admin"); ?></a> </td>
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
        &raquo; <? echo $loc->getText("adminTheme_preview_Themepreview"); ?><br>
        <a href="#" class="alt1"><? echo $loc->getText("adminTheme_preview_Samplelink"); ?></a><br>
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

<? echo $loc->getText("adminTheme_preview_Thisstart"); ?><?php echo $HTTP_POST_VARS["themeName"]; ?> <? echo $loc->getText("adminTheme_preview_Thisend"); ?>

<h1><? echo $loc->getText("adminTheme_preview_Samplelist"); ?></h1>
<table class="primary">
  <tr>
    <th valign="top">
      <? echo $loc->getText("adminTheme_preview_Tableheading"); ?>
    </th>
  </tr>
  <tr>
    <td valign="top" class="primary">
      <? echo $loc->getText("adminTheme_preview_Sampledatarow1"); ?>
    </td>
  </tr>
  <tr>
    <td valign="top" class="alt1">
      <? echo $loc->getText("adminTheme_preview_Sampledatarow2"); ?>
    </td>
  </tr>
  <tr>
    <td valign="top" class="primary">
      <? echo $loc->getText("adminTheme_preview_Sampledatarow3"); ?>
    </td>
  </tr>
</table>
<br>
<a href="#"><? echo $loc->getText("adminTheme_preview_Samplelink"); ?></a><br>
<font class="error"><? echo $loc->getText("adminTheme_preview_Sampleerror"); ?></font><br />
<form>
<input type="text" name="sampleInput" size="40" maxlength="40" value="<? echo $loc->getText("adminTheme_preview_Sampleinput"); ?>" ><br />
<input type="button" value="<? echo $loc->getText("adminTheme_preview_Samplebutton"); ?>" class="button">
</form>

<!-- **************************************************************************************
     * Footer
     **************************************************************************************-->
<br><br><br>
</font>
<font face="Arial, Helvetica, sans-serif" size="1" color="<?php echo OBIB_PRIMARY_FONT_COLOR;?>">
<center>
  <br><br>
  <? echo $loc->getText("adminTheme_preview_Poweredby"); ?><br>
  <? echo $loc->getText("adminTheme_preview_Copyright"); ?> <a href="http://dave.stevens.name">Dave Stevens</a><br>
  <? echo $loc->getText("adminTheme_preview_underthe"); ?>
  <a href="../shared/copying.html"><? echo $loc->getText("adminTheme_preview_GNU"); ?></a>
</center>
<br>
</font>
    </td>
  </tr>
</table>
</body>
</html>