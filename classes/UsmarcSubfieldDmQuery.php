<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Query.php"));

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
    $this->Query();
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
      $sql .= $this->mkSQL("where tag = %N ", $tag);
    }
    $sql .= "order by tag, subfield_cd ";
    return $this->_query($sql, T("Error accessing the marc subfield data."));
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
  function get($tag, $subfld) {
    $sql = $this->mkSQL("select * from usmarc_subfield_dm "
                        . "where tag=%N and subfield_cd=%Q ",
                        $tag, $subfld);
    $rows = $this->eexec($sql);
    if (empty($rows)) {
      return NULL;
    }
    assert('count($rows) == 1');
    return $this->_formatSubfield($rows[0]);
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
