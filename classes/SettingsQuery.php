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
 * SettingsQuery data access component for settings table
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class SettingsQuery extends Query {

  /****************************************************************************
   * Executes a query
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect() {
    $sql = "select * from settings";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing library settings information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Settings object.
   * @return Settings returns settings object or false if no more rows to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }
    $set = new Settings();
    $set->setLibraryName($array["library_name"]);
    $set->setLibraryImageUrl($array["library_image_url"]);
    if ($array["use_image_flg"] == 'Y') {
      $set->setUseImageFlg(true);
    } else {
      $set->setUseImageFlg(false);
    }
    $set->setLibraryHours($array["library_hours"]);
    $set->setLibraryPhone($array["library_phone"]);
    $set->setLibraryUrl($array["library_url"]);
    $set->setOpacUrl($array["opac_url"]);
    $set->setSessionTimeout($array["session_timeout"]);
    $set->setItemsPerPage($array["items_per_page"]);
    $set->setVersion($array["version"]);
    $set->setThemeid($array["themeid"]);
    $set->setPurgeHistoryAfterMonths($array["purge_history_after_months"]);
    if ($array["block_checkouts_when_fines_due"] == 'Y') {
      $set->setBlockCheckoutsWhenFinesDue(true);
    } else {
      $set->setBlockCheckoutsWhenFinesDue(false);
    }
    $set->setLocale($array["locale"]);
    $set->setCharset($array["charset"]);
    $set->setHtmlLangAttr($array["html_lang_attr"]);

    return $set;
  }

  /****************************************************************************
   * Update a the row in the settings table.
   * @param Settings $set settings object to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($set) {
    $sql = "update settings set ";
    $sql = $sql."library_name='".$set->getLibraryName()."', ";
    $sql = $sql."library_image_url='".$set->getLibraryImageUrl()."', ";
    if ($set->isUseImageSet()) {
      $sql = $sql."use_image_flg='Y', ";
    } else {
      $sql = $sql."use_image_flg='N', ";
    }
    $sql = $sql."library_hours='".$set->getLibraryHours()."', ";
    $sql = $sql."library_phone='".$set->getLibraryPhone()."', ";
    $sql = $sql."library_url='".$set->getLibraryUrl()."', ";
    $sql = $sql."opac_url='".$set->getOpacUrl()."', ";
    $sql = $sql."session_timeout=".$set->getSessionTimeout().", ";
    $sql = $sql."items_per_page=".$set->getItemsPerPage().", ";
    $sql = $sql."purge_history_after_months=".$set->getPurgeHistoryAfterMonths().", ";
    if ($set->isBlockCheckoutsWhenFinesDue()) {
      $sql = $sql."block_checkouts_when_fines_due='Y', ";
    } else {
      $sql = $sql."block_checkouts_when_fines_due='N', ";
    }
    $sql = $sql."locale='".$set->getLocale()."', ";
    $sql = $sql."charset='".$set->getCharset()."', ";
    $sql = $sql."html_lang_attr='".$set->getHtmlLangAttr()."'";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating library settings information";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Update a the row in the settings table.
   * @param Settings $set settings object to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function updateTheme($themeId) {
    $sql = "update settings set ";
    $sql = $sql."themeid=".$themeId;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating library theme in use";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  function getPurgeHistoryAfterMonths($connection) {
    $sql = "select purge_history_after_months from settings";
    $result = $connection->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating library theme in use";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $connection->fetchRow();
    if ($array == false) {
      return false;
    }
    return $array["purge_history_after_months"];
  }
}

?>
