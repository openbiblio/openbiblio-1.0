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
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Theme.php");
  require_once("../classes/ThemeQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  
  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../admin/theme_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $theme = new Theme();
  $theme->setThemeid($_POST["themeid"]);
  $_POST["themeid"] = $theme->getThemeid();
  $theme->setThemeName($_POST["themeName"]);
  $_POST["themeName"] = $theme->getThemeName();

  $theme->setTitleBg($_POST["titleBg"]);
  $_POST["titleBg"] = $theme->getTitleBg();
  $theme->setTitleFontFace($_POST["titleFontFace"]);
  $_POST["titleFontFace"] = $theme->getTitleFontFace();
  $theme->setTitleFontSize($_POST["titleFontSize"]);
  $_POST["titleFontSize"] = $theme->getTitleFontSize();
  $theme->setTitleFontBold(isset($_POST["titleFontBold"]));
  $theme->setTitleFontColor($_POST["titleFontColor"]);
  $_POST["titleFontColor"] = $theme->getTitleFontColor();
  $theme->setTitleAlign($_POST["titleAlign"]);

  $theme->setPrimaryBg($_POST["primaryBg"]);
  $_POST["primaryBg"] = $theme->getPrimaryBg();
  $theme->setPrimaryFontFace($_POST["primaryFontFace"]);
  $_POST["primaryFontFace"] = $theme->getPrimaryFontFace();
  $theme->setPrimaryFontSize($_POST["primaryFontSize"]);
  $_POST["primaryFontSize"] = $theme->getPrimaryFontSize();
  $theme->setPrimaryFontColor($_POST["primaryFontColor"]);
  $_POST["primaryFontColor"] = $theme->getPrimaryFontColor();
  $theme->setPrimaryLinkColor($_POST["primaryLinkColor"]);
  $_POST["primaryLinkColor"] = $theme->getPrimaryLinkColor();
  $theme->setPrimaryErrorColor($_POST["primaryErrorColor"]);
  $_POST["primaryErrorColor"] = $theme->getPrimaryErrorColor();

  $theme->setAlt1Bg($_POST["alt1Bg"]);
  $_POST["alt1Bg"] = $theme->getAlt1Bg();
  $theme->setAlt1FontFace($_POST["alt1FontFace"]);
  $_POST["alt1FontFace"] = $theme->getAlt1FontFace();
  $theme->setAlt1FontSize($_POST["alt1FontSize"]);
  $_POST["alt1FontSize"] = $theme->getAlt1FontSize();
  $theme->setAlt1FontColor($_POST["alt1FontColor"]);
  $_POST["alt1FontColor"] = $theme->getAlt1FontColor();
  $theme->setAlt1LinkColor($_POST["alt1LinkColor"]);
  $_POST["alt1LinkColor"] = $theme->getAlt1LinkColor();

  $theme->setAlt2Bg($_POST["alt2Bg"]);
  $_POST["alt2Bg"] = $theme->getAlt2Bg();
  $theme->setAlt2FontFace($_POST["alt2FontFace"]);
  $_POST["alt2FontFace"] = $theme->getAlt2FontFace();
  $theme->setAlt2FontSize($_POST["alt2FontSize"]);
  $_POST["alt2FontSize"] = $theme->getAlt2FontSize();
  $theme->setAlt2FontColor($_POST["alt2FontColor"]);
  $_POST["alt2FontColor"] = $theme->getAlt2FontColor();
  $theme->setAlt2LinkColor($_POST["alt2LinkColor"]);
  $_POST["alt2LinkColor"] = $theme->getAlt2LinkColor();
  $theme->setAlt2FontBold(isset($_POST["alt2FontBold"]));

  $theme->setBorderColor($_POST["borderColor"]);
  $_POST["borderColor"] = $theme->getBorderColor();
  $theme->setBorderWidth($_POST["borderWidth"]);
  $_POST["borderWidth"] = $theme->getBorderWidth();
  $theme->setTablePadding($_POST["tablePadding"]);
  $_POST["tablePadding"] = $theme->getTablePadding();

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

    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
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
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<? echo $loc->getText("adminTheme_Theme"); ?><?php echo $theme->getThemeName();?><? echo $loc->getText("adminTheme_Updated"); ?><br><br>
<a href="../admin/theme_list.php"><? echo $loc->getText("adminTheme_Return"); ?></a>

<?php require_once("../shared/footer.php"); ?>
