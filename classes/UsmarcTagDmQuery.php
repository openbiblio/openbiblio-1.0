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
 * UsmarcTagDmQuery data access component for usmarc_tag_dm table
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class UsmarcTagDmQuery extends Query {
  var $_loc;

  function UsmarcTagDmQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  /****************************************************************************
   * Executes a query
   * @param int $code (optional) code of row to fetch
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($blockNmbr = "") {
    $sql = "select * from usmarc_tag_dm ";
    if ($blockNmbr != "") {
      $sql = $sql."where block_nmbr = ".$blockNmbr." ";
    }
    $sql = $sql."order by tag ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("usmarcTagDmQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Formats a tag object from the selected row result
   * @param int $result fetched row
   * @return UsmarcTagDm returns UsMarcTagDm object
   * @access private
   ****************************************************************************
   */
  function _formatTag($result) {
    $dm = new UsmarcTagDm();
    $dm->setBlockNmbr($result["block_nmbr"]);
    $dm->setTag($result["tag"]);
    $dm->setDescription($result["description"]);
    $dm->setInd1Description($result["ind1_description"]);
    $dm->setInd2Description($result["ind2_description"]);
    $dm->setRepeatableFlg($result["repeatable_flg"]);
    return $dm;
  }

  /****************************************************************************
   * Executes a query
   * @param int $tag tag of row to fetch
   * @return UsmarcTagDm returns UsMarcTagDm object or false if error occurs
   * @access public
   ****************************************************************************
   */
  function query($tag) {
    $sql = "select * from usmarc_tag_dm ";
    $sql = $sql."where tag = ".$tag." ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("usmarcTagDmQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $result = $this->_conn->fetchRow();
    if ($result == false) {
      return false;
    }
    $dm = $this->_formatTag($result);
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
    $dm = $this->_formatTag($result);
    return $dm;
  }

  /****************************************************************************
   * Fetches all rows from the query result.
   * @return assocArray returns associative array indexed by tag containing UsmarcTagDm objects.
   * @access public
   ****************************************************************************
   */
  function fetchRows() {
    $tagsFound = false;
    while ($result = $this->_conn->fetchRow()) {
      $tagsFound = true;
      $dm = new UsmarcTagDm();
      $dm->setBlockNmbr($result["block_nmbr"]);
      $dm->setTag($result["tag"]);
      $dm->setDescription($result["description"]);
      $dm->setInd1Description($result["ind1_description"]);
      $dm->setInd2Description($result["ind2_description"]);
      $dm->setRepeatableFlg($result["repeatable_flg"]);
      $assoc[$result["tag"]] = $dm;
    }
    if ($tagsFound) {
      return $assoc;
    } else {
      return false;
    }
  }

}

?>
