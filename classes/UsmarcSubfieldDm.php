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
 * UsmarcSubfieldDm represents a row in usmarc_subfield_dm.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class UsmarcSubfieldDm {
  var $_tag = "";
  var $_subfieldCd = "";
  var $_description = "";
  var $_repeatableFlg = "";

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getTag() {
    return $this->_tag;
  }
  function getSubfieldCd() {
    return $this->_subfieldCd;
  }
  function getDescription() {
    return $this->_description;
  }
  function getRepeatableFlg() {
    return $this->_repeatableFlg;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setTag($value) {
    if (trim($value) == "") {
      $this->_tag = "0";
    } else {
      $this->_tag = trim($value);
    }
  }
  function setSubfieldCd($value) {
    $this->_subfieldCd = substr(trim($value),0,1);
  }
  function setDescription($value) {
    $this->_description = trim($value);
  }
  function setRepeatableFlg($value) {
    $this->_repeatableFlg = trim($value);
  }

}

?>
