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
  var $_loc;

  function SubLabel () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
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
  function addLine($value) {
    $this->_lines[] = trim($value);
  }

}

?>
