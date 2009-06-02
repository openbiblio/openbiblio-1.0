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

  require_once("../classes/Localize.php");

/******************************************************************************
 * UsmarcField represents a library bibliography subfield.  Contains business rules for
 * subfield data validation.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioField {
  var $_bibid = "";
  var $_fieldid = "";
  var $_tag = "";
  var $_tagError = "";
  var $_ind1Cd = "";
  var $_ind2Cd = "";
  var $_subfieldCd = "";
  var $_subfieldCdError = "";
  var $_fieldData = "";
  var $_fieldDataError = "";
  var $_isRequired = false;
  var $_isRepeatable = false;

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $loc = new Localize(OBIB_LOCALE,"classes");
    $valid = true;
    if (($this->_isRequired ) and ($this->_fieldData == "")) {
      $valid = false;
      $this->_fieldDataError = $loc->getText("biblioFieldError1");
    }
    if ($this->_tag == "") {
      $valid = false;
      $this->_tagError = $loc->getText("biblioFieldError1");
    } else if (!is_numeric($this->_tag)) {
      $valid = false;
      $this->_tagError = $loc->getText("biblioFieldError2");
    }
    if ($this->_subfieldCd == "") {
      $valid = false;
      $this->_subfieldCdError = $loc->getText("biblioFieldError1");
    }
    unset($loc);
    return $valid;
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getBibid() {
    return $this->_bibid;
  }
  function getFieldid() {
    return $this->_fieldid;
  }
  function getTag() {
    return $this->_tag;
  }
  function getTagError() {
    return $this->_tagError;
  }
  function getInd1Cd() {
    return $this->_ind1Cd;
  }
  function getInd2Cd() {
    return $this->_ind2Cd;
  }
  function getSubfieldCd() {
    return $this->_subfieldCd;
  }
  function getSubfieldCdError() {
    return $this->_subfieldCdError;
  }
  function getFieldData() {
    return $this->_fieldData;
  }
  function getFieldDataError() {
    return $this->_fieldDataError;
  }
  function isRequired() {
    if ($this->_isRequired) {
      return true;
    } else {
      return false;
    }
  }
  function isRepeatable() {
    if ($this->_isRepeatable) {
      return true;
    } else {
      return false;
    }
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setBibid($value) {
    $this->_bibid = trim($value);
  }
  function setFieldid($value) {
    $this->_fieldid = trim($value);
  }
  function setTag($value) {
    $this->_tag = trim($value);
  }
  function setInd1Cd($value) {
    $this->_ind1Cd = substr(trim($value),0,1);
  }
  function setInd2Cd($value) {
    $this->_ind2Cd = substr(trim($value),0,1);
  }
  function setSubfieldCd($value) {
    $this->_subfieldCd = substr(trim($value),0,1);
  }
  function setFieldData($value) {
    $this->_fieldData = trim($value);
  }
  function setFieldDataError($value) {
    $this->_fieldDataError = trim($value);
  }
  function setIsRequired($value) {
    if ($value) {
      $this->_isRequired = true;
    } else {
      $this->_isRequired = false;
    }
  }
  function setIsRepeatable($value) {
    if ($value) {
      $this->_isRepeatable = true;
    } else {
      $this->_isRepeatable = false;
    }
  }
}

?>
