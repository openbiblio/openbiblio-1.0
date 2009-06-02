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

/******************************************************************************
 * Settings represents the library settings.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class Settings {
  var $_libraryName = "";
  var $_libraryImageUrl = "";
  var $_isUseImageSet = false;
  var $_libraryHours = "";
  var $_libraryPhone = "";
  var $_libraryUrl = "";
  var $_opacUrl = "";
  var $_sessionTimeout = 0;
  var $_sessionTimeoutError = "";
  var $_itemsPerPage = 0;
  var $_itemsPerPageError = "";
  var $_version = "";
  var $_themeid = 0;

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = true;
    if (!is_numeric($this->_sessionTimeout)) {
      $valid = false;
      $this->_sessionTimeoutError = "Session timeout must be numeric.";
    } elseif (strrpos($this->_sessionTimeout,".")) {
      $valid = false;
      $this->_sessionTimeoutError = "Session timeout must not contain a decimal point.";
    } elseif ($this->_sessionTimeout <= 0) {
      $valid = false;
      $this->_sessionTimeoutError = "Session timeout must be greater than zero.";
    }

    if (!is_numeric($this->_itemsPerPage)) {
      $valid = false;
      $this->_itemsPerPageError = "Items per page must be numeric.";
    } elseif (strrpos($this->_itemsPerPage,".")) {
      $valid = false;
      $this->_itemsPerPageError = "Items per page must not contain a decimal point.";
    } elseif ($this->_itemsPerPage <= 0) {
      $valid = false;
      $this->_itemsPerPageError = "Items per page must be greater than zero.";
    }

    return $valid;
  }

  /****************************************************************************
   * getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getLibraryName() {
    return $this->_libraryName;
  }
  function getLibraryImageUrl() {
    return $this->_libraryImageUrl;
  }
  function isUseImageSet() {
    return $this->_isUseImageSet;
  }
  function getLibraryHours() {
    return $this->_libraryHours;
  }
  function getLibraryPhone() {
    return $this->_libraryPhone;
  }
  function getLibraryUrl() {
    return $this->_libraryUrl;
  }
  function getOpacUrl() {
    return $this->_opacUrl;
  }
  function getSessionTimeout() {
    return $this->_sessionTimeout;
  }
  function getSessionTimeoutError() {
    return $this->_sessionTimeoutError;
  }
  function getItemsPerPage() {
    return $this->_itemsPerPage;
  }
  function getItemsPerPageError() {
    return $this->_itemsPerPageError;
  }
  function getVersion() {
    return $this->_version;
  }
  function getThemeid() {
    return $this->_themeid;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setLibraryName($value) {
    $this->_libraryName = trim($value);
  }
  function setLibraryImageUrl($value) {
    $this->_libraryImageUrl = trim($value);
  }
  function setUseImageFlg($value) {
    if ($value) {
      $this->_isUseImageSet = true;
    } else {
      $this->_isUseImageSet = false;
    }
  }
  function setLibraryHours($value) {
    $this->_libraryHours = trim($value);
  }
  function setLibraryPhone($value) {
    $this->_libraryPhone = trim($value);
  }
  function setLibraryUrl($value) {
    $this->_libraryUrl = trim($value);
  }
  function setOpacUrl($value) {
    $this->_opacUrl = trim($value);
  }
  function setSessionTimeout($value) {
    $temp = trim($value);
    if ($temp == "") {
      $this->_sessionTimeout = 0;
    } else {
      $this->_sessionTimeout = $temp;
    }
  }
  function setSessionTimeoutError($value) {
    $this->_sessionTimeoutError = trim($value);
  }
  function setItemsPerPage($value) {
    $temp = trim($value);
    if ($temp == "") {
      $this->_itemsPerPage = 0;
    } else {
      $this->_itemsPerPage = $temp;
    }
  }
  function setItemsPerPageError($value) {
    $this->_itemsPerPageError = trim($value);
  }
  function setVersion($value) {
    $this->_version = trim($value);
  }
  function setThemeid($value) {
    $temp = trim($value);
    if ($temp == "") {
      $this->_themeid = 0;
    } else {
      $this->_themeid = $temp;
    }
  }

}

?>
