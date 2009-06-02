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
require_once("../classes/BiblioHold.php");

/******************************************************************************
 * ReportQuery data access component for holds on library bibliography copies
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class ReportQuery extends Query {
  var $_rowCount = 0;
  var $_loc;

  function ReportQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  function getRowCount() {
    return $this->_rowCount;
  }

  /****************************************************************************
   * Executes a query to select holds
   * @param string $bibid bibid of bibliography copy to select
   * @return BiblioHold returns hold record or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function query($sql) {
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("reportQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result
   * @return array return array containing row
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    return $this->_conn->fetchRow(MYSQL_NUM);
  }
  function fetchRowAssoc() {
    return $this->_conn->fetchRow(MYSQL_ASSOC);
  }

  /****************************************************************************
   * Fetches field meta data for result
   * @access public
   ****************************************************************************
   */
  function fetchField() {
    return $this->_conn->fetchField();
  }


}
?>
