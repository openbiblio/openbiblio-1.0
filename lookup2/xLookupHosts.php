<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/******************************************************************************
 * LookupHosts represents the lookup Host settings.
 *
 * @author Fred LaPlante <flaplante@flos-inc.com>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class LookupHosts {
  var $_id = "";
  var $_seq = "";
  var $_active = false;
  var $_host = "";
  var $_name = "";
  var $_db = "";
  var $_user = "";
  var $_pw = "";

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = true;
    return $valid;
  }

  /****************************************************************************
   * getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getId() {
    return $this->_id;
  }
  function getSeq() {
    return $this->_seq;
  }
  function getActive() {
    return $this->_active;
  }
  function getHost() {
    return $this->_host;
  }
  function getName() {
    return $this->_name;
  }
  function getDb() {
    return $this->_db;
  }
  function getUser() {
    return $this->_defaultuser;
  }
  function getPw() {
    return $this->_pw;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setId($value) {
    $this->_id = trim($value);
  }
  function setSeq($value) {
    $this->_seq = trim($value);
  }
  function setActive($value) {
    $this->_active = trim($value);
  }
  function setHost($value) {
    $this->_host = $value;
   }
  function setName($value) {
    $this->_name = trim($value);
  }
  function setDb($value) {
    $this->_db = trim($value);
  }
  function setUser($value) {
    $this->_user = trim($value);
  }
  function setPw($value) {
    $this->_pw = $value;
  }
}

?>
