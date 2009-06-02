<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/global_constants.php");
require_once("../database_constants.php");

class DbConnection {
  var $_link;
  var $_result;

  function connect_e() {
    if (!function_exists('mysql_connect')) {
      return new DbError(NULL,
                         "Unable to connect to database",
                         "The MySQL extension is not available");
    }
    $this->_link = mysql_connect(OBIB_HOST,OBIB_USERNAME,OBIB_PWD);
    if ($this->_link == false) {
      return new DbError(NULL,
                         "Unable to connect to database",
                         mysql_error());
    }
    $rc = mysql_select_db(OBIB_DATABASE, $this->_link);
    if ($rc == false) {
      return new DbError(NULL,
                         "Unable to select database",
                         mysql_error());
    }
    return NULL;
  }
  function close_e() {
    if (!mysql_close($this->_link)) {
      return new DbError(NULL,
                         "Unable to close database",
                         mysql_error());
    }
    return NULL;
  }
  function exec_e($sql) {
    $this->_result = mysql_query($sql, $this->_link);
    if (!$this->_result) {
      return new DbError($sql,
                         "SQL Query Failed",
                         mysql_error());
    }
    return NULL;
  }

  /****************************************************************************
   * Gets a result row
   * @param boolean $arrayType (optional) array type to return
   * @return array resulting array.  Returns false, if no more rows to fetch.
   * @access public
   ****************************************************************************
   */
  function fetchRow($arrayType=OBIB_ASSOC) {
    if (!$this->_result) {
      Fatal::internalError("Must execute query before fetching result row.");
    }
    if (is_bool($this->_result)) {
      return NULL;
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
   * Returns the number of rows in the result
   * @return int, number of rows in result
   * @access public
   ****************************************************************************
   */
  function numRows() {
    return mysql_num_rows($this->_result);
  }

  /****************************************************************************
   * Returns the field meta data for the result
   * @return object, Returns an object containing field information. 
   * @access public
   ****************************************************************************
   */
  function fetchField() {
    return mysql_fetch_field($this->_result);
  }

  /****************************************************************************
   * Returns the ID generated from the previous INSERT operation 
   * @return string, ID from previous INSERT
   ****************************************************************************
   */
  function getInsertId() {
    return mysql_insert_id();
  }
  
  /****************************************************************************
   * Quotes a string for use in a query
   * @param string $s string to be quoted
   * @return string quoted $s
   * @access public
   ****************************************************************************
   */
  function quote($s) {
    # would use mysql_real_escape_string(), but it requires PHP >= 4.3
    return "'" . mysql_escape_string($s) . "'";
  }
  /****************************************************************************
   * Quotes an identifier for use in a query
   * @param string $i identifier to be validated
   * @return string valid identifier
   * @access public
   ****************************************************************************
   */
  function ident($i) {
    # Because the MySQL manual is unclear on how to include a ` in a `-quoted
    # identifer, we just drop them.  It looks like phpMyAdmin does about the
    # same thing, so we're in good company.  But clearer documentation would
    # be nice.
    return '`' . str_replace('`', '', $i) . '`';
  }
  /****************************************************************************
   * Validates a numeric string for use in a query
   * @param string $n numeric string to be validated
   * @return string longest prefix of $n that can be treated as a number or "0"
   * @access public
   ****************************************************************************
   */
  function numstr($n) {
    if (ereg("^([+-]?[0-9]+(\.[0-9]*)?([Ee][0-9]+)?)", $n, $subs)) {
      return $subs[1];
    } else {
      return "0";
    }
  }
}


?>
