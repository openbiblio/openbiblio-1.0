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
 * BiblioCopyQuery data access component for library bibliography copies
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioCopyQuery extends Query {
  var $_rowCount = 0;
  var $_loc;

  function BiblioCopyQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  function getRowCount() {
    return $this->_rowCount;
  }


  /****************************************************************************
   * Executes a query to select ONLY ONE COPY
   * @param string $bibid bibid of bibliography copy to select
   * @param string $copyid copyid of bibliography copy to select
   * @return Copy returns copy or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function query($bibid,$copyid) {
    # setting query that will return all the data
    $sql = "select biblio_copy.* ";
    $sql = $sql.",greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late ";
    $sql = $sql."from biblio_copy ";
    $sql = $sql."where biblio_copy.bibid = ".$bibid;
    $sql = $sql." and biblio_copy.copyid = ".$copyid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr4");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $this->fetchCopy();
  }

  /****************************************************************************
   * Executes a query to select ONLY ONE COPY
   * @param string $bibid bibid of bibliography copy to select
   * @param string $copyid copyid of bibliography copy to select
   * @return Copy returns copy or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function queryByBarcode($barcode) {
    # setting query that will return all the data
    $sql = "select biblio_copy.* ";
    $sql = $sql.",greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late ";
    $sql = $sql."from biblio_copy ";
    $sql = $sql."where biblio_copy.barcode_nmbr = '".$barcode."'";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr4");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    if ($this->_rowCount == 0) {
      return true;
    }
    return $this->fetchCopy();
  }


  /****************************************************************************
   * Executes a query to select ALL COPIES
   * @param string $bibid bibid of bibliography copies to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($bibid) {
    # setting query that will return all the data
    $sql = "select biblio_copy.* ";
    $sql = $sql.",greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late ";
    $sql = $sql."from biblio_copy ";
    $sql = $sql."where biblio_copy.bibid = ".$bibid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr4");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the BiblioCopy object.
   * @return BiblioCopy returns bibliography copy or false if no more bibliography copies to fetch
   * @access public
   ****************************************************************************
   */
  function fetchCopy() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    $copy = new BiblioCopy();
    $copy->setBibid($array["bibid"]);
    $copy->setCopyid($array["copyid"]);
    $copy->setCopyDesc($array["copy_desc"]);
    $copy->setBarcodeNmbr($array["barcode_nmbr"]);
    $copy->setStatusCd($array["status_cd"]);
    $copy->setStatusBeginDt($array["status_begin_dt"]);
    $copy->setDueBackDt($array["due_back_dt"]);
    $copy->setDaysLate($array["days_late"]);
    $copy->setMbrid($array["mbrid"]);
    return $copy;
  }

  /****************************************************************************
   * Returns true if barcode number already exists
   * @param string $barcode Bibliography barcode number
   * @param string $bibid Bibliography id
   * @return boolean returns true if barcode already exists
   * @access private
   ****************************************************************************
   */
  function _dupBarcode($barcode, $bibid=0, $copyid=0) {
    $sql = "select count(*) from biblio_copy where barcode_nmbr = '".$barcode."'";
    $sql = $sql." and not (bibid = ".$bibid;
    $sql = $sql." and copyid = ".$copyid.")";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return 0;
    }
    $array = $this->_conn->fetchRow(OBIB_NUM);
    if ($array[0] > 0) {
      return true;
    }
    return false;
  }

  /****************************************************************************
   * Inserts a new bibliography copy into the biblio_copy table.
   * @param BiblioCopy $copy bibliography copy to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($copy) {
    # checking for duplicate barcode number
    $dupBarcode = $this->_dupBarcode($copy->getBarcodeNmbr());
    if ($this->errorOccurred()) return false;
    if ($dupBarcode) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr2",array("barcodeNmbr"=>$copy->getBarcodeNmbr()));
      return false;
    }
    $sql = "insert into biblio_copy values (";
    $sql = $sql.$copy->getBibid().",null, ";
    $sql = $sql."'".$copy->getCopyDesc()."', ";
    $sql = $sql."'".$copy->getBarcodeNmbr()."', ";
    $sql = $sql."'".$copy->getStatusCd()."', ";
    $sql = $sql."sysdate(), ";
    if ($copy->getDueBackDt() == "") {
      $sql = $sql."null, ";
    } else {
      $sql = $sql."'".$copy->getDueBackDt()."', ";
    }
    if ($copy->getMbrid() == "") {
      $sql = $sql."null)";
    } else {
      $sql = $sql."'".$copy->getMbrid()."')";
    }
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr3");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Updates a bibliography in the biblio table.
   * @param Biblio $biblio bibliography to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($copy, $checkout=FALSE) {
    # checking for duplicate barcode number
    if (!$checkout) {
      $dupBarcode = $this->_dupBarcode($copy->getBarcodeNmbr(), $copy->getBibid(), $copy->getCopyid());
      if ($this->errorOccurred()) return false;
      if ($dupBarcode) {
        $this->_errorOccurred = true;
        $this->_error = $this->_loc->getText("biblioCopyQueryErr2",array("barcodeNmbr"=>$copy->getBarcodeNmbr()));
        return false;
      }
    }
    $sql = "update biblio_copy set ";
    $sql = $sql."status_cd='".$copy->getStatusCd()."', ";
    $sql = $sql."status_begin_dt=sysdate(), ";

    if ($copy->getStatusCd() == OBIB_STATUS_OUT){
      if ($copy->getDueBackDt() != "") {
        $sql = $sql."due_back_dt=date_add(sysdate(),interval ".$copy->getDueBackDt()." day), ";
      } else {
        $sql = $sql."due_back_dt=null, ";
      }
      if ($copy->getMbrid() != "") {
        $sql = $sql."mbrid=".$copy->getMbrid().", ";
      } else {
        $sql = $sql."mbrid=null, ";
      }
    }
    $sql = $sql."copy_desc='".$copy->getCopyDesc()."', ";
    $sql = $sql."barcode_nmbr='".$copy->getBarcodeNmbr()."'";
    $sql = $sql." where bibid=".$copy->getBibid();
    $sql = $sql." and copyid=".$copy->getCopyid();

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr5");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
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
  function delete($bibid,$copyid=0) {
    $sql = "delete from biblio_copy where bibid = ".$bibid;
    if ($copyid > 0) {
      $sql = $sql." and copyid = ".$copyid;
    }
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr6");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Retrieves collection info
   * @param int $bibid
   * @access private
   ****************************************************************************
   */
  function _getCollectionInfo($bibid) {
    // first get collection code
    $sql = "select collection_cd from biblio where bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr7");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $collectionCd = $array["collection_cd"];

    // now read collection domain for days due back
    $sql = "select * from collection_dm where code = ".$collectionCd;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr8");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $this->_conn->fetchRow();
  }

  /****************************************************************************
   * Retrieves days due back for a given copies collection code
   * @param BilioCopy $copy bibliography copy object to get days due back
   * @return integer days due back or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function getDaysDueBack($copy) {
    $array = $this->_getCollectionInfo($copy->getBibid());
    return $array["days_due_back"];
  }

  /****************************************************************************
   * Retrieves daily late fee for a given copies collection code
   * @param BilioCopy $copy bibliography copy object to get days due back
   * @return decimal daily late fee or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function getDailyLateFee($copy) {
    $array = $this->_getCollectionInfo($copy->getBibid());
    return $array["daily_late_fee"];
  }

  /****************************************************************************
   * Update biblio copies to set the status to checked in
   * @param boolean $massCheckin checkin all shelving cart copies
   * @param array $bibids array of bibids to checkin
   * @param array $copyids array of copyids to checkin
   * @return boolean false, if error occurs
   * @access public
   ****************************************************************************
   */
  function checkin($massCheckin,$bibids,$copyids) {
    $sql = "update biblio_copy set ";
    $sql = $sql."status_cd='".OBIB_STATUS_IN."', ";
    $sql = $sql."status_begin_dt=sysdate(), ";
    $sql = $sql."due_back_dt=null, ";
    $sql = $sql."mbrid=null ";
    $sql = $sql."where status_cd='".OBIB_STATUS_SHELVING_CART."' ";
    if (!$massCheckin) {
      $prefix = "and (";
      for ($i = 0; $i < count($bibids); $i++) {
        $sql = $sql.$prefix."(bibid=".$bibids[$i]." and copyid=".$copyids[$i].")";
        $prefix = " or ";
      }
      $sql = $sql.")";
    }
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr9");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * determines if checkout limit for given member and material type has been reached
   * @param int $mbrid member id
   * @param String $classification member classification code
   * @param int $bibid bibliography id of bibliography material type to check for
   * @return boolean true if member has reached limit, otherwise false
   * @access public
   ****************************************************************************
   */
  function hasReachedCheckoutLimit($mbrid,$classification,$bibid) {
    // get material code for given bibid
    $sql = "select material_cd from biblio where bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr10");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $materialCd = $array["material_cd"];

    // get checkout limits from material_type_dm
    $sql = "select * from material_type_dm where code = ".$materialCd;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr10");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    if ($classification == OBIB_MBR_CLASSIFICATION_JUVENILE) {
      $checkoutLimit = $array["juvenile_checkout_limit"];
    } else {
      $checkoutLimit = $array["adult_checkout_limit"];
    }

    // get member's current checkout count for given material type
    $sql = "select count(*) row_count from biblio_copy, biblio";
    $sql = $sql." where biblio_copy.bibid = biblio.bibid";
    $sql = $sql." and biblio_copy.mbrid = ".$mbrid;
    $sql = $sql." and biblio.material_cd = ".$materialCd;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioCopyQueryErr10");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $array = $this->_conn->fetchRow();
    $rowCount = $array["row_count"];
    if ($rowCount >= $checkoutLimit) {
      return TRUE;
    }
    return FALSE;
  }

}

?>
