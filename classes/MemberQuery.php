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
 * MemberQuery data access component for library members
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class MemberQuery extends Query {
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
   *               OBIB_SEARCH_BARCODE or OBIB_SEARCH_NAME
   * @param string $word String to search for
   * @param integer $page What page should be returned if results are more than one page
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSearch($type, $word, $page) {
    # reset stats
    $this->_rowNmbr = 0;
    $this->_currentRowNmbr = 0;
    $this->_currentPageNmbr = $page;
    $this->_rowCount = 0;
    $this->_pageCount = 0;

    # Building sql statements
    if ($type == OBIB_SEARCH_BARCODE) {
      $col = "barcode_nmbr";
    } elseif ($type == OBIB_SEARCH_NAME) {
      $col = "last_name";
    }

    # Building sql statements
    $sql = "from member where ".$col." like '".$word."%'";
    $sqlcount = "select count(*) as rowcount ".$sql;
    $sql = "select * ".$sql;
    $sql = $sql." order by last_name, first_name";
    # setting limit so we can page through the results
    $offset = ($page - 1) * $this->_itemsPerPage;
    $limit = $this->_itemsPerPage;
    $sql = $sql." limit ".$offset.",".$limit;
    #echo "sql=[".$sql."]<br>\n";

    # Running row count sql statement
    $countResult = $this->_conn->exec($sqlcount);
    if ($countResult == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error counting library member search results.";
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
      $this->_error = "Error searching library member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    return $result;
  }

  /****************************************************************************
   * Executes a query
   * @param string $mbrid Member id of library member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($mbrid) {
    $sql = "select * from member";
    $sql = $sql." where mbrid=".$mbrid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing library member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Member object.
   * @return Member returns library member or false if no more members to fetch
   * @access public
   ****************************************************************************
   */
  function fetchMember() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    # increment rowNmbr
    $this->_rowNmbr = $this->_rowNmbr + 1;
    $this->_currentRowNmbr = $this->_rowNmbr + (($this->_currentPageNmbr - 1) * $this->_itemsPerPage);

    $mbr = new Member();
    $mbr->setMbrid($array["mbrid"]);
    $mbr->setBarcodeNmbr($array["barcode_nmbr"]);
    $mbr->setLastName($array["last_name"]);
    $mbr->setFirstName($array["first_name"]);
    $mbr->setAddress1($array["address1"]);
    $mbr->setAddress2($array["address2"]);
    $mbr->setCity($array["city"]);
    $mbr->setState($array["state"]);
    $mbr->setZip($array["zip"]);
    $mbr->setZipExt($array["zip_ext"]);
    $mbr->setHomePhone($array["home_phone"]);
    $mbr->setWorkPhone($array["work_phone"]);
    $mbr->setClassification($array["classification"]);
    $mbr->setSchoolGrade($array["school_grade"]);
    $mbr->setSchoolTeacher($array["school_teacher"]);

    return $mbr;
  }

  /****************************************************************************
   * Returns true if barcode number already exists
   * @param string $barcode Library member barcode number
   * @param string $mbrid Library member id
   * @return boolean returns true if barcode already exists
   * @access private
   ****************************************************************************
   */
  function _dupBarcode($barcode, $mbrid=0) {
    $sql = "select count(*) from member where barcode_nmbr = '".$barcode."'";
    $sql = $sql." and mbrid <> ".$mbrid;
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
   * Inserts a new library member into the member table.
   * @param Member $mbr library member to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($mbr) {
    $dupBarcode = $this->_dupBarcode($mbr->getBarcodeNmbr());
    if ($this->errorOccurred()) return false;
    if ($dupBarcode) {
      $this->_errorOccurred = true;
      $this->_error = "Barcode number ".$mbr->getBarcodeNmbr()." is already in use.";
      return false;
    }

    $sql = "insert into member values (null, ";
    $sql = $sql.$mbr->getBarcodeNmbr().", ";
    $sql = $sql."curdate(), curdate(), ";
    $sql = $sql."'".$mbr->getLastName()."', ";
    $sql = $sql."'".$mbr->getFirstName()."', ";
    $sql = $sql."'".$mbr->getAddress1()."', ";
    $sql = $sql."'".$mbr->getAddress2()."', ";
    $sql = $sql."'".$mbr->getCity()."', ";
    $sql = $sql."'".$mbr->getState()."', ";
    $sql = $sql.$mbr->getZip().", ";
    $sql = $sql.$mbr->getZipExt().", ";
    $sql = $sql."'".$mbr->getHomePhone()."', ";
    $sql = $sql."'".$mbr->getWorkPhone()."', ";
    $sql = $sql."'".$mbr->getClassification()."', ";
    $sql = $sql."'".$mbr->getSchoolGrade()."', ";
    $sql = $sql."'".$mbr->getSchoolTeacher()."')";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error inserting new library member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Update a library member in the member table.
   * @param Member $mbr library member to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($mbr) {
    $dupBarcode = $this->_dupBarcode($mbr->getBarcodeNmbr(),$mbr->getMbrid());
    if ($this->errorOccurred()) return false;
    if ($dupBarcode) {
      $this->_errorOccurred = true;
      $this->_error = "Barcode number ".$mbr->getBarcodeNmbr()." is already in use.";
      return false;
    }

    $sql = "update member set barcode_nmbr=".$mbr->getBarcodeNmbr().", ";
    $sql = $sql."last_updated_dt = curdate(),";
    $sql = $sql."barcode_nmbr=".$mbr->getBarcodeNmbr().", ";
    $sql = $sql."last_name='".$mbr->getLastName()."', ";
    $sql = $sql."first_name='".$mbr->getFirstName()."', ";
    $sql = $sql."address1='".$mbr->getAddress1()."', ";
    $sql = $sql."address2='".$mbr->getAddress2()."', ";
    $sql = $sql."city='".$mbr->getCity()."', ";
    $sql = $sql."state='".$mbr->getState()."', ";
    $sql = $sql."zip=".$mbr->getZip().", ";
    $sql = $sql."zip_ext=".$mbr->getZipExt().", ";
    $sql = $sql."home_phone='".$mbr->getHomePhone()."', ";
    $sql = $sql."work_phone='".$mbr->getWorkPhone()."', ";
    $sql = $sql."classification='".$mbr->getClassification()."', ";
    $sql = $sql."school_grade='".$mbr->getSchoolGrade()."', ";
    $sql = $sql."school_teacher='".$mbr->getSchoolTeacher()."' ";
    $sql = $sql."where mbrid=".$mbr->getMbrid();

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating library member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes a library member from the member table.
   * @param string $mbrid Member id of library member to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($mbrid) {
    $sql = "delete from member where mbrid = ".$mbrid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting library member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
