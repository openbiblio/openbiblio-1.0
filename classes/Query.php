<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
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
    $error = $this->_conn->connect_e();
    if ($error) {
      Fatal::dbError($error->sql, $error->msg, $error->dberror);
    }
  }
  /****************************************************************************
   * Closes database and destroys connection
   *
   * @return void
   * @access public
   ****************************************************************************
   */
  function close() {
    $error = $this->_conn->close_e();
    if ($error) {
      Fatal::dbError($error->sql, $error->msg, $error->dberror);
    }
    unset($this->_conn);
  }

  function _exec($sql) {
    $error = $this->_conn->exec_e($sql);
    if ($error) {
      Fatal::dbError($error->sql, $error->msg, $error->dberror);
    }
  }
  function fetchRows($arrayType=OBIB_ASSOC) {
    $results = array();
    while ($r = $this->_conn->fetchRow($arrayType)) {
      array_push($results, $r);
    }
    return $results;
  }
  function exec($sql, $arrayType=OBIB_ASSOC) {
    $this->_exec($sql);
    return $this->fetchRows($arrayType);
  }
  
  /****************************************************************************
   * Makes SQL by interpolating values into a format string.
   * This function works something like printf() for SQL queries.  Format
   * strings contain %-escapes signifying that a value from the argument
   * list should be inserted into the string at that point.  The routine
   * properly quotes or transforms the value to make sure that it will be
   * handled correctly by the SQL server.  The recognized format strings
   * are as follows:
   *  %% - is replaced by a single '%' character and does not consume a
   *       value form the argument list.
   *  %C - treats the argument as a column reference.  This differs from
   *       %I below only in that it passes the '.' operator for separating
   *       table and column names on to the SQL server unquoted.
   *  %I - treats the argument as an identifier to be quoted.
   *  %N - treats the argument as a number and strips off all of it but
   *       an initial numeric string with optional sign and decimal point.
   *  %Q - treats the argument as a string and quotes it.
   * @param string $fmt format string
   * @param string ... optional argument values
   * @return string the result of interpreting $fmt
   * @access public
   ****************************************************************************
   */
  function mkSQL() {
    $n = func_num_args();
    if ($n < 1)
      return false;
    $i = 1;
    $SQL = "";
    $fmt = func_get_arg(0);
    while (strlen($fmt)) {
      $p = strpos($fmt, "%");
      if ($p === false) {
        $SQL .= $fmt;
        break;
      }
      $SQL .= substr($fmt, 0, $p);
      if (strlen($fmt) < $p+2)
        return false;
      if ($fmt{$p+1} == '%') {
        $SQL .= "%";
      } else {
        if ($i >= $n)
          return false;
        $arg = func_get_arg($i++);
        switch ($fmt{$p+1}) {
        case 'C':
          $a = array();
          foreach (explode('.', $arg) as $ident) {
            array_push($a, $this->_conn->ident($ident));
          }
          $SQL .= implode('.', $a);
          break;
        case 'I':
          $SQL .= $this->_conn->ident($arg);
          break;
        case 'N':
          $SQL .= $this->_conn->numstr($arg);
          break;
        case 'Q':
          $SQL .= $this->_conn->quote($arg);
          break;
        default:
          return false;
        }
      }
      $fmt = substr($fmt, $p+2);
    }
    return $SQL;
  }

  /****************************************************************************
   * Executes an SQL query handling the standard bookkeeping.
   * @param string $sql query string
   * @param string $msg error message to be returned if the query fails
   * @return boolean false if there was an error
   * @access private to be used by subclasses only
   ****************************************************************************
   */
  function _query($sql, $msg) {
    $error = $this->_conn->exec_e($sql);
    if ($error) {
      $this->_errorOccurred = true;
      $this->_error = $error->msg;
      $this->_dbErrno = NULL;
      $this->_dbError = $error->dberror;
      $this->_SQL = $error->sql;
      return false;
    }
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
