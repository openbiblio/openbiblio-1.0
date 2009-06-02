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
 * BiblioStatus represents a bibliography status entry.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioStatus {
  var $_bibid = 0;
  var $_barcodeNmbr = "";
  var $_barcodeNmbrError = "";
  var $_statusBeginDt = "";
  var $_statusCd = "";
  var $_mbrid = 0;
  var $_classification = "";
  var $_statusRenewDt = "";
  var $_dueBackDt = "";
  var $_materialCd = "";
  var $_title = "";
  var $_author = "";
  var $_daysLate = "";

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = true;
    if ($this->_barcodeNmbr == "") {
      $valid = false;
      $this->_barcodeNmbrError = "Barcode number is required.";
    } else {
      if (!is_numeric($this->_barcodeNmbr)) {
        $valid = false;
        $this->_barcodeNmbrError = "Barcode number must be numeric.";
      }
    }
    if (strrpos($this->_barcodeNmbr,".")) {
      $valid = false;
      $this->_barcodeNmbrError = "Barcode number must not contain a decimal point.";
    }

    # validating barcode if it has a valid format
    if ($valid) {
      $statQ = new BiblioStatusQuery();
      $statQ->connect();
      if ($statQ->errorOccurred()) {
        $valid = false;
        $statQ->close();
        $this->_barcodeNmbrError = "Could not connect to database to validate barcode.";
      } else {
        $bibid = $statQ->validateBarcode($this->_barcodeNmbr, $this->_statusCd, $this->_mbrid, $this->_classification);
        if ($bibid == false) {
          $valid = false;
          $this->_barcodeNmbrError = $statQ->getError();
        } else {
          $this->_bibid = $bibid;
        }
        $statQ->close();
      }
    }

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
  function getBarcodeNmbr() {
    return $this->_barcodeNmbr;
  }
  function getBarcodeNmbrError() {
    return $this->_barcodeNmbrError;
  }
  function getStatusBeginDt() {
    return $this->_statusBeginDt;
  }
  function getStatusCd() {
    return $this->_statusCd;
  }
  function getMbrid() {
    return $this->_mbrid;
  }
  function getStatusRenewDt() {
    return $this->_statusRenewDt;
  }
  function getDueBackDt() {
    return $this->_dueBackDt;
  }
  function getTitle() {
    return $this->_title;
  }
  function getMaterialCd() {
    return $this->_materialCd;
  }
  function getAuthor() {
    return $this->_author;
  }
  function getDaysLate() {
    return $this->_daysLate;
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
  function setBarcodeNmbr($value) {
    $this->_barcodeNmbr = trim($value);
  }
  function setStatusBeginDt($value) {
    $this->_statusBeginDt = trim($value);
  }
  function setStatusCd($value) {
    $this->_statusCd = trim($value);
  }
  function setMbrid($value) {
    $this->_mbrid = trim($value);
  }
  function setClassification($value) {
    $this->_classification = trim($value);
  }
  function setStatusRenewDt($value) {
    $this->_statusRenewDt = trim($value);
  }
  function setDueBackDt($value) {
    $this->_dueBackDt = trim($value);
  }
  function setMaterialCd($value) {
    $this->_materialCd = trim($value);
  }
  function setTitle($value) {
    $this->_title = trim($value);
  }
  function setAuthor($value) {
    $this->_author = trim($value);
  }
  function setDaysLate($value) {
    $this->_daysLate = trim($value);
  }

}

?>
