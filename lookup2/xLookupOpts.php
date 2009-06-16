<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
/******************************************************************************
 * LookupOpts represents the lookup settings.
 *
 * @author Fred LaPlante <flaplante@flos-inc.com>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class LookupOpts {
  var $_protocol = "";
  var $_maxHits = 0;
  var $_keepDashes = false;
  var $_callNmbrType = "";
  var $_autoDewey = true;
  var $_defaultDewey = "";
  var $_autoCutter = true;
  var $_cutterType = "";
  var $_cutterWord = "";
  var $_autoCollect = true;
  var $_fictionName = "";
  var $_fictionCode = "";
  var $_fictionLoC = "";
  var $_fictionDew = "";

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
  function getProtocol() {
    return $this->_protocol;
  }
  function getMaxHits() {
    return $this->_maxHits;
  }
  function getKeepDashes() {
    return $this->_keepDashes;
  }
  function getCallNmbrType() {
    return $this->_callNmbrType;
  }
  function getAutoDewey() {
    return $this->_autoDewey;
  }
  function getDefaultDewey() {
    return $this->_defaultDewey;
  }
  function getAutoCutter() {
    return $this->_autoCutter;
  }
  function getCutterType() {
    return $this->_cutterType;
  }
  function getCutterWord() {
    return $this->_cutterWord;
  }
  function getAutoCollect() {
    return $this->_autoCollect;
  }
  function getFictionName() {
    return $this->_fictionName;
  }
  function getFictionCode() {
    return $this->_fictionCode;
  }
  function getFictionLoC() {
    return $this->_fictionLoC;
  }
  function getFictionDew() {
    return $this->_fictionDew;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setProtocol($value) {
    $this->_protocol = trim($value);
  }
  function setMaxHits($value) {
    $this->_maxHits = trim($value);
  }
  function setKeepDashes($value) {
    $this->_keepDashes = $value;
   }
  function setCallNmbrType($value) {
    $this->_callNmbrType = trim($value);
  }
  function setAutoDewey($value) {
    $this->_autoDewey = trim($value);
  }
  function setDefaultDewey($value) {
    $this->_defaultDewey = trim($value);
  }
  function setAutoCutter($value) {
    $this->_autoCutter = $value;
  }
  function setCutterType($value) {
    $this->_cutterType = $value;
  }
  function setCutterWord($value) {
    $this->_cutterWord = trim($value);
  }
  function setAutoCollect($value) {
    $this->_autoCollect = $value;
  }
  function setFictionName($value) {
    $this->_fictionName = trim($value);
  }
  function setFictionCode($value) {
    $this->_fictionCode = trim($value);
  }
  function setFictionLoc($value) {
    $this->_fictionLoC = trim($value);
  }
  function setFictionDew($value) {
    $this->_fictionDew = trim($value);
  }
}

?>
