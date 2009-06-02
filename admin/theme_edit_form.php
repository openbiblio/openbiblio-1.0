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
  //$headerWording="Edit";
  $focus_form_name = "editthemeform";
  $focus_form_field = "themeName";

  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  
  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($_GET["themeid"])){
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);

    $themeid = $_GET["themeid"];
    $postVars["themeid"] = $themeid;
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

    $postVars["themeName"] = $theme->getThemeName();
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
  } else {
    require("../shared/get_form_vars.php");
  }


?>

<script language="JavaScript" type="text/javascript">
<!--
function previewTheme() {
  var SecondaryWin;
  SecondaryWin = window.open('',"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
  document.editthemeform.action = "../admin/theme_preview.php";
  document.editthemeform.target = 'secondary';
  document.editthemeform.submit();
}

function editTheme() {
  document.editthemeform.action = "../admin/theme_edit.php";
  document.editthemeform.target = '';
  document.editthemeform.submit();
}

-->
</script>


<a href="javascript:previewTheme()"><? echo $loc->getText("adminTheme_Preview"); ?></a><br /><br />

<form name="editthemeform" method="POST" action="../admin/theme_edit.php">
<input type="hidden" name="themeid" value="<?php echo $postVars["themeid"];?>">
<?php include("../admin/theme_fields.php"); ?>
<?php include("../shared/footer.php"); ?>
