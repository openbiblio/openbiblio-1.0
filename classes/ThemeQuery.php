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

require_once("../shared/global_constants.php");
require_once("../classes/Query.php");

/******************************************************************************
 * ThemeQuery data access component for library themes
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class ThemeQuery extends Query {
  /****************************************************************************
   * Executes a query
   * @param string $themeid themeid of theme to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($themeid="") {
    if ($themeid == "") {
      $sql = "select * from theme order by theme_name";
    } else {
      $sql = "select * from theme where themeid=".$themeid." order by theme_name";
    }
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing theme information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Theme object.
   * @return Theme returns theme or false if no more themes to fetch
   * @access public
   ****************************************************************************
   */
  function fetchTheme() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }
    $theme = new Theme();
    $theme->setThemeid($array["themeid"]);
    $theme->setThemeName($array["theme_name"]);
    $theme->setTitleBg($array["title_bg"]);
    $theme->setTitleFontFace($array["title_font_face"]);
    $theme->setTitleFontSize($array["title_font_size"]);
    if ($array["title_font_bold"] == "Y") {
      $theme->setTitleFontBold(true);
    } else {
      $theme->setTitleFontBold(false);
    }
    $theme->setTitleFontColor($array["title_font_color"]);
    $theme->setTitleAlign($array["title_align"]);
    $theme->setPrimaryBg($array["primary_bg"]);
    $theme->setPrimaryFontFace($array["primary_font_face"]);
    $theme->setPrimaryFontSize($array["primary_font_size"]);
    $theme->setPrimaryFontColor($array["primary_font_color"]);
    $theme->setPrimaryLinkColor($array["primary_link_color"]);
    $theme->setPrimaryErrorColor($array["primary_error_color"]);
    $theme->setAlt1Bg($array["alt1_bg"]);
    $theme->setAlt1FontFace($array["alt1_font_face"]);
    $theme->setAlt1FontSize($array["alt1_font_size"]);
    $theme->setAlt1FontColor($array["alt1_font_color"]);
    $theme->setAlt1LinkColor($array["alt1_link_color"]);
    $theme->setAlt2Bg($array["alt2_bg"]);
    $theme->setAlt2FontFace($array["alt2_font_face"]);
    $theme->setAlt2FontSize($array["alt2_font_size"]);
    $theme->setAlt2FontColor($array["alt2_font_color"]);
    $theme->setAlt2LinkColor($array["alt2_link_color"]);
    if ($array["alt2_font_bold"] == "Y") {
      $theme->setAlt2FontBold(true);
    } else {
      $theme->setAlt2FontBold(false);
    }
    $theme->setBorderColor($array["border_color"]);
    $theme->setBorderWidth($array["border_width"]);
    $theme->setTablePadding($array["table_padding"]);

    return $theme;
  }

  /****************************************************************************
   * Inserts a new theme into the theme table.
   * @param Theme $theme theme to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($theme) {
    $sql = "insert into theme values (null, ";
    $sql = $sql."'".$theme->getThemeName()."', ";
    $sql = $sql."'".$theme->getTitleBg()."', ";
    $sql = $sql."'".$theme->getTitleFontFace()."', ";
    $sql = $sql.$theme->getTitleFontSize().", ";
    if ($theme->getTitleFontBold()) {
      $sql = $sql."'Y', ";
    } else {
      $sql = $sql."'N', ";
    }
    $sql = $sql."'".$theme->getTitleFontColor()."', ";
    $sql = $sql."'".$theme->getTitleAlign()."', ";
    $sql = $sql."'".$theme->getPrimaryBg()."', ";
    $sql = $sql."'".$theme->getPrimaryFontFace()."', ";
    $sql = $sql.$theme->getPrimaryFontSize().", ";
    $sql = $sql."'".$theme->getPrimaryFontColor()."', ";
    $sql = $sql."'".$theme->getPrimaryLinkColor()."', ";
    $sql = $sql."'".$theme->getPrimaryErrorColor()."', ";
    $sql = $sql."'".$theme->getAlt1Bg()."', ";
    $sql = $sql."'".$theme->getAlt1FontFace()."', ";
    $sql = $sql.$theme->getAlt1FontSize().", ";
    $sql = $sql."'".$theme->getAlt1FontColor()."', ";
    $sql = $sql."'".$theme->getAlt1LinkColor()."', ";
    $sql = $sql."'".$theme->getAlt2Bg()."', ";
    $sql = $sql."'".$theme->getAlt2FontFace()."', ";
    $sql = $sql.$theme->getAlt2FontSize().", ";
    $sql = $sql."'".$theme->getAlt2FontColor()."', ";
    $sql = $sql."'".$theme->getAlt2LinkColor()."', ";
    if ($theme->getAlt2FontBold()) {
      $sql = $sql."'Y', ";
    } else {
      $sql = $sql."'N', ";
    }
    $sql = $sql."'".$theme->getBorderColor()."', ";
    $sql = $sql.$theme->getBorderWidth().", ";
    $sql = $sql.$theme->getTablePadding().")";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error inserting new library look and feel theme.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Update a theme in the theme table.
   * @param Theme $theme theme to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($theme) {
    $sql = "update theme set ";
    $sql = $sql."theme_name='".$theme->getThemeName()."', ";

    $sql = $sql."title_bg='".$theme->getTitleBg()."', ";
    $sql = $sql."title_font_face='".$theme->getTitleFontFace()."', ";
    $sql = $sql."title_font_size=".$theme->getTitleFontSize().", ";
    if ($theme->getTitleFontBold()) {
      $sql = $sql."title_font_bold='Y', ";
    } else {
      $sql = $sql."title_font_bold='N', ";
    }
    $sql = $sql."title_font_color='".$theme->getTitleFontColor()."', ";
    $sql = $sql."title_align='".$theme->getTitleAlign()."', ";

    $sql = $sql."primary_bg='".$theme->getPrimaryBg()."', ";
    $sql = $sql."primary_font_face='".$theme->getPrimaryFontFace()."', ";
    $sql = $sql."primary_font_size=".$theme->getPrimaryFontSize().", ";
    $sql = $sql."primary_font_color='".$theme->getPrimaryFontColor()."', ";
    $sql = $sql."primary_link_color='".$theme->getPrimaryLinkColor()."', ";
    $sql = $sql."primary_error_color='".$theme->getPrimaryErrorColor()."', ";

    $sql = $sql."alt1_bg='".$theme->getAlt1Bg()."', ";
    $sql = $sql."alt1_font_face='".$theme->getAlt1FontFace()."', ";
    $sql = $sql."alt1_font_size=".$theme->getAlt1FontSize().", ";
    $sql = $sql."alt1_font_color='".$theme->getAlt1FontColor()."', ";
    $sql = $sql."alt1_link_color='".$theme->getAlt1LinkColor()."', ";

    $sql = $sql."alt2_bg='".$theme->getAlt2Bg()."', ";
    $sql = $sql."alt2_font_face='".$theme->getAlt2FontFace()."', ";
    $sql = $sql."alt2_font_size=".$theme->getAlt2FontSize().", ";
    $sql = $sql."alt2_font_color='".$theme->getAlt2FontColor()."', ";
    $sql = $sql."alt2_link_color='".$theme->getAlt2LinkColor()."', ";
    if ($theme->getAlt2FontBold()) {
      $sql = $sql."alt2_font_bold='Y', ";
    } else {
      $sql = $sql."alt2_font_bold='N', ";
    }

    $sql = $sql."border_color='".$theme->getBorderColor()."', ";
    $sql = $sql."border_width='".$theme->getBorderWidth()."', ";
    $sql = $sql."table_padding='".$theme->getTablePadding()."' ";
    $sql = $sql." where themeid = ".$theme->getThemeid();
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating library look and feel theme.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes a theme from the theme table.
   * @param string $themeid themeid of theme to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($themeid) {
    $sql = "delete from theme where themeid = ".$themeid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting library look and feel theme.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
