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

/******************************************************************************
 * BiblioQuery data access component for library bibliographies
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioStatusQuery extends Query {
  var $_rowCount = 0;
  function getRowCount() {
    return $this->_rowCount;
  }

  /****************************************************************************
   * Executes a query
   * @param string $mbrid mbrid of member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($statusCd, $mbrid = "") {
    # setting query that will return all the data
    $sql = "select biblio_status.*, ";
    $sql = $sql."biblio.material_cd, ";
    $sql = $sql."biblio.title, ";
    $sql = $sql."biblio.author, ";
    $sql = $sql."biblio.barcode_nmbr, ";
    $sql = $sql."greatest(0,to_days(curdate()) - to_days(biblio_status.due_back_dt)) days_late ";
    $sql = $sql."from biblio_status, biblio ";
    $sql = $sql."where biblio_status.status_cd = '".$statusCd."' ";
    if ($mbrid != "") {
      $sql = $sql."and biblio_status.mbrid = ".$mbrid." ";
    }
    $sql = $sql."and biblio_status.bibid = biblio.bibid ";
    $sql = $sql."order by biblio_status.due_back_dt, biblio.title ";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography status information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Returns the status of the specified bibliography
   * @param string $bibid bibid of bibliography to select
   * @return string returns status code or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function getStatusCd($bibid) {
    # setting query that will return all the data
    $sql = "select status_cd from biblio_status ";
    $sql = $sql."where bibid = ".$bibid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography status information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    if ($this->_rowCount <= 0) {
      return true;
    }
    $array = $this->_conn->fetchRow();
    return $array["status_cd"];
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the BiblioStatus object.
   * @return Biblio returns bibliography or false if no more bibliographies to fetch
   * @access public
   ****************************************************************************
   */
  function fetchBiblioStatus() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    $stat = new BiblioStatus();
    $stat->setBibid($array["bibid"]);
    $stat->setBarcodeNmbr($array["barcode_nmbr"]);
    $stat->setStatusBeginDt($array["status_begin_dt"]);
    $stat->setStatusCd($array["status_cd"]);
    $stat->setMbrid($array["mbrid"]);
    $stat->setStatusRenewDt($array["status_renew_dt"]);
    $stat->setDueBackDt($array["due_back_dt"]);
    $stat->setMaterialCd($array["material_cd"]);
    $stat->setTitle($array["title"]);
    $stat->setAuthor($array["author"]);
    $stat->setDaysLate($array["days_late"]);

    return $stat;
  }

  /****************************************************************************
   * Checkes a given bibliography barcode to see if it is ready to change status
   * @param string $barcodeNmbr barcode of bibliography to check
   * @return string returns bibid if ready to checkout, and false if error occurs
   * @access public
   ****************************************************************************
   */
  function validateBarcode($barcodeNmbr,$newStatus,$mbrid="",$classification="") {
    # Checking to see if there is a bibliography with the given barcode number
    $sql = "select bibid, material_cd, collection_cd from biblio where barcode_nmbr = ".$barcodeNmbr;
    $result = $this->_conn->exec($sql);
    $biblioCount = $this->_conn->numRows();
    if ($biblioCount == 0) {
      $this->_errorOccurred = true;
      $this->_error = "Bibliography not found with that barcode number.";
      return false;
    }
    $array = $this->_conn->fetchRow();
    $bibid = $array["bibid"];
    $materialCd = $array["material_cd"];
    $collectionCd = $array["collection_cd"];

    if ($newStatus == "out") {
      if (!$this->validateBarcodeCheckout($bibid, $mbrid, $classification, $materialCd, $collectionCd)) return false;
    } else {
      # all other status code updates: show error if new status is same as old
      $sql = "select biblio_status_dm.description ";
      $sql = $sql."from biblio_status, biblio_status_dm ";
      $sql = $sql."where biblio_status.status_cd = biblio_status_dm.code ";
      $sql = $sql."and biblio_status.bibid = ".$bibid." ";
      $sql = $sql."and biblio_status.status_cd = '".$newStatus."'";
      $result = $this->_conn->exec($sql);
      if ($result == false) {
        $this->_errorOccurred = true;
        $this->_error = "Error accessing bibliography status information to validate barcode.";
        $this->_dbErrno = $this->_conn->getDbErrno();
        $this->_dbError = $this->_conn->getDbError();
        $this->_SQL = $sql;
        return false;
      }
      $biblioCount = $this->_conn->numRows();
      if ($biblioCount > 0) {
        $array = $this->_conn->fetchRow();
        $desc = $array["description"];
        $this->_errorOccurred = true;
        $this->_error = "Bibliography is already in ".$desc." status.";
        return false;
      }
      if ($newStatus == "crt") {
        if (!$this->validateBarcodeCart($bibid)) return false;
      }
    }

    return $bibid;
  }


  /****************************************************************************
   * Checkes a given bibliography to see if it is ready to checkout
   * @param string $bibid bibid of bibliography to check out
   * @return boolean returns true if ready to checkout, and false if error occurs
   * @access public
   ****************************************************************************
   */
  function validateBarcodeCheckout($bibid,$mbrid,$classification,$materialCd, $collectionCd) {
    # Checkout edit: only allow checkout on biblios with no status
    $sql = "select biblio_status_dm.description ";
    $sql = $sql."from biblio_status, biblio_status_dm ";
    $sql = $sql."where biblio_status.status_cd = biblio_status_dm.code ";
    $sql = $sql."and biblio_status.bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography status information to validate barcode.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $biblioCount = $this->_conn->numRows();
    if ($biblioCount > 0) {
      $array = $this->_conn->fetchRow();
      $desc = $array["description"];
      $this->_errorOccurred = true;
      $this->_error = "Bibliography is currently in ".$desc." status.";
      return false;
    }

    #**************************************************************************
    #*  Check collection days_due_back to see if it is > 0
    #**************************************************************************
    $sql = "select description, days_due_back ";
    $sql = $sql."from collection_dm where code = ".$collectionCd;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing collection information to check days due back.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $daysDueBack = $array["days_due_back"];
    if ($daysDueBack <= 0) {
      $description = $array["description"];
      $this->_errorOccurred = true;
      $this->_error = "Bibliographies from the ".$description." collection can not be checked out.";
      return false;
    }

    #**************************************************************************
    #*  Now check biblio_status to see if member has reached his/her limit
    #**************************************************************************
    # getting current checkout count for the material being checked out
    $sql = "select count(*) row_count from biblio_status, biblio ";
    $sql = $sql."where biblio_status.mbrid = ".$mbrid." ";
    $sql = $sql."and biblio_status.bibid = biblio.bibid ";
    $sql = $sql."and biblio.material_cd = ".$materialCd;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography status information to validate material count.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $biblioCount = $array["row_count"];

    # getting checkout limit for the material and member classification
    if ($classification == "a") {
      $sql = "select description, adult_checkout_limit checkout_limit ";
    } else {
      $sql = "select description, juvenile_checkout_limit checkout_limit ";
    }
    $sql = $sql."from material_type_dm where code = ".$materialCd;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing material type information to check checkout limit.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $checkoutLimit = $array["checkout_limit"];
    if ($biblioCount >= $checkoutLimit) {
      $description = $array["description"];
      $this->_errorOccurred = true;
      $this->_error = "Member has already reached the checkout limit for material type, ".$description.".";
      return false;
    }
    return true;
  }

  /****************************************************************************
   * Checkes a given bibliography to see if it is ready to checkin to shelving cart
   * @param string $bibid bibid of bibliography to check in to shelving cart
   * @return boolean returns true if ready to checkout, and false if error occurs
   * @access public
   ****************************************************************************
   */
  function validateBarcodeCart($bibid) {
    # Checkin edit: bibliographies must be in some form of status in order to shelve.
    $sql = "select count(*) row_count ";
    $sql = $sql."from biblio_status ";
    $sql = $sql."where bibid = ".$bibid." ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography status information to verify status count.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $biblioCount = $array["row_count"];
    if ($biblioCount == 0) {
      $array = $this->_conn->fetchRow();
      $desc = $array["description"];
      $this->_errorOccurred = true;
      $this->_error = "Bibliography is not checked out.";
      return false;
    }
    return true;
  }

  /****************************************************************************
   * Inserts a new biblio status entry.
   * @param BiblioStatus $biblio status entry to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($stat) {
    # getting days due back so we can calculate the due back date
    $sql = "select collection_dm.days_due_back ";
    $sql = $sql."from biblio, ";
    $sql = $sql."collection_dm ";
    $sql = $sql."where biblio.collection_cd = collection_dm.code ";
    $sql = $sql."and biblio.bibid = ".$stat->getBibid();
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography days due back information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    if ($this->_conn->numRows() == 0) {
      $this->_errorOccurred = true;
      $this->_error = "Bibliography is invalid.";
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $daysDueBack = $array["days_due_back"];
    #echo "daysDueBack=".$daysDueBack;

    # building insert sql
    $sql = "insert into biblio_status values (";
    $sql = $sql.$stat->getBibid().", ";
    $sql = $sql."curdate(), ";
    $sql = $sql."'".$stat->getStatusCd()."', ";
    $sql = $sql.$stat->getMbrid().", ";
    $sql = $sql."null, date_add(curdate(),interval ".$daysDueBack." day)) ";

    # executing insert
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error inserting new bibliography status information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Updates a bibliography's status in the biblio status table.
   * @param BiblioStatus $stat bibliography status object to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($stat) {
    $sql = "update biblio_status set status_cd = '".$stat->getStatusCd()."', ";
    $sql = $sql."status_begin_dt = curdate(), ";
    if ($stat->getMbrid() != "") {
      $sql = $sql."mbrid=".$stat->getMbrid().", ";
    } else {
      $sql = $sql."mbrid=null, ";
    }
    if ($stat->getStatusRenewDt() != "") {
      $sql = $sql."status_renew_dt=".$stat->getStatusRenewDt().", ";
    } else {
      $sql = $sql."status_renew_dt=null, ";
    }
    if ($stat->getDueBackDt() != "") {
      $sql = $sql."due_back_dt=".$stat->getDueBackDt()." ";
    } else {
      $sql = $sql."due_back_dt=null ";
    }
    $sql = $sql."where bibid=".$stat->getBibid();
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating bibliography status information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes a bibliography from the biblio table.
   * @param string $statusCd status code to delete.
   * @param string_array $bibidArray bibliography ids of bibliographies to delete.
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($statusCd, $bibidArray) {
    $sql = "delete from biblio_status where status_cd = '".$statusCd."' ";
    if (is_array($bibidArray)) {
      $delimit = "";
      $bibidList = "";
      foreach($bibidArray as $value) {
        $bibidList = $bibidList.$delimit.$value;
        $delimit = ",";
      }
      $sql = $sql."and bibid in (".$bibidList.")";
    }

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting bibliography status information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
