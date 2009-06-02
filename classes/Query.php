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
require_once("../classes/DbConnection.php");

/******************************************************************************
 * Query parent data access component class for all data access components
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class Query {
  var $_conn;
  var $_errorOccurred = false;
  var $_error = "";
  var $_dbErrno = "";
  var $_dbError = "";
  var $_SQL = "";

  /****************************************************************************
   * Instantiates private connection var and connects to the database
   *
   * @return void
   * @access public
   ****************************************************************************
   */
  function connect() {
    $this->_conn = new DbConnection();
    $rc = $this->_conn->connect();
    if ($rc == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_conn->getError();
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      return false;
    }
    return true;
  }
  /****************************************************************************
   * Closes database and destroys connection
   *
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function close() {
    $rc = $this->_conn->close();
    if ($rc == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_conn->getError();
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      unset($this->_conn);
      return false;
    }
    unset($this->_conn);
    return true;
  }

  /****************************************************************************
   * @return boolean true if error occurred
   * @access public
   ****************************************************************************
   */
  function resetResult() {
    return $this->_conn->resetResult();
  }

  /****************************************************************************
   * clears error info
   * @access public
   ****************************************************************************
   */
  function clearErrors() {
    $this->_errorOccurred = false;
    $this->_error = "";
    $this->_dbErrno = "";
    $this->_dbError = "";
  }

  /****************************************************************************
   * @return boolean true if error occurred
   * @access public
   ****************************************************************************
   */
  function errorOccurred() {
    return $this->_errorOccurred;
  }
  /****************************************************************************
   * @return string error message
   * @access public
   ****************************************************************************
   */
  function getError() {
    return $this->_error;
  }
  /****************************************************************************
   * @return string error number returned from database
   * @access public
   ****************************************************************************
   */
  function getDbErrno() {
    return $this->_dbErrno;
  }
  /****************************************************************************
   * @return string error message returned from database
   * @access public
   ****************************************************************************
   */
  function getDbError() {
    return $this->_dbError;
  }
  /****************************************************************************
   * @return string SQL used in query when an error occurs in Query execution
   * @access public
   ****************************************************************************
   */
  function getSQL() {
    return $this->_SQL;
  }
}

?>
