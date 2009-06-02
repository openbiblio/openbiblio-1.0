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

require_once("../shared/global_constants.php");
require_once("../classes/Query.php");
require_once("../classes/Localize.php");

/******************************************************************************
 * UsmarcSubfieldDmQuery data access component for usmarc_subfield_dm table
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class UsmarcSubfieldDmQuery extends Query {
  var $_loc;

  function UsmarcSubfieldDmQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  /****************************************************************************
   * Executes a query
   * @param int $code (optional) code of row to fetch
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($tag = "") {
    $sql = "select * from usmarc_subfield_dm ";
    if ($tag != "") {
      $sql = $sql."where tag = ".$tag." ";
    }
    $sql = $sql."order by tag, subfield_cd ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("usmarcSubfldDmQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Formats a subfield object from the selected row result
   * @param int $result fetched row
   * @return UsmarcSubfieldDm returns UsMarcSubfieldDm object
   * @access private
   ****************************************************************************
   */
  function _formatSubfield($result) {
    $dm = new UsmarcSubfieldDm();
    $dm->setTag($result["tag"]);
    $dm->setSubfieldCd($result["subfield_cd"]);
    $dm->setDescription($result["description"]);
    $dm->setRepeatableFlg($result["repeatable_flg"]);
    return $dm;
  }

  /****************************************************************************
   * Executes a query
   * @param int $subfld subfield code of row to fetch
   * @return SubfieldDm returns SubfieldDm object or false if error occurs
   * @access public
   ****************************************************************************
   */
  function query($tag, $subfld) {
    $sql = "select * from usmarc_subfield_dm ";
    $sql = $sql."where tag = ".$tag;
    $sql = $sql." and subfield_cd = '".$subfld."'";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("usmarcSubfldDmQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $result = $this->_conn->fetchRow();
    if ($result == false) {
      return false;
    }
    $dm = $this->_formatSubfield($result);
    return $dm;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Dm object.
   * @return Dm returns domain object or false if no more domain rows to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    $result = $this->_conn->fetchRow();
    if ($result == false) {
      return false;
    }
    $dm = $this->_formatSubfield($result);
    return $dm;
  }

  /****************************************************************************
   * Fetches all rows from the query result.
   * @return assocArray returns associative array indexed by tag containing UsmarcsubfieldDm objects.
   * @access public
   ****************************************************************************
   */
  function fetchRows() {
    while ($result = $this->_conn->fetchRow()) {
      $dm = new UsmarcSubfieldDm();
      $dm->setTag($result["tag"]);
      $dm->setSubfieldCd($result["subfield_cd"]);
      $dm->setDescription($result["description"]);
      $dm->setRepeatableFlg($result["repeatable_flg"]);
      $index = sprintf("%03d",$result["tag"]).$result["subfield_cd"];
      $assoc[$index] = $dm;
    }
    return $assoc;
  }

}

?>
