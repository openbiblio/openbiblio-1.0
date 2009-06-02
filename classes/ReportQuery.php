<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
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

  function query($sql) {
    if (!$this->_query($sql, $this->_loc->getText("reportQueryErr1"))) {
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return true;
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
