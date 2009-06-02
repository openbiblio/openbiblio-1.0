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
require_once("../classes/BiblioField.php");
require_once("../classes/Localize.php");

/******************************************************************************
 * BiblioFieldQuery data access component for library bibliography fields
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioFieldQuery extends Query {
  var $_rowCount = 0;
  var $_loc;

  function BiblioFieldQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  function getRowCount() {
    return $this->_rowCount;
  }

  /****************************************************************************
   * Executes a query to select ONLY ONE SUBFIELD
   * @param string $bibid bibid of bibliography copy to select
   * @param string $fieldid copyid of bibliography copy to select
   * @return BiblioField returns subfield or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function query($bibid,$fieldid) {
    # setting query that will return all the data in biblio
    $sql = "select biblio_field.* ";
    $sql = $sql."from biblio_field ";
    $sql = $sql."where biblio_field.bibid = ".$bibid;
    $sql = $sql." and biblio_field.fieldid = ".$fieldid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioFieldQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $this->fetchField();
  }

  /****************************************************************************
   * Executes a query
   * @param string $bibid bibid of bibliography fields to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($bibid) {
    # setting query that will return all the data in biblio
    $sql = "select biblio_field.* ";
    $sql = $sql."from biblio_field ";
    $sql = $sql."where biblio_field.bibid = ".$bibid;
    $sql = $sql." order by tag, subfield_cd";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioFieldQueryErr2");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Theme object.
   * @return Theme returns theme or false if no more themes to fetch
   * @access public
   ****************************************************************************
   */
  function fetchField() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }
    $fld = new BiblioField();
    $fld->setBibid($array["bibid"]);
    $fld->setFieldid($array["fieldid"]);
    $fld->setTag($array["tag"]);
    $fld->setInd1Cd($array["ind1_cd"]);
    $fld->setInd2Cd($array["ind2_cd"]);
    $fld->setSubfieldCd($array["subfield_cd"]);
    $fld->setFieldData($array["field_data"]);

    return $fld;
  }

  /****************************************************************************
   * Inserts new bibliography field into the biblio_field table.
   * @param BiblioField $field bibliography field to insert
   * @return boolean returns true if insert was successful or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($field) {
    # inserting biblio field row
    $sql = "insert into biblio_field values (";
    $sql = $sql.$field->getBibid().",null,";
    $sql = $sql.$field->getTag().",";
    $sql = $sql."'".$field->getInd1Cd()."',";
    $sql = $sql."'".$field->getInd2Cd()."',";
    $sql = $sql."'".$field->getSubfieldCd()."',";
    $sql = $sql."'".$field->getFieldData()."')";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioFieldQueryInsertErr");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return true;
  }


  /****************************************************************************
   * Updates a bibliography field in the biblio_field table.
   * @param BiblioField $field bibliography field to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($field) {
    # updating biblio table
    $sql = "update biblio_field set tag=".$field->getTag();
    $sql = $sql.",ind1_cd='".$field->getInd1Cd()."'";
    $sql = $sql.",ind2_cd='".$field->getInd2Cd()."'";
    $sql = $sql.",subfield_cd='".$field->getSubfieldCd()."'";
    $sql = $sql.",field_data='".$field->getFieldData()."'";
    $sql = $sql." where bibid=".$field->getBibid();
    $sql = $sql." and fieldid=".$field->getFieldid();

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioFieldQueryUpdateErr");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    return true;
  }

  /****************************************************************************
   * Deletes a bibliography field from the biblio_field table.
   * @param string $bibid bibliography id of bibliography field to delete
   * @param string $fieldid field id of bibliography field to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($bibid, $fieldid) {
    $sql = "delete from biblio_field where bibid = ".$bibid;
    $sql = $sql." and fieldid = ".$fieldid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioFieldQueryDeleteErr");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
