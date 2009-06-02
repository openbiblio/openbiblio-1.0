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
class BiblioQuery extends Query {
  var $_itemsPerPage = 1;
  var $_rowNmbr = 0;
  var $_currentRowNmbr = 0;
  var $_currentPageNmbr = 0;
  var $_rowCount = 0;
  var $_pageCount = 0;

  function setItemsPerPage($value) {
    $this->_itemsPerPage = $value;
  }
  function getCurrentRowNmbr() {
    return $this->_currentRowNmbr;
  }
  function getRowCount() {
    return $this->_rowCount;
  }
  function getPageCount() {
    return $this->_pageCount;
  }

  /****************************************************************************
   * Executes a query
   * @param string $type one of the global constants
   *               OBIB_SEARCH_BARCODE,
   *               OBIB_SEARCH_TITLE,
   *               OBIB_SEARCH_AUTHOR,
   *               or OBIB_SEARCH_SUBJECT
   * @param string @$words pointer to an array containing words to search for
   * @param integer $page What page should be returned if results are more than one page
   * @param string $sortBy column name to sort by.  Can be title or author
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSearch($type, &$words, $page, $sortBy) {
    # reset stats
    $this->_rowNmbr = 0;
    $this->_currentRowNmbr = 0;
    $this->_currentPageNmbr = $page;
    $this->_rowCount = 0;
    $this->_pageCount = 0;

    # Building sql statements
    if ($type == OBIB_SEARCH_BARCODE) {
      $col = "barcode_nmbr";
    } elseif ($type == OBIB_SEARCH_AUTHOR) {
      $col = "author";
#    } elseif ($type == OBIB_SEARCH_SUBJECT) {
#      $col = "";
    } else {
      $col = "title";
    }

    # setting selection criteria sql
    $criteria = "where ".$col." like '%".$words[0]."%'";
    for ($i = 1; $i < sizeof($words); $i++) {
      $criteria = $criteria." and ".$col." like '%".$words[$i]."%'";
    }

    # setting count query
    $sqlcount = "select count(*) as rowcount from biblio ".$criteria;

    # setting query that will return all the data
    $sql = "select biblio.*, ";
    $sql = $sql."biblio_status.status_cd, ";
    $sql = $sql."biblio_status.mbrid status_mbrid, ";
    $sql = $sql."biblio_status.due_back_dt ";
    $sql = $sql."from biblio ";
    $sql = $sql."left join biblio_status on biblio.bibid=biblio_status.bibid ";
    $sql = $sql.$criteria;
    $sql = $sql." order by ".$sortBy;

    # setting limit so we can page through the results
    $offset = ($page - 1) * $this->_itemsPerPage;
    $limit = $this->_itemsPerPage;
    $sql = $sql." limit ".$offset.",".$limit;

    #echo "sqlcount=[".$sqlcount."]<br>\n";
    #echo "sql=[".$sql."]<br>\n";

    # Running row count sql statement
    $countResult = $this->_conn->exec($sqlcount);
    if ($countResult == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error counting bibliography search results.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    # Calculate stats based on row count
    $array = $this->_conn->fetchRow();
    $this->_rowCount = $array["rowcount"];
    $this->_pageCount = ceil($this->_rowCount / $this->_itemsPerPage);

    # Running search sql statement
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error searching bibliography information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    return $result;
  }

  /****************************************************************************
   * Executes a query
   * @param string $bibid bibid of bibliography to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($bibid) {
    # reset rowNmbr
    $this->_rowNmbr = 0;
    $this->_currentRowNmbr = 1;
    $this->_rowCount = 1;
    $this->_pageCount = 1;

    # setting query that will return all the data
    $sql = "select biblio.*, ";
    $sql = $sql."biblio_status.status_cd, ";
    $sql = $sql."biblio_status.mbrid status_mbrid, ";
    $sql = $sql."biblio_status.due_back_dt, ";
    $sql = $sql."biblio_hold.mbrid hold_mbrid ";
    $sql = $sql."from biblio ";
    $sql = $sql."left join biblio_status on biblio.bibid=biblio_status.bibid ";
    $sql = $sql."left join biblio_hold on biblio.bibid=biblio_hold.bibid ";
    $sql = $sql."where biblio.bibid = ".$bibid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing bibliography information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }
  /****************************************************************************
   * Fetches a row from the query result and populates the Biblio object.
   * @return Biblio returns bibliography or false if no more bibliographies to fetch
   * @access public
   ****************************************************************************
   */
  function fetchBiblio() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    # increment rowNmbr
    $this->_rowNmbr = $this->_rowNmbr + 1;
    $this->_currentRowNmbr = $this->_rowNmbr + (($this->_currentPageNmbr - 1) * $this->_itemsPerPage);

    $bib = new Biblio();
    $bib->setBibid($array["bibid"]);
    $bib->setBarcodeNmbr($array["barcode_nmbr"]);
    $bib->setCreateDt($array["create_dt"]);
    $bib->setLastUpdatedDt($array["last_updated_dt"]);
    $bib->setMaterialCd($array["material_cd"]);
    $bib->setCollectionCd($array["collection_cd"]);
    $bib->setTitle($array["title"]);
    $bib->setSubtitle($array["subtitle"]);
    $bib->setAuthor($array["author"]);
    $bib->setAddAuthor($array["add_author"]);
    $bib->setEdition($array["edition"]);
    $bib->setCallNmbr($array["call_nmbr"]);
    $bib->setLccnNmbr($array["lccn_nmbr"]);
    $bib->setIsbnNmbr($array["isbn_nmbr"]);
    $bib->setLcCallNmbr($array["lc_call_nmbr"]);
    $bib->setLcItemNmbr($array["lc_item_nmbr"]);
    $bib->setUdcNmbr($array["udc_nmbr"]);
    $bib->setUdcEdNmbr($array["udc_ed_nmbr"]);
    $bib->setPublisher($array["publisher"]);
    $bib->setPublicationDt($array["publication_dt"]);
    $bib->setPublicationLoc($array["publication_loc"]);
    $bib->setSummary($array["summary"]);
    $bib->setPages($array["pages"]);
    $bib->setPhysicalDetails($array["physical_details"]);
    $bib->setDimensions($array["dimensions"]);
    $bib->setAccompanying($array["accompanying"]);
    $bib->setPrice($array["price"]);
    $bib->setStatusCd($array["status_cd"]);
    $bib->setStatusMbrid($array["status_mbrid"]);
    $bib->setDueBackDt($array["due_back_dt"]);
    if (isset($array["hold_mbrid"])) {
      $bib->setHoldMbrid($array["hold_mbrid"]);
    }
    return $bib;
  }

  /****************************************************************************
   * Returns true if barcode number already exists
   * @param string $barcode Bibliography barcode number
   * @param string $bibid Bibliography id
   * @return boolean returns true if barcode already exists
   * @access private
   ****************************************************************************
   */
  function _dupBarcode($barcode, $bibid=0) {
    $sql = "select count(*) from biblio where barcode_nmbr = '".$barcode."'";
    $sql = $sql." and bibid <> ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error checking for dup barcode.";
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
   * Inserts a new bibliography into the biblio table.
   * @param Biblio $biblio bibliography to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($biblio) {
    # checking for duplicate barcode number
    $dupBarcode = $this->_dupBarcode($biblio->getBarcodeNmbr());
    if ($this->errorOccurred()) return false;
    if ($dupBarcode) {
      $this->_errorOccurred = true;
      $this->_error = "Barcode number ".$biblio->getBarcodeNmbr()." is already in use.";
      return false;
    }

    $sql = "insert into biblio values (null, ";
    $sql = $sql.$biblio->getBarcodeNmbr().", ";
    $sql = $sql."curdate(), curdate(), ";
    $sql = $sql.$biblio->getMaterialCd().", ";
    $sql = $sql.$biblio->getCollectionCd().", ";
    $sql = $sql."'".$biblio->getTitle()."', ";
    $sql = $sql."'".$biblio->getSubtitle()."', ";
    $sql = $sql."'".$biblio->getAuthor()."', ";
    $sql = $sql."'".$biblio->getAddAuthor()."', ";
    $sql = $sql."'".$biblio->getEdition()."', ";
    $sql = $sql."'".$biblio->getCallNmbr()."', ";
    $sql = $sql."'".$biblio->getLccnNmbr()."', ";
    $sql = $sql."'".$biblio->getIsbnNmbr()."', ";
    $sql = $sql."'".$biblio->getLcCallNmbr()."', ";
    $sql = $sql."'".$biblio->getLcItemNmbr()."', ";
    $sql = $sql."'".$biblio->getUdcNmbr()."', ";
    $sql = $sql."'".$biblio->getUdcEdNmbr()."', ";
    $sql = $sql."'".$biblio->getPublisher()."', ";
    $sql = $sql."'".$biblio->getPublicationDt()."', ";
    $sql = $sql."'".$biblio->getPublicationLoc()."', ";
    $sql = $sql."'".$biblio->getSummary()."', ";
    $sql = $sql."'".$biblio->getPages()."', ";
    $sql = $sql."'".$biblio->getPhysicalDetails()."', ";
    $sql = $sql."'".$biblio->getDimensions()."', ";
    $sql = $sql."'".$biblio->getAccompanying()."', ";
    $sql = $sql."'".$biblio->getPrice()."')";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error inserting new bibliography information.";
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
  function update($biblio) {
    # checking for duplicate barcode number
    $dupBarcode = $this->_dupBarcode($biblio->getBarcodeNmbr(), $biblio->getBibid());
    if ($this->errorOccurred()) return false;
    if ($dupBarcode) {
      $this->_errorOccurred = true;
      $this->_error = "Barcode number ".$biblio->getBarcodeNmbr()." is already in use.";
      return false;
    }
    $sql = "update biblio set last_updated_dt = curdate(), ";
    $sql = $sql."barcode_nmbr=".$biblio->getBarcodeNmbr().", ";
    $sql = $sql."material_cd=".$biblio->getMaterialCd().", ";
    $sql = $sql."collection_cd=".$biblio->getCollectionCd().", ";
    $sql = $sql."title='".$biblio->getTitle()."', ";
    $sql = $sql."subtitle='".$biblio->getSubtitle()."', ";
    $sql = $sql."author='".$biblio->getAuthor()."', ";
    $sql = $sql."add_author='".$biblio->getAddAuthor()."', ";
    $sql = $sql."edition='".$biblio->getEdition()."', ";
    $sql = $sql."call_nmbr='".$biblio->getCallNmbr()."', ";
    $sql = $sql."lccn_nmbr='".$biblio->getLccnNmbr()."', ";
    $sql = $sql."isbn_nmbr='".$biblio->getIsbnNmbr()."', ";
    $sql = $sql."lc_call_nmbr='".$biblio->getLcCallNmbr()."', ";
    $sql = $sql."lc_item_nmbr='".$biblio->getLcItemNmbr()."', ";
    $sql = $sql."udc_nmbr='".$biblio->getUdcNmbr()."', ";
    $sql = $sql."udc_ed_nmbr='".$biblio->getUdcEdNmbr()."', ";
    $sql = $sql."publisher='".$biblio->getPublisher()."', ";
    $sql = $sql."publication_dt='".$biblio->getPublicationDt()."', ";
    $sql = $sql."publication_loc='".$biblio->getPublicationLoc()."', ";
    $sql = $sql."summary='".$biblio->getSummary()."', ";
    $sql = $sql."pages='".$biblio->getPages()."', ";
    $sql = $sql."physical_details='".$biblio->getPhysicalDetails()."', ";
    $sql = $sql."dimensions='".$biblio->getDimensions()."', ";
    $sql = $sql."accompanying='".$biblio->getAccompanying()."', ";
    $sql = $sql."price=".$biblio->getPrice()." ";
    $sql = $sql."where bibid=".$biblio->getBibid();

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating bibliography information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes a bibliography from the biblio table.
   * @param string $bibid bibliography id of bibliography to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($bibid) {
    $sql = "delete from biblio_topic where bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting bibliography information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $sql = "delete from biblio where bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting bibliography information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
