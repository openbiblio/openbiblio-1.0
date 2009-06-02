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
 * Staff represents a library staff member.  Contains business rules for
 * staff member data validation.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class Biblio {
  var $_bibid = "";
  var $_barcodeNmbr = "";
  var $_barcodeNmbrError = "";
  var $_createDt = "";
  var $_lastUpdatedDt = "";
  var $_materialCd = "";
  var $_collectionCd = "";
  var $_title = "";
  var $_titleError = "";
  var $_subtitle = "";
  var $_author = "";
  var $_addAuthor = "";
  var $_edition = "";
  var $_summary = "";
  var $_callNmbr = "";
  var $_callNmbrError = "";
  var $_lccnNmbr = "";
  var $_isbnNmbr = "";
  var $_lcCallNmbr = "";
  var $_lcItemNmbr = "";
  var $_udcNmbr = "";
  var $_udcEdNmbr = "";
  var $_publisher = "";
  var $_publicationDt = "";
  var $_publicationLoc = "";
  var $_pages = "";
  var $_physicalDetails = "";
  var $_dimensions = "";
  var $_accompanying = "";
  var $_price = "";
  var $_priceError = "";
  var $_statusCd = "";
  var $_statusMbrid = "";
  var $_dueBackDt = "";
  var $_holdMbrid = "";

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
    if ($this->_title == "") {
      $valid = false;
      $this->_titleError = "Title is required.";
    }
    if ($this->_callNmbr == "") {
      $valid = false;
      $this->_callNmbrError = "Call number is required.";
    }
    if (!is_numeric($this->_price)) {
      $valid = false;
      $this->_priceError = "Price must be numeric.";
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
  function getCreateDt() {
    return $this->_createDt;
  }
  function getLastUpdatedDt() {
    return $this->_lastUpdatedDt;
  }
  function getMaterialCd() {
    return $this->_materialCd;
  }
  function getCollectionCd() {
    return $this->_collectionCd;
  }
  function getTitle() {
    return $this->_title;
  }
  function getTitleError() {
    return $this->_titleError;
  }
  function getSubtitle() {
    return $this->_subtitle;
  }
  function getAuthor() {
    return $this->_author;
  }
  function getAddAuthor() {
    return $this->_addAuthor;
  }
  function getEdition() {
    return $this->_edition;
  }
  function getSummary() {
    return $this->_summary;
  }
  function getCallNmbr() {
    return $this->_callNmbr;
  }
  function getCallNmbrError() {
    return $this->_callNmbrError;
  }
  function getLccnNmbr() {
    return $this->_lccnNmbr;
  }
  function getIsbnNmbr() {
    return $this->_isbnNmbr;
  }
  function getLcCallNmbr() {
    return $this->_lcCallNmbr;
  }
  function getLcItemNmbr() {
    return $this->_lcItemNmbr;
  }
  function getUdcNmbr() {
    return $this->_udcNmbr;
  }
  function getUdcEdNmbr() {
    return $this->_udcEdNmbr;
  }
  function getPublisher() {
    return $this->_publisher;
  }
  function getPublicationDt() {
    return $this->_publicationDt;
  }
  function getPublicationLoc() {
    return $this->_publicationLoc;
  }
  function getPages() {
    return $this->_pages;
  }
  function getPhysicalDetails() {
    return $this->_physicalDetails;
  }
  function getDimensions() {
    return $this->_dimensions;
  }
  function getAccompanying() {
    return $this->_accompanying;
  }
  function getPrice() {
    return $this->_price;
  }
  function getPriceError() {
    return $this->_priceError;
  }
  function getStatusCd() {
    return $this->_statusCd;
  }
  function getStatusMbrid() {
    return $this->_statusMbrid;
  }
  function getDueBackDt() {
    return $this->_dueBackDt;
  }
  function getHoldMbrid() {
    return $this->_holdMbrid;
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
  function setCreateDt($value) {
    $this->_createDt = trim($value);
  }
  function setLastUpdatedDt($value) {
    $this->_lastUpdatedDt = trim($value);
  }
  function setMaterialCd($value) {
    $this->_materialCd = trim($value);
  }
  function setCollectionCd($value) {
    $this->_collectionCd = trim($value);
  }
  function setTitle($value) {
    $this->_title = trim($value);
  }
  function setSubtitle($value) {
    $this->_subtitle = trim($value);
  }
  function setAuthor($value) {
    $this->_author = trim($value);
  }
  function setAddAuthor($value) {
    $this->_addAuthor = trim($value);
  }
  function setEdition($value) {
    $this->_edition = trim($value);
  }
  function setSummary($value) {
    $this->_summary = trim($value);
  }
  function setCallNmbr($value) {
    $this->_callNmbr = trim($value);
  }
  function setCallNmbrError($value) {
    $this->_callNmbrError = trim($value);
  }
  function setLccnNmbr($value) {
    $this->_lccnNmbr = trim($value);
  }
  function setIsbnNmbr($value) {
    $this->_isbnNmbr = trim($value);
  }
  function setLcCallNmbr($value) {
    $this->_lcCallNmbr = trim($value);
  }
  function setLcItemNmbr($value) {
    $this->_lcItemNmbr = trim($value);
  }
  function setUdcNmbr($value) {
    $this->_udcNmbr = trim($value);
  }
  function setUdcEdNmbr($value) {
    $this->_udcEdNmbr = trim($value);
  }
  function setPublisher($value) {
    $this->_publisher = trim($value);
  }
  function setPublicationDt($value) {
    $this->_publicationDt = trim($value);
  }
  function setPublicationLoc($value) {
    $this->_publicationLoc = trim($value);
  }
  function setPages($value) {
    $this->_pages = trim($value);
  }
  function setPhysicalDetails($value) {
    $this->_physicalDetails = trim($value);
  }
  function setDimensions($value) {
    $this->_dimensions = trim($value);
  }
  function setAccompanying($value) {
    $this->_accompanying = trim($value);
  }
  function setPrice($value) {
    if (is_numeric($value)) {
      $this->_price = number_format($value,2,".","");
    } else {
      if (trim($value) == "") {
        $this->_price = "0";
      } else {
        $this->_price = trim($value);
      }
    }
  }
  function setPriceError($value) {
    $this->_priceError = trim($value);
  }
  function setStatusCd($value) {
    if ($value == "") {
      $value = "in";
    }
    $this->_statusCd = trim($value);
  }
  function setStatusMbrid($value) {
    $this->_statusMbrid = trim($value);
  }
  function setDueBackDt($value) {
    $this->_dueBackDt = trim($value);
  }
  function setHoldMbrid($value) {
    $this->_holdMbrid = trim($value);
  }
}

?>
