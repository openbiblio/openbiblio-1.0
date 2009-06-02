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

  session_cache_limiter(null);

  $tab = "admin";
  $nav = "themes";
  $headerWording="Add New";
  $focus_form_name = "newthemeform";
  $focus_form_field = "themeName";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #*  This is only used when copying an existing theme.
  #****************************************************************************
  if (isset($HTTP_GET_VARS["themeid"])){
    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);

    $themeid = $HTTP_GET_VARS["themeid"];
    include_once("../classes/Theme.php");
    include_once("../classes/ThemeQuery.php");
    include_once("../functions/errorFuncs.php");
    $themeQ = new ThemeQuery();
    $themeQ->connect();
    if ($themeQ->errorOccurred()) {
      $themeQ->close();
      displayErrorPage($themeQ);
    }
    $themeQ->execSelect($themeid);
    if ($themeQ->errorOccurred()) {
      $themeQ->close();
      displayErrorPage($themeQ);
    }
    $theme = $themeQ->fetchTheme();

    $postVars["titleBg"] = $theme->getTitleBg();
    $postVars["titleFontFace"] = $theme->getTitleFontFace();
    $postVars["titleFontSize"] = $theme->getTitleFontSize();
    if ($theme->getTitleFontBold()) {  
      $postVars["titleFontBold"] = "CHECKED";
    } else {
      $postVars["titleFontBold"] = "";
    }
    $postVars["titleFontColor"] = $theme->getTitleFontColor();
    $postVars["titleAlign"] = $theme->getTitleAlign();

    $postVars["primaryBg"] = $theme->getPrimaryBg();
    $postVars["primaryFontFace"] = $theme->getPrimaryFontFace();
    $postVars["primaryFontSize"] = $theme->getPrimaryFontSize();
    $postVars["primaryFontColor"] = $theme->getPrimaryFontColor();
    $postVars["primaryLinkColor"] = $theme->getPrimaryLinkColor();
    $postVars["primaryErrorColor"] = $theme->getPrimaryErrorColor();

    $postVars["alt1Bg"] = $theme->getAlt1Bg();
    $postVars["alt1FontFace"] = $theme->getAlt1FontFace();
    $postVars["alt1FontSize"] = $theme->getAlt1FontSize();
    $postVars["alt1FontColor"] = $theme->getAlt1FontColor();
    $postVars["alt1LinkColor"] = $theme->getAlt1LinkColor();

    $postVars["alt2Bg"] = $theme->getAlt2Bg();
    $postVars["alt2FontFace"] = $theme->getAlt2FontFace();
    $postVars["alt2FontSize"] = $theme->getAlt2FontSize();
    $postVars["alt2FontColor"] = $theme->getAlt2FontColor();
    $postVars["alt2LinkColor"] = $theme->getAlt2LinkColor();
    if ($theme->getAlt2FontBold()) {  
      $postVars["alt2FontBold"] = "CHECKED";
    } else {
      $postVars["alt2FontBold"] = "";
    }

    $postVars["borderColor"] = $theme->getBorderColor();
    $postVars["borderWidth"] = $theme->getBorderWidth();
    $postVars["tablePadding"] = $theme->getTablePadding();

    $themeQ->close();
  }


?>

<script language="JavaScript" type="text/javascript">
<!--
function previewTheme() {
  var SecondaryWin;
  SecondaryWin = window.open('',"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
  document.newthemeform.action = "../admin/theme_preview.php";
  document.newthemeform.target = 'secondary';
  document.newthemeform.submit();
}
function editTheme() {
  document.newthemeform.action = "../admin/theme_new.php";
  document.newthemeform.target = '';
  document.newthemeform.submit();
}
-->
</script>


<a href="javascript:previewTheme()">Preview Theme</a>

<form name="newthemeform" method="POST" action="../admin/theme_new.php">
<?php include("../admin/theme_fields.php"); ?>
<?php include("../shared/footer.php"); ?>
