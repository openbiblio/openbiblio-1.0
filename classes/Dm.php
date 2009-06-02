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
 * Dm represents a domain table row.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class Dm {
  var $_code = "";
  var $_description = "";
  var $_descriptionError = "";
  var $_defaultFlg = "";
  var $_daysDueBack = "0";
  var $_daysDueBackError = "";
  var $_dailyLateFee = "0";
  var $_dailyLateFeeError = "";
  var $_adultCheckoutLimit = "0";
  var $_adultCheckoutLimitError = "";
  var $_juvenileCheckoutLimit = "0";
  var $_juvenileCheckoutLimitError = "";
  var $_imageFile = "";
  var $_count = "0";

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = true;
    if ($this->_description == "") {
      $valid = false;
      $this->_descriptionError = "Description is required.";
    }
    if (!is_numeric($this->_daysDueBack)) {
      $valid = false;
      $this->_daysDueBackError = "Days due back must be numeric.";
    } elseif ($this->_daysDueBack < 0) {
      $valid = false;
      $this->_daysDueBackError = "Days due back can not be less than zero.";
    }
    if (!is_numeric($this->_dailyLateFee)) {
      $valid = false;
      $this->_dailyLateFeeError = "Daily late fee must be numeric.";
    } elseif ($this->_dailyLateFee < 0) {
      $valid = false;
      $this->_dailyLateFeeError = "Daily late fee can not be less than zero.";
    }
    if (!is_numeric($this->_adultCheckoutLimit)) {
      $valid = false;
      $this->_adultCheckoutLimitError = "Adult checkout limit must be numeric.";
    } elseif ($this->_adultCheckoutLimit < 0) {
      $valid = false;
      $this->_adultCheckoutLimitError = "Adult checkout limit can not be less than zero.";
    }
    if (!is_numeric($this->_juvenileCheckoutLimit)) {
      $valid = false;
      $this->_juvenileCheckoutLimitError = "Juvenile checkout limit must be numeric.";
    } elseif ($this->_juvenileCheckoutLimit < 0) {
      $valid = false;
      $this->_juvenileCheckoutLimitError = "Juvenile checkout limit can not be less than zero.";
    }
    return $valid;
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getCode() {
    return $this->_code;
  }
  function getDescription() {
    return $this->_description;
  }
  function getDescriptionError() {
    return $this->_descriptionError;
  }
  function getDefaultFlg() {
    return $this->_defaultFlg;
  }
  function getDaysDueBack() {
    return $this->_daysDueBack;
  }
  function getDaysDueBackError() {
    return $this->_daysDueBackError;
  }
  function getDailyLateFee() {
    return $this->_dailyLateFee;
  }
  function getDailyLateFeeError() {
    return $this->_dailyLateFeeError;
  }
  function getAdultCheckoutLimit() {
    return $this->_adultCheckoutLimit;
  }
  function getAdultCheckoutLimitError() {
    return $this->_adultCheckoutLimitError;
  }
  function getJuvenileCheckoutLimit() {
    return $this->_juvenileCheckoutLimit;
  }
  function getJuvenileCheckoutLimitError() {
    return $this->_juvenileCheckoutLimitError;
  }
  function getImageFile() {
    return $this->_imageFile;
  }
  function getCount() {
    return $this->_count;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setCode($value) {
    $this->_code = trim($value);
  }
  function setDescription($value) {
    $this->_description = trim($value);
  }
  function setDescriptionError($value) {
    $this->_descriptionError = trim($value);
  }
  function setDefaultFlg($value) {
    $this->_defaultFlg = trim($value);
  }
  function setDaysDueBack($value) {
    if (trim($value) == "") {
      $this->_daysDueBack = "0";
    } else {
      $this->_daysDueBack = trim($value);
    }
  }
  function setDaysDueBackError($value) {
    $this->_daysDueBackError = trim($value);
  }
  function setDailyLateFee($value) {
    if (trim($value) == "") {
      $this->_dailyLateFee = "0";
    } else {
      $this->_dailyLateFee = trim($value);
    }
  }
  function setDailyLateFeeError($value) {
    $this->_dailyLateFeeError = trim($value);
  }
  function setAdultCheckoutLimit($value) {
    if (trim($value) == "") {
      $this->_adultCheckoutLimit = "0";
    } else {
      $this->_adultCheckoutLimit = trim($value);
    }
  }
  function setAdultCheckoutLimitError($value) {
    $this->_adultCheckoutLimitError = trim($value);
  }
  function setJuvenileCheckoutLimit($value) {
    if (trim($value) == "") {
      $this->_juvenileCheckoutLimit = "0";
    } else {
      $this->_juvenileCheckoutLimit = trim($value);
    }
  }
  function setJuvenileCheckoutLimitError($value) {
    $this->_juvenileCheckoutLimitError = trim($value);
  }
  function setImageFile($value) {
    $this->_imageFile = trim($value);
  }
  function setCount($value) {
    if (trim($value) == "") {
      $this->_count = "0";
    } else {
      $this->_count = trim($value);
    }
  }


}

?>
