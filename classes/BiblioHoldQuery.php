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
 * BiblioHoldQuery data access component for holds on library bibliography copies
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioHoldQuery extends Query {
  var $_rowCount = 0;
  var $_loc;

  function BiblioHoldQuery () {
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
  function queryByBibid($bibid) {
    # setting query that will return all the data
    $sql = "select biblio_hold.* ";
    $sql = $sql.",member.last_name ";
    $sql = $sql.",member.first_name ";
    $sql = $sql.",biblio_copy.barcode_nmbr ";
    $sql = $sql.",biblio_copy.status_cd ";
    $sql = $sql.",biblio_copy.due_back_dt ";
    $sql = $sql."from biblio_hold ";
    $sql = $sql.",biblio_copy ";
    $sql = $sql.",member ";
    $sql = $sql."where biblio_hold.bibid = biblio_copy.bibid";
    $sql = $sql." and biblio_hold.copyid = biblio_copy.copyid";
    $sql = $sql." and biblio_hold.mbrid = member.mbrid";
    $sql = $sql." and biblio_hold.bibid = ".$bibid;
    $sql = $sql." order by barcode_nmbr, hold_begin_dt ";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioHoldQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Executes a query to select holds
   * @param string $mbrid mbrid of member placing holds
   * @return BiblioHold returns hold record or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function queryByMbrid($mbrid) {
    # setting query that will return all the data
    $sql = "select biblio_hold.* ";
    $sql = $sql.",biblio.title ";
    $sql = $sql.",biblio.author ";
    $sql = $sql.",biblio.material_cd ";
    $sql = $sql.",biblio_copy.barcode_nmbr ";
    $sql = $sql.",biblio_copy.status_cd ";
    $sql = $sql.",biblio_copy.due_back_dt ";
    $sql = $sql."from biblio_hold ";
    $sql = $sql.",biblio_copy ";
    $sql = $sql.",biblio ";
    $sql = $sql."where biblio_hold.bibid = biblio_copy.bibid";
    $sql = $sql." and biblio_hold.copyid = biblio_copy.copyid";
    $sql = $sql." and biblio_hold.bibid = biblio.bibid";
    $sql = $sql." and biblio_hold.mbrid = ".$mbrid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioHoldQueryErr2");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    if ($this->_rowCount == 0) {
      return true;
    }
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the BiblioHold object.
   * @return BiblioHold returns hold on bibliography copy or false if no more holds to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    $hold = new BiblioHold();
    $hold->setBibid($array["bibid"]);
    $hold->setCopyid($array["copyid"]);
    $hold->setHoldid($array["holdid"]);
    $hold->setHoldBeginDt($array["hold_begin_dt"]);
    $hold->setMbrid($array["mbrid"]);
    $hold->setBarcodeNmbr($array["barcode_nmbr"]);
    $hold->setStatusCd($array["status_cd"]);
    $hold->setDueBackDt($array["due_back_dt"]);
    if (isset($array["title"])) {
      $hold->setTitle($array["title"]);
    }
    if (isset($array["author"])) {
      $hold->setAuthor($array["author"]);
    }
    if (isset($array["material_cd"])) {
      $hold->setMaterialCd($array["material_cd"]);
    }
    if (isset($array["last_name"])) {
      $hold->setLastName($array["last_name"]);
    }
    if (isset($array["first_name"])) {
      $hold->setFirstName($array["first_name"]);
    }
    return $hold;
  }

  /****************************************************************************
   * Inserts a new bibliography copy hold into the biblio_hold table.
   * @param BiblioHold $hold hold to insert
   * @return int 0 - error
   *             1 - success
   *             2 - invalid barcode
   * @access public
   ****************************************************************************
   */
  function insert($mbrid,$barcode) {
    // getting bibid and copyid for a given barcode
    $sql = "select bibid, copyid from biblio_copy where barcode_nmbr = '".$barcode."'";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioHoldQueryErr3");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    if ($this->_conn->numRows() == 0) {
      return 2;
    }
    $array = $this->_conn->fetchRow();
    $bibid = $array["bibid"];
    $copyid = $array["copyid"];


    $sql = "insert into biblio_hold values (";
    $sql = $sql.$bibid.", ";
    $sql = $sql.$copyid.",null, ";
    $sql = $sql."sysdate(), ";
    $sql = $sql.$mbrid.")";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioHoldQueryErr4");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return 1;
  }

  /****************************************************************************
   * Deletes a copy from the biblio_copy table.
   * @param string $bibid bibliography id of copy to delete
   * @param string $copyid optional copy id of copy to delete.  If none
   *               supplied then all copies under a given bibid will be deleted.
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($bibid,$copyid,$holdid) {
    $sql = "delete from biblio_hold where bibid = ".$bibid;
    $sql = $sql." and copyid = ".$copyid;
    $sql = $sql." and holdid = ".$holdid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioHoldQueryErr5");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Retrieves mbrid of first member in hold queue
   * @param long $bibid bibid of bibliography on hold
   * @param long $copyid copyid of bibliography on hold
   * @return long mbrid of first member in queue, -1 if not on hold, or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function getFirstHold($bibid,$copyid) {
    $sql = "select * from biblio_hold ";
    $sql = $sql."where bibid = ".$bibid;
    $sql = $sql." and copyid = ".$copyid;
    $sql = $sql." order by hold_begin_dt";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioHoldQueryErr6");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return FALSE;
    }
    $this->_rowCount = $this->_conn->numRows();
    if ($this->_rowCount == 0) {
      return FALSE;
    }
    $array = $this->_conn->fetchRow();
    $hold = new BiblioHold();
    $hold->setBibid($array["bibid"]);
    $hold->setCopyid($array["copyid"]);
    $hold->setHoldid($array["holdid"]);
    $hold->setHoldBeginDt($array["hold_begin_dt"]);
    $hold->setMbrid($array["mbrid"]);
    return $hold;
  }

}
?>
