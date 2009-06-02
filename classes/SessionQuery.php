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

require_once("../classes/Query.php");

/******************************************************************************
 * SessionQuery data access component for signon sessions
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class SessionQuery extends Query {
  /****************************************************************************
   * Executes a query to validate the token
   * @param string $userid userid of staff member to validate
   * @param string $token signon token of staff member to validate
   * @return boolean returns true if token is valid, false if token is not.
   * @access public
   ****************************************************************************
   */
  function validToken($userid, $token) {
    $sql = "select last_updated_dt, token from session where userid = '".$userid;
    $sql = $sql."' and token = ".$token;
    $sql = $sql." and last_updated_dt >= date_sub(sysdate(), interval ".OBIB_SESSION_TIMEOUT." minute)";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing session information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $rowCount = $this->_conn->numRows();
    if ($rowCount > 0) {
      $this->_updateToken($token);
      return true;
    } else {
      return false;
    }
  }
  /****************************************************************************
   * Inserts or updates the session table and returns a new valid signon token
   * @param string $userid userid of staff member to validate
   * @return string returns token or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function getToken($userid) {
    /**************************************************************************
     * Only purpose of the delete is to clean up old session rows so that the
     * session table doesn't get too full.
     **************************************************************************/
    $sql = "delete from session where userid = ".$userid;
    $sql = $sql." and last_updated_dt < date_sub(sysdate(), interval ".OBIB_SESSION_TIMEOUT." minute)";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting session information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    srand((double) microtime() * 1000000);
    $token = rand(-10000,10000);
    $sql = "insert into session values (";
    $sql = $sql.$userid.", sysdate(), ";
    $sql = $sql.$token.")";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error creating a new session.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $token;
  }
  /****************************************************************************
   * Updates last updated date in session table so that the session will not
   * time out.
   * @param string $token token of session to update
   * @return boolean returns true if successful, false if error occurs.
   * @access private
   ****************************************************************************
   */
  function _updateToken($token) {
    $sql = "update session set last_updated_dt=sysdate() where token = '".$token."'";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating session timeout.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }
}

?>
