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

  $tab = "admin";
  $nav = "themes";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Theme.php");
  require_once("../classes/ThemeQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  
  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../admin/theme_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $theme = new Theme();
  $theme->setThemeid($HTTP_POST_VARS["themeid"]);
  $HTTP_POST_VARS["themeid"] = $theme->getThemeid();
  $theme->setThemeName($HTTP_POST_VARS["themeName"]);
  $HTTP_POST_VARS["themeName"] = $theme->getThemeName();

  $theme->setTitleBg($HTTP_POST_VARS["titleBg"]);
  $HTTP_POST_VARS["titleBg"] = $theme->getTitleBg();
  $theme->setTitleFontFace($HTTP_POST_VARS["titleFontFace"]);
  $HTTP_POST_VARS["titleFontFace"] = $theme->getTitleFontFace();
  $theme->setTitleFontSize($HTTP_POST_VARS["titleFontSize"]);
  $HTTP_POST_VARS["titleFontSize"] = $theme->getTitleFontSize();
  $theme->setTitleFontBold(isset($HTTP_POST_VARS["titleFontBold"]));
  $theme->setTitleFontColor($HTTP_POST_VARS["titleFontColor"]);
  $HTTP_POST_VARS["titleFontColor"] = $theme->getTitleFontColor();
  $theme->setTitleAlign($HTTP_POST_VARS["titleAlign"]);

  $theme->setPrimaryBg($HTTP_POST_VARS["primaryBg"]);
  $HTTP_POST_VARS["primaryBg"] = $theme->getPrimaryBg();
  $theme->setPrimaryFontFace($HTTP_POST_VARS["primaryFontFace"]);
  $HTTP_POST_VARS["primaryFontFace"] = $theme->getPrimaryFontFace();
  $theme->setPrimaryFontSize($HTTP_POST_VARS["primaryFontSize"]);
  $HTTP_POST_VARS["primaryFontSize"] = $theme->getPrimaryFontSize();
  $theme->setPrimaryFontColor($HTTP_POST_VARS["primaryFontColor"]);
  $HTTP_POST_VARS["primaryFontColor"] = $theme->getPrimaryFontColor();
  $theme->setPrimaryLinkColor($HTTP_POST_VARS["primaryLinkColor"]);
  $HTTP_POST_VARS["primaryLinkColor"] = $theme->getPrimaryLinkColor();
  $theme->setPrimaryErrorColor($HTTP_POST_VARS["primaryErrorColor"]);
  $HTTP_POST_VARS["primaryErrorColor"] = $theme->getPrimaryErrorColor();

  $theme->setAlt1Bg($HTTP_POST_VARS["alt1Bg"]);
  $HTTP_POST_VARS["alt1Bg"] = $theme->getAlt1Bg();
  $theme->setAlt1FontFace($HTTP_POST_VARS["alt1FontFace"]);
  $HTTP_POST_VARS["alt1FontFace"] = $theme->getAlt1FontFace();
  $theme->setAlt1FontSize($HTTP_POST_VARS["alt1FontSize"]);
  $HTTP_POST_VARS["alt1FontSize"] = $theme->getAlt1FontSize();
  $theme->setAlt1FontColor($HTTP_POST_VARS["alt1FontColor"]);
  $HTTP_POST_VARS["alt1FontColor"] = $theme->getAlt1FontColor();
  $theme->setAlt1LinkColor($HTTP_POST_VARS["alt1LinkColor"]);
  $HTTP_POST_VARS["alt1LinkColor"] = $theme->getAlt1LinkColor();

  $theme->setAlt2Bg($HTTP_POST_VARS["alt2Bg"]);
  $HTTP_POST_VARS["alt2Bg"] = $theme->getAlt2Bg();
  $theme->setAlt2FontFace($HTTP_POST_VARS["alt2FontFace"]);
  $HTTP_POST_VARS["alt2FontFace"] = $theme->getAlt2FontFace();
  $theme->setAlt2FontSize($HTTP_POST_VARS["alt2FontSize"]);
  $HTTP_POST_VARS["alt2FontSize"] = $theme->getAlt2FontSize();
  $theme->setAlt2FontColor($HTTP_POST_VARS["alt2FontColor"]);
  $HTTP_POST_VARS["alt2FontColor"] = $theme->getAlt2FontColor();
  $theme->setAlt2LinkColor($HTTP_POST_VARS["alt2LinkColor"]);
  $HTTP_POST_VARS["alt2LinkColor"] = $theme->getAlt2LinkColor();
  $theme->setAlt2FontBold(isset($HTTP_POST_VARS["alt2FontBold"]));

  $theme->setBorderColor($HTTP_POST_VARS["borderColor"]);
  $HTTP_POST_VARS["borderColor"] = $theme->getBorderColor();
  $theme->setBorderWidth($HTTP_POST_VARS["borderWidth"]);
  $HTTP_POST_VARS["borderWidth"] = $theme->getBorderWidth();
  $theme->setTablePadding($HTTP_POST_VARS["tablePadding"]);
  $HTTP_POST_VARS["tablePadding"] = $theme->getTablePadding();

  if (!$theme->validateData()) {
    $pageErrors["themeName"] = $theme->getThemeNameError();
    $pageErrors["titleBg"] = $theme->getTitleBgError();
    $pageErrors["titleFontFace"] = $theme->getTitleFontFaceError();
    $pageErrors["titleFontSize"] = $theme->getTitleFontSizeError();
    $pageErrors["titleFontColor"] = $theme->getTitleFontColorError();
    $pageErrors["primaryBg"] = $theme->getPrimaryBgError();
    $pageErrors["primaryFontFace"] = $theme->getPrimaryFontFaceError();
    $pageErrors["primaryFontSize"] = $theme->getPrimaryFontSizeError();
    $pageErrors["primaryFontColor"] = $theme->getPrimaryFontColorError();
    $pageErrors["primaryLinkColor"] = $theme->getPrimaryLinkColorError();
    $pageErrors["primaryErrorColor"] = $theme->getPrimaryErrorColorError();
    $pageErrors["alt1Bg"] = $theme->getAlt1BgError();
    $pageErrors["alt1FontFace"] = $theme->getAlt1FontFaceError();
    $pageErrors["alt1FontSize"] = $theme->getAlt1FontSizeError();
    $pageErrors["alt1FontColor"] = $theme->getAlt1FontColorError();
    $pageErrors["alt1LinkColor"] = $theme->getAlt1LinkColorError();
    $pageErrors["alt2Bg"] = $theme->getAlt2BgError();
    $pageErrors["alt2FontFace"] = $theme->getAlt2FontFaceError();
    $pageErrors["alt2FontSize"] = $theme->getAlt2FontSizeError();
    $pageErrors["alt2FontColor"] = $theme->getAlt2FontColorError();
    $pageErrors["alt2LinkColor"] = $theme->getAlt2LinkColorError();
    $pageErrors["borderColor"] = $theme->getBorderColorError();
    $pageErrors["borderWidth"] = $theme->getBorderWidthError();
    $pageErrors["tablePadding"] = $theme->getTablePaddingError();

    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../admin/theme_edit_form.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new domain table row
  #**************************************************************************
  $themeQ = new ThemeQuery();
  $themeQ->connect();
  if ($themeQ->errorOccurred()) {
    $themeQ->close();
    displayErrorPage($themeQ);
  }
  if (!$themeQ->update($theme)) {
    $themeQ->close();
    displayErrorPage($themeQ);
  }
  $themeQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<? echo $loc->getText("adminTheme_Theme"); ?><?php echo $theme->getThemeName();?><? echo $loc->getText("adminTheme_Updated"); ?><br><br>
<a href="../admin/theme_list.php"><? echo $loc->getText("adminTheme_Return"); ?></a>

<?php require_once("../shared/footer.php"); ?>
