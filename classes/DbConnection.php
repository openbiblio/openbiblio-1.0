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
require_once("../database_constants.php");

/******************************************************************************
 * DbConnection encapsulates all database specific functions for OpenBiblio
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 * @package MyWebShop
 ******************************************************************************
 */
class DbConnection {
  var $_link;
  var $_result;
  var $_error;
  var $_dbErrno;
  var $_dbError;

  /****************************************************************************
   * Connects to the database
   *
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function connect() {
    $this->_link = mysql_connect(OBIB_HOST,OBIB_USERNAME,OBIB_PWD);
    if ($this->_link == false) {
      $this->_error = "Unable to connect to database";
      $this->_dbErrno = mysql_errno();
      $this->_dbError = mysql_error();
      return false;
    }
    $rc = mysql_select_db(OBIB_DATABASE, $this->_link);
    if ($rc == false) {
      $this->_error = "Unable to connect to database";
      $this->_dbErrno = mysql_errno();
      $this->_dbError = mysql_error();
      return false;
    }
    return true;
  }
  /****************************************************************************
   * Closes database connection
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function close() {
    $rc = mysql_close($this->_link);
    if ($rc == false) {
      $this->_error = "Unable to close database";
      $this->_dbErrno = mysql_errno();
      $this->_dbError = mysql_error();
      return false;
    }
    return true;
  }
  /****************************************************************************
   * Executes a query
   * @param string $sql SQL of query to execute
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function exec($sql) {
    $this->_result = mysql_query($sql, $this->_link);
    if ($this->_result == false) {
      $this->_error = "Unable to execute query";
      $this->_dbErrno = mysql_errno();
      $this->_dbError = mysql_error();
      return false;
    }
    ;
    return $this->_result;
  }

  /****************************************************************************
   * Executes a query
   * @param boolean $arrayType (optional) array type to return
   * @return array resulting array.  Returns false, if no more rows to fetch.
   * @access public
   ****************************************************************************
   */
  function fetchRow($arrayType=OBIB_ASSOC) {
    if ($this->_result == false) {
      $this->_error = "Invalid result.  Must execute query first.";
      return false;
    }
    switch ($arrayType) {
      case OBIB_ASSOC:
        $mysqlArrayType = MYSQL_ASSOC;
        break;
      case OBIB_NUM:
        $mysqlArrayType = MYSQL_NUM;
        break;
      case OBIB_BOTH:
        $mysqlArrayType = MYSQL_BOTH;
        break;
      default:
        $mysqlArrayType = MYSQL_ASSOC;
    }
    return mysql_fetch_array($this->_result, $mysqlArrayType);
  }


  /****************************************************************************
   * Resets row point to the first row in the resultset
   * @return false, if no more rows to fetch.
   * @access public
   ****************************************************************************
   */
  function resetResult() {
    mysql_data_seek($this->_result,0);
  }

  /****************************************************************************
   * Returns then number of rows in the result
   * @return int, number of rows in result
   * @access public
   ****************************************************************************
   */
  function numRows() {
    return mysql_num_rows($this->_result);
  }

  /****************************************************************************
   * @return link connection link
   * @access public
   ****************************************************************************
   */
  function get_link() {
    return $this->_link;
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
}


?>
