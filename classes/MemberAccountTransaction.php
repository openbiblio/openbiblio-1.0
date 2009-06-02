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
 * MemberAccountTransaction represents a member account transaction
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class MemberAccountTransaction {
  var $_mbrid = "";
  var $_transid = "";
  var $_createDt = "";
  var $_createUserid = "";
  var $_transactionTypeCd = "";
  var $_transactionTypeDesc = "";
  var $_amount = "";
  var $_amountError = "";
  var $_description = "";
  var $_descriptionError = "";
  var $_loc;

  function MemberAccountTransaction () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = true;
    if ($this->_amount == "") {
      $valid = FALSE;
      $this->_amountError = $this->_loc->getText("memberAccountTransError1");
    } else if (!is_numeric($this->_amount)) {
      $valid = FALSE;
      $this->_amountError = $this->_loc->getText("memberAccountTransError2");
    }
    if ($this->_description == "") {
      $valid = false;
      $this->_descriptionError = $this->_loc->getText("memberAccountTransError3");
    }
    return $valid;
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getMbrid() {
    return $this->_mbrid;
  }
  function getTransid() {
    return $this->_transid;
  }
  function getCreateDt() {
    return $this->_createDt;
  }
  function getCreateUserid() {
    return $this->_createUserid;
  }
  function getTransactionTypeCd() {
    return $this->_transactionTypeCd;
  }
  function getTransactionTypeDesc() {
    return $this->_transactionTypeDesc;
  }
  function getAmount() {
    return $this->_amount;
  }
  function getAmountError() {
    return $this->_amountError;
  }
  function getDescription() {
    return $this->_description;
  }
  function getDescriptionError() {
    return $this->_descriptionError;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setMbrid($value) {
    $this->_mbrid = trim($value);
  }
  function setTransid($value) {
    $this->_transid = trim($value);
  }
  function setCreateDt($value) {
    $this->_createDt = trim($value);
  }
  function setCreateUserid($value) {
    $this->_createUserid = trim($value);
  }
  function setTransactionTypeCd($value) {
    $this->_transactionTypeCd = trim($value);
  }
  function setTransactionTypeDesc($value) {
    $this->_transactionTypeDesc = trim($value);
  }
  function setAmount($value) {
    $this->_amount = trim($value);
  }
  function setDescription($value) {
    $this->_description = trim($value);
  }

}

?>
