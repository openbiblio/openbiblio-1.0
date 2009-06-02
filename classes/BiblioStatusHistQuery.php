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
require_once("../classes/BiblioStatusHist.php");
require_once("../classes/SettingsQuery.php");

/******************************************************************************
 * BiblioStatusHistQuery data access component for holds on library bibliography copies
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioStatusHistQuery extends Query {
  var $_rowCount = 0;
  var $_loc;

  function BiblioStatusHistQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  function getRowCount() {
    return $this->_rowCount;
  }


  /****************************************************************************
   * Executes a query to select status history
   * @param string $bibid bibid of bibliography status history to select
   * @return BiblioHold returns hold record or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function queryByBibid($bibid) {
    # setting query that will return all the data
    $sql = "select biblio_status_hist.* ";
    $sql = $sql.",biblio.title ";
    $sql = $sql.",biblio.author ";
    $sql = $sql.",biblio_copy.barcode_nmbr biblio_barcode_nmbr";
    $sql = $sql.",member.last_name ";
    $sql = $sql.",member.first_name ";
    $sql = $sql.",member.barcode_nmbr mbr_barcode_nmbr ";

    $sql = $sql."from biblio_status_hist ";
    $sql = $sql.",biblio ";
    $sql = $sql.",biblio_copy ";
    $sql = $sql.",member ";

    $sql = $sql."where biblio_status_hist.bibid = biblio.bibid";
    $sql = $sql." and biblio_status_hist.bibid = biblio_copy.bibid";
    $sql = $sql." and biblio_status_hist.copyid = biblio_copy.copyid";
    $sql = $sql." and biblio_status_hist.mbrid = member.mbrid";
    $sql = $sql." and biblio_status_hist.bibid = ".$bibid;
    $sql = $sql." order by barcode_nmbr, status_begin_dt ";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioStatusHistQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Executes a query to select status history
   * @param string $mbrid mbrid of member
   * @return BiblioHold returns hold record or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function queryByMbrid($mbrid) {
    # setting query that will return all the data
    $sql = "select biblio_status_hist.* ";
    $sql = $sql.",biblio.title ";
    $sql = $sql.",biblio.author ";
    $sql = $sql.",biblio_copy.barcode_nmbr biblio_barcode_nmbr";
    $sql = $sql.",member.last_name ";
    $sql = $sql.",member.first_name ";
    $sql = $sql.",member.barcode_nmbr mbr_barcode_nmbr ";

    $sql = $sql."from biblio_status_hist ";
    $sql = $sql.",biblio ";
    $sql = $sql.",biblio_copy ";
    $sql = $sql.",member ";

    $sql = $sql."where biblio_status_hist.bibid = biblio.bibid";
    $sql = $sql." and biblio_status_hist.bibid = biblio_copy.bibid";
    $sql = $sql." and biblio_status_hist.copyid = biblio_copy.copyid";
    $sql = $sql." and biblio_status_hist.mbrid = member.mbrid";
    $sql = $sql." and biblio_status_hist.mbrid = ".$mbrid;
    $sql = $sql." order by status_begin_dt desc ";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioStatusHistQueryErr2");
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
   * Fetches a row from the query result and populates the BiblioStatusHist object.
   * @return BiblioStatusHist returns bibliography status history object or false if no more holds to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    $hist = new BiblioStatusHist();
    $hist->setBibid($array["bibid"]);
    $hist->setCopyid($array["copyid"]);
    $hist->setBiblioBarcodeNmbr($array["biblio_barcode_nmbr"]);
    $hist->setTitle($array["title"]);
    $hist->setAuthor($array["author"]);
    $hist->setStatusCd($array["status_cd"]);
    $hist->setStatusBeginDt($array["status_begin_dt"]);
    $hist->setMbrid($array["mbrid"]);
    $hist->setLastName($array["last_name"]);
    $hist->setFirstName($array["first_name"]);
    $hist->setMbrBarcodeNmbr($array["mbr_barcode_nmbr"]);
    $hist->setDueBackDt($array["due_back_dt"]);
    return $hist;
  }

  /****************************************************************************
   * Inserts a new bibliography status history into the biblio_status_hist table.
   * @param BiblioStatusHist $hist history to insert
   * @access public
   ****************************************************************************
   */
  function insert($hist) {
    $sql = "insert into biblio_status_hist values (";
    $sql = $sql.$hist->getBibid().", ";
    $sql = $sql.$hist->getCopyid().", ";
    $sql = $sql."'".$hist->getStatusCd()."', ";
    $sql = $sql."sysdate(), ";
    if ($hist->getDueBackDt() != "") {
      $sql = $sql."date_add(sysdate(),interval ".$hist->getDueBackDt()." day), ";
    } else {
      $sql = $sql."null, ";
    }
    $sql = $sql.$hist->getMbrid().")";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioStatusHistQueryErr3");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_purgeHistory($hist->getMbrid());
    return $result;
  }

  /****************************************************************************
   * Deletes history from the biblio_status_hist table.
   * @param string $bibid bibliography id of history to delete
   * @param string $copyid copy id of history to delete.
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function deleteByBibid($bibid,$copyid) {
    $sql = "delete from biblio_status_hist where bibid = ".$bibid;
    $sql = $sql." and copyid = ".$copyid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioStatusHistQueryErr4");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes history from the biblio_status_hist table.
   * @param string $mbrid member id of history to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function deleteByMbrid($mbrid) {
    $sql = "delete from biblio_status_hist where mbrid = ".$mbrid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioStatusHistQueryErr5");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes history from the biblio_status_hist table.
   * @param string $mbrid member id of history to delete
   * @return boolean returns false, if error occurs
   * @access private
   ****************************************************************************
   */
  function _purgeHistory($mbrid) {
    $setQ = new SettingsQuery();
    $purgeMo = $setQ->getPurgeHistoryAfterMonths($this->_conn);
    if ($setQ->errorOccurred()) {
      $this->_error = $setQ->getError();
      $this->_dbErrno = $setQ->getDbErrno();
      $this->_dbError = $setQ->getDbError();
      return false;
    }
    if ($purgeMo == 0) {
      return TRUE;
    }
    $sql = "delete from biblio_status_hist where mbrid = ".$mbrid;
    $sql = $sql." and status_begin_dt <= date_add(sysdate(),interval - ".$purgeMo." month)";
    // need to add where clause for purge rule
    $result = $this->_conn->exec($sql);
    if ($result == FALSE) {
      $this->_errorOccurred = TRUE;
      $this->_error = $this->_loc->getText("biblioStatusHistQueryErr5");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return FALSE;
    }
    return $result;
  }

}
?>
