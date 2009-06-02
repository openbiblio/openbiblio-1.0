<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
/******************************************************************************
 * LabelFormat represents a label format specification
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class SubLabel {
  var $_left = 0;
  var $_top = 0;
  var $_lines;

  function SubLabel () {
    $this->_lines = Array();
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getLeft() {
    return $this->_left;
  }
  function getTop() {
    return $this->_top;
  }
  function getLines() {
    return $this->_lines;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setLeft($value) {
    $this->_left = trim($value);
  }
  function setTop($value) {
    $this->_top = trim($value);
  }
  function addLine($line) {
    $this->_lines[] = $line;
  }

}

?>
