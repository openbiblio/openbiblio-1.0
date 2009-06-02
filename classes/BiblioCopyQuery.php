<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/global_constants.php");
require_once("../classes/Query.php");
require_once("../classes/BiblioCopy.php");

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

  function BiblioCopyQuery() {
    $this->Query();
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
  function doQuery($bibid,$copyid) {
    # setting query that will return all the data
    $sql = $this->mkSQL("select biblio_copy.*, "
                        . " greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late "
                        . "from biblio_copy "
                        . "where biblio_copy.bibid = %N"
                        . " and biblio_copy.copyid = %N",
                        $bibid, $copyid);

    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr4"))) {
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $this->fetchCopy();
  }

  /****************************************************************************
   * Executes a query to select ONLY ONE COPY by barcode
   * @param string $barcode barcode of bibliography copy to select
   * @return Copy returns copy or true if barcode doesn't exist,
   *              false on error
   * @access public
   ****************************************************************************
   */
  function queryByBarcode($barcode) {
    # setting query that will return all the data
    $sql = $this->mkSQL("select biblio_copy.*, "
                        . "greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late "
                        . "from biblio_copy where biblio_copy.barcode_nmbr = %Q",
                        $barcode);

    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr4"))) {
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    if ($this->_rowCount == 0) {
      return true;
    }
    return $this->fetchCopy();
  }


  /****************************************************************************
   * Executes a query to select ALL COPIES belonging to a particular bibid
   * @param string $bibid bibid of bibliography copies to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($bibid) {
    # setting query that will return all the data
    $sql = $this->mkSQL("select biblio_copy.* "
                        . ",greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late "
                        . "from biblio_copy where biblio_copy.bibid = %N",
                        $bibid);

    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr4"))) {
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return true;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the BiblioCopy object.
   * @return BiblioCopy returns bibliography copy or false if no more
   *                    bibliography copies to fetch
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
    $copy->setCreateDt($array["create_dt"]);
    $copy->setCopyDesc($array["copy_desc"]);
    $copy->setBarcodeNmbr($array["barcode_nmbr"]);
    $copy->setStatusCd($array["status_cd"]);
    $copy->setStatusBeginDt($array["status_begin_dt"]);
    $copy->setDueBackDt($array["due_back_dt"]);
    $copy->setDaysLate($array["days_late"]);
    $copy->setMbrid($array["mbrid"]);
    $copy->setRenewalCount($array["renewal_count"]);
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
    $sql = $this->mkSQL("select count(*) from biblio_copy "
                        . "where barcode_nmbr = %Q "
                        . " and not (bibid = %N and copyid = %N) ",
                        $barcode, $bibid, $copyid);
    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr1"))) {
      return false;
    }
    $array = $this->_conn->fetchRow(OBIB_NUM);
    if ($array[0] > 0) {
      return true;
    }
    return false;
  }

  /****************************************************************************
   * Returns the next copyid number available in the biblio_copy copyid field for a given biblio
   * @return boolean returns false, if error occurs
   * @access private
   ****************************************************************************
   */
  function nextCopyid($bibid) {
    $sql = $this->mkSQL("select max(copyid) as lastNmbr from biblio_copy "
                        . "where biblio_copy.bibid = %Q",
                        $bibid);
    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr11"))) {
		//echo 'copyid fetch failed.';
      return false;
    }
		//echo 'got something!';
    $array = $this->_conn->fetchRow();
		$nmbr = $array["lastNmbr"];
    return $nmbr+1;
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
    $sql = $this->mkSQL("insert into biblio_copy values (%N"
                        . ",null, now(), %Q, %Q, %Q, sysdate(), ",
                        $copy->getBibid(), $copy->getCopyDesc(),
                        $copy->getBarcodeNmbr(), $copy->getStatusCd());
    if ($copy->getDueBackDt() == "") {
      $sql .= "null, ";
    } else {
      $sql .= $this->mkSQL("%Q, ", $copy->getDueBackDt());
    }
    if ($copy->getMbrid() == "") {
      $sql .= "null,";
    } else {
      $sql .= $this->mkSQL("%Q,", $copy->getMbrid());
    }
    $sql .= " 0)"; //Default renewal count to zero
    return $this->_query($sql, $this->_loc->getText("biblioCopyQueryErr3"));
  }

  /****************************************************************************
   * Updates a bibliography in the biblio table.
   * @param Biblio $biblio bibliography to update
   * @param boolean $checkout is this a checkout operation? default FALSE
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
    $sql = $this->mkSQL("update biblio_copy set "
                        . "status_cd=%Q, "
                        . "status_begin_dt=sysdate(), "
                        . "renewal_count=%N, ",
                        $copy->getStatusCd(),
                        $copy->getRenewalCount());

    if ($checkout){
      if ($copy->getDueBackDt() != "") {
        $sql .= $this->mkSQL("due_back_dt=date_add(sysdate(),interval %N day), ",
                             $copy->getDueBackDt());
      } else {
        $sql .= "due_back_dt=null, ";
      }
      if ($copy->getMbrid() != "") {
        $sql .= $this->mkSQL("mbrid=%N, ", $copy->getMbrid());
      } else {
        $sql .= "mbrid=null, ";
      }
    }
    $sql .= $this->mkSQL("copy_desc=%Q, barcode_nmbr=%Q "
                         . "where bibid=%N and copyid=%N",
                         $copy->getCopyDesc(), $copy->getBarcodeNmbr(),
                         $copy->getBibid(), $copy->getCopyid());
    return $this->_query($sql, $this->_loc->getText("biblioCopyQueryErr5"));
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
    $sql = $this->mkSQL("delete from biblio_copy where bibid = %N", $bibid);
    if ($copyid > 0) {
      $sql .= $this->mkSQL(" and copyid = %N", $copyid);
    }
    return $this->_query($sql, $this->_loc->getText("biblioCopyQueryErr6"));
  }

  /****************************************************************************
   * Retrieves collection info
   * @param int $bibid
   * @access private
   ****************************************************************************
   */
  function _getCollectionInfo($bibid) {
    // first get collection code
    $sql = $this->mkSQL("select collection_cd from biblio where bibid = %N",
                        $bibid);
    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr7"))) {
      return false;
    }
    $array = $this->_conn->fetchRow();
    $collectionCd = $array["collection_cd"];

    // now read collection domain for days due back
    $sql = $this->mkSQL("select * from collection_dm where code = %N",
                        $collectionCd);
    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr8"))) {
      return false;
    }
    return $this->_conn->fetchRow();
  }

  /****************************************************************************
   * Retrieves days due back for a given copy's collection code
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
   * Retrieves daily late fee for a given copy's collection code
   * @param BiblioCopy $copy bibliography copy object to get days due back
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
    $sql = $this->mkSQL("update biblio_copy set "
                        . " status_cd=%Q, status_begin_dt=sysdate(), "
                        . " due_back_dt=null, mbrid=null "
                        . "where status_cd=%Q ",
                        OBIB_STATUS_IN, OBIB_STATUS_SHELVING_CART);
    if (!$massCheckin) {
      $prefix = "and (";
      for ($i = 0; $i < count($bibids); $i++) {
        $sql .= $prefix;
	$sql .= $this->mkSQL("(bibid=%N and copyid=%N)",
                             $bibids[$i], $copyids[$i]);
        $prefix = " or ";
      }
      $sql .= ")";
    }
    return $this->_query($sql, $this->_loc->getText("biblioCopyQueryErr9"));
  }

  function _getCheckoutPrivs($bibid, $classification) {
    $sql = $this->mkSQL("select checkout_privs.* "
                        . "from biblio, checkout_privs "
                        . "where bibid=%N and classification=%N "
                        . "and biblio.material_cd=checkout_privs.material_cd ",
                        $bibid, $classification);
    $rows = $this->exec($sql);
    if (count($rows) != 1) {
      return array('checkout_limit'=>0, 'renewal_limit'=>0);
    }
    return $rows[0];
  }

  function hasReachedRenewalLimit($mbrid,$classification,$copy) {
    $array = $this->_getCheckoutPrivs($copy->getBibid(), $classification);
    if($array['renewal_limit'] == 0) {
        //0 = unlimited
        return FALSE;
    }
    if($copy->getRenewalCount() < $array['renewal_limit']) {
        return FALSE;
    }
    else {
        return TRUE;
    }
  }

  /****************************************************************************
   * determines if checkout limit for given member and material type has been reached
   * @param int $mbrid member id
   * @param int $classification member classification code
   * @param int $bibid bibliography id of bibliography material type to check for
   * @return boolean true if member has reached limit, otherwise false
   * @access public
   ****************************************************************************
   */
  function hasReachedCheckoutLimit($mbrid,$classification,$bibid) {
    $privs = $this->_getCheckoutPrivs($bibid, $classification);
    if($privs['checkout_limit'] == 0) {
        //0 = unlimited
        return FALSE;
    }

    // get member's current checkout count for given material type
    $sql = $this->mkSQL("select count(*) row_count from biblio_copy, biblio"
                        . " where biblio_copy.bibid = biblio.bibid"
                        . " and biblio_copy.mbrid = %N"
                        . " and biblio.material_cd = %N",
                        $mbrid, $privs["material_cd"]);
    if (!$this->_query($sql, $this->_loc->getText("biblioCopyQueryErr10"))) {
      return false;
    }
    $array = $this->_conn->fetchRow();
    if ($array["row_count"] >= $privs['checkout_limit']) {
      return TRUE;
    }
    return FALSE;
  }
}
?>
