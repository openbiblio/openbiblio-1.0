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
 * UsmarcTagDm represents a row in usmarc_tag_dm.
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class UsmarcTagDm {
  var $_blockNmbr = "";
  var $_tag = "";
  var $_description = "";
  var $_ind1Description = "";
  var $_ind2Description = "";
  var $_repeatableFlg = "";

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getBlockNmbr() {
    return $this->_blockNmbr;
  }
  function getTag() {
    return $this->_tag;
  }
  function getDescription() {
    return $this->_description;
  }
  function getInd1Description() {
    return $this->_ind1Description;
  }
  function getInd2Description() {
    return $this->_ind2Description;
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
  function setBlockNmbr($value) {
    if (trim($value) == "") {
      $this->_blockNmbr = "0";
    } else {
      $this->_blockNmbr = trim($value);
    }
  }
  function setTag($value) {
    if (trim($value) == "") {
      $this->_tag = "0";
    } else {
      $this->_tag = trim($value);
    }
  }
  function setDescription($value) {
    $this->_description = trim($value);
  }
  function setInd1Description($value) {
    $this->_ind1Description = trim($value);
  }
  function setInd2Description($value) {
    $this->_ind2Description = trim($value);
  }
  function setRepeatableFlg($value) {
    $this->_repeatableFlg = trim($value);
  }

}

?>
