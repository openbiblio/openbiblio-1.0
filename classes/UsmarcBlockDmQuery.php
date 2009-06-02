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
 * UsmarcBlockDmQuery data access component for usmarc_block_dm table
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class UsmarcBlockDmQuery extends Query {
  var $_loc;

  function UsmarcBlockDmQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  /****************************************************************************
   * Executes a query
   * @param string $table table name of domain table to query
   * @param int $code (optional) code of row to fetch
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect() {
    $sql = "select * from usmarc_block_dm ";
    $sql = $sql."order by block_nmbr ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("usmarcBlockDmQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
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
    $dm = new UsmarcBlockDm();
    $dm->setBlockNmbr($result["block_nmbr"]);
    $dm->setDescription($result["description"]);
    return $dm;
  }

  /****************************************************************************
   * Fetches all rows from the query result.
   * @return assocArray returns associative array indexed by block nmbr containing UsmarcBlockDm objects.
   * @access public
   ****************************************************************************
   */
  function fetchRows() {
    while ($result = $this->_conn->fetchRow()) {
      $dm = new UsmarcBlockDm();
      $dm->setBlockNmbr($result["block_nmbr"]);
      $dm->setDescription($result["description"]);
      $assoc[$result["block_nmbr"]] = $dm;
    }
    return $assoc;
  }

}

?>
