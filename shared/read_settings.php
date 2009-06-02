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
   * Reading settings from database
   ****************************************************************************
   */
  include_once("../classes/Settings.php");
  include_once("../classes/SettingsQuery.php");
  require_once("../classes/Theme.php");
  require_once("../classes/ThemeQuery.php");
  include_once("../functions/errorFuncs.php");


  /****************************************************************************
   * Reading general settings
   ****************************************************************************
   */
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
   * Reading theme settings
   ****************************************************************************
   */
  $themeQ = new ThemeQuery();
  $themeQ->connect();
  if ($themeQ->errorOccurred()) {
    $themeQ->close();
    displayErrorPage($themeQ);
  }
  $themeQ->execSelect($set->getThemeid());
  if ($themeQ->errorOccurred()) {
    $themeQ->close();
    displayErrorPage($themeQ);
  }
  $theme = $themeQ->fetchTheme();
  $themeQ->close();

  /****************************************************************************
   * general settings constants
   ****************************************************************************
   */
  define("OBIB_LIBRARY_NAME",$set->getLibraryName());
  define("OBIB_LIBRARY_HOURS",$set->getLibraryHours());
  define("OBIB_LIBRARY_PHONE",$set->getLibraryPhone());
  define("OBIB_LIBRARY_URL",$set->getLibraryUrl());
  define("OBIB_OPAC_URL",$set->getOpacUrl());
  define("OBIB_SESSION_TIMEOUT",$set->getSessionTimeout());
  define("OBIB_ITEMS_PER_PAGE",$set->getItemsPerPage());
  define("OBIB_VERSION",$set->getVersion());
  define("OBIB_THEMEID",$set->getThemeid());
  define("OBIB_LOCALE","en");
  define("OBIB_LIBRARY_USE_IMAGE",$set->isUseImageSet());
  define("OBIB_LIBRARY_IMAGE_URL",$set->getLibraryImageUrl());

  /****************************************************************************
   * theme related constants.
   ****************************************************************************
   */
  define("OBIB_TITLE_BG",$theme->getTitleBg());
  define("OBIB_TITLE_FONT_FACE",$theme->getTitleFontFace());
  define("OBIB_TITLE_FONT_SIZE",$theme->getTitleFontSize());
  define("OBIB_TITLE_FONT_BOLD",$theme->getTitleFontBold());
  define("OBIB_TITLE_ALIGN",$theme->getTitleAlign());
  define("OBIB_TITLE_FONT_COLOR",$theme->getTitleFontColor());

  define("OBIB_PRIMARY_BG",$theme->getPrimaryBg());
  define("OBIB_PRIMARY_FONT_FACE",$theme->getPrimaryFontFace());
  define("OBIB_PRIMARY_FONT_SIZE",$theme->getPrimaryFontSize());
  define("OBIB_PRIMARY_FONT_COLOR",$theme->getPrimaryFontColor());
  define("OBIB_PRIMARY_LINK_COLOR",$theme->getPrimaryLinkColor());
  define("OBIB_PRIMARY_ERROR_COLOR",$theme->getPrimaryErrorColor());

  define("OBIB_ALT1_BG",$theme->getAlt1Bg());
  define("OBIB_ALT1_FONT_FACE",$theme->getAlt1FontFace());
  define("OBIB_ALT1_FONT_SIZE",$theme->getAlt1FontSize());
  define("OBIB_ALT1_FONT_COLOR",$theme->getAlt1FontColor());
  define("OBIB_ALT1_LINK_COLOR",$theme->getAlt1LinkColor());

  define("OBIB_ALT2_BG",$theme->getAlt2Bg());
  define("OBIB_ALT2_FONT_FACE",$theme->getAlt2FontFace());
  define("OBIB_ALT2_FONT_SIZE",$theme->getAlt2FontSize());
  define("OBIB_ALT2_FONT_COLOR",$theme->getAlt2FontColor());
  define("OBIB_ALT2_LINK_COLOR",$theme->getAlt2LinkColor());
  define("OBIB_ALT2_FONT_BOLD",$theme->getAlt2FontBold());

  define("OBIB_BORDER_COLOR",$theme->getBorderColor());
  define("OBIB_BORDER_WIDTH",$theme->getBorderWidth());
  define("OBIB_PADDING",$theme->getTablePadding());

  /****************************************************************************
   *  System constants
   ****************************************************************************
   */
  define("OBIB_DEMO_FLG",false);

  #****************************************************************************
  #*  Making session user info available on all pages.
  #****************************************************************************
  session_start();
?>
