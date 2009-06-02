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
class Staff {
  var $_userid = "";
  var $_pwd = "";
  var $_pwdError = "";
  var $_pwd2 = "";
  var $_lastName = "";
  var $_lastNameError = "";
  var $_firstName = "";
  var $_username = "";
  var $_usernameError = "";
  var $_circAuth = false;
  var $_catalogAuth = false;
  var $_adminAuth = false;
  var $_suspended = false;
  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = true;
    if ($this->_lastName == "") {
      $valid = false;
      $this->_lastNameError = "Last name is required.";
    }
    if (strlen($this->_username) < 4) {
      $valid = false;
      $this->_usernameError = "Username must be at least 4 characters.";
    } elseif (substr_count($this->_username, " ") > 0) {
      $valid = false;
      $this->_usernameError = "Username must not contain any spaces.";
    }
    return $valid;
  }

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validatePwd() {
    $valid = true;
    if (strlen($this->_pwd) < 4) {
      $valid = false;
      $this->_pwdError = "Password must be at least 4 characters.";
    } elseif (substr_count($this->_pwd, " ") > 0) {
      $valid = false;
      $this->_pwdError = "Password must not contain any spaces.";
    } elseif ($this->_pwd != $this->_pwd2) {
      $valid = false;
      $this->_pwdError = "Passwords do not match.";
    }
    return $valid;
  }

  /****************************************************************************
   * @return string Staff userid
   * @access public
   ****************************************************************************
   */
  function getUserid() {
    return $this->_userid;
  }
  /****************************************************************************
   * @param string $userid userid of staff member
   * @return void
   * @access public
   ****************************************************************************
   */
  function setUserid($userid) {
    $this->_userid = trim($userid);
  }

  /****************************************************************************
   * @param string $pwd Password of staff member
   * @return void
   * @access public
   ****************************************************************************
   */
  function setPwd($pwd) {
    $this->_pwd = strtolower(trim($pwd));
  }
  function getPwd() {
    return $this->_pwd;
  }
  function getPwdError() {
    return $this->_pwdError;
  }
  function setPwd2($pwd) {
    $this->_pwd2 = strtolower(trim($pwd));
  }
  function getPwd2() {
    return $this->_pwd2;
  }

  /****************************************************************************
   * @return string Staff last name
   * @access public
   ****************************************************************************
   */
  function getLastName() {
    return $this->_lastName;
  }
  /****************************************************************************
   * @return string Last name error text
   * @access public
   ****************************************************************************
   */
  function getLastNameError() {
    return $this->_lastNameError;
  }
  /****************************************************************************
   * @param string $lastName last name of staff member
   * @return void
   * @access public
   ****************************************************************************
   */
  function setLastName($lastName) {
    $this->_lastName = trim($lastName);
  }
  /****************************************************************************
   * @return string first name of staff member
   * @access public
   ****************************************************************************
   */
  function getFirstName() {
    return $this->_firstName;
  }
  /****************************************************************************
   * @param string $firstName first name of staff member
   * @return void
   * @access public
   ****************************************************************************
   */
  function setFirstName($firstName) {
    $this->_firstName = trim($firstName);
  }
  /****************************************************************************
   * @return string Staff username
   * @access public
   ****************************************************************************
   */
  function getUsername() {
    return $this->_username;
  }
  /****************************************************************************
   * @return string Username error text
   * @access public
   ****************************************************************************
   */
  function getUsernameError() {
    return $this->_usernameError;
  }
  /****************************************************************************
   * @param string $username username of staff member
   * @return void
   * @access public
   ****************************************************************************
   */
  function setUsername($username) {
    $this->_username = strtolower(trim($username));
  }
  /****************************************************************************
   * @return boolean true if staff member has circulation authorization
   * @access public
   ****************************************************************************
   */
  function hasCircAuth() {
    return $this->_circAuth;
  }
  /****************************************************************************
   * @param boolean $circAuth true if staff member has circulation authorization
   * @return void
   * @access public
   ****************************************************************************
   */
  function setCircAuth($circAuth) {
    if ($circAuth == true) {
      $this->_circAuth = true;
    } else {
      $this->_circAuth = false;
    }
  }
  /****************************************************************************
   * @return boolean true if staff member has catalog authorization
   * @access public
   ****************************************************************************
   */
  function hasCatalogAuth() {
    return $this->_catalogAuth;
  }
  /****************************************************************************
   * @param boolean $catalogAuth true if staff member has catalog authorization
   * @return void
   * @access public
   ****************************************************************************
   */
  function setCatalogAuth($catalogAuth) {
    if ($catalogAuth == true) {
      $this->_catalogAuth = true;
    } else {
      $this->_catalogAuth = false;
    }
  }
  /****************************************************************************
   * @return boolean true if staff member has administration authorization
   * @access public
   ****************************************************************************
   */
  function hasAdminAuth() {
    return $this->_adminAuth;
  }
  /****************************************************************************
   * @param boolean $AdminAuth true if staff member has administration authorization
   * @return void
   * @access public
   ****************************************************************************
   */
  function setAdminAuth($adminAuth) {
    if ($adminAuth == true) {
      $this->_adminAuth = true;
    } else {
      $this->_adminAuth = false;
    }
  }
  /****************************************************************************
   * @return boolean true if staff member account has been suspended
   * @access public
   ****************************************************************************
   */
  function isSuspended() {
    return $this->_suspended;
  }
  /****************************************************************************
   * @param boolean $suspended true if staff member has been suspended
   * @return void
   * @access public
   ****************************************************************************
   */
  function setSuspended($suspended) {
    if ($suspended == true) {
      $this->_suspended = true;
    } else {
      $this->_suspended = false;
    }
  }
}

?>
