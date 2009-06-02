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
    $sql = $this->mkSQL("from member where %C like %Q ", $col, $word."%");
    $sqlcount = "select count(*) as rowcount ".$sql;
    $sql = "select * ".$sql;
    $sql .= " order by last_name, first_name";
    # setting limit so we can page through the results
    $offset = ($page - 1) * $this->_itemsPerPage;
    $limit = $this->_itemsPerPage;
    $sql .= $this->mkSQL(" limit %N, %N ", $offset, $limit);
    #echo "sql=[".$sql."]<br>\n";

    # Running row count sql statement
    if (!$this->_query($sqlcount, "Error counting library member search results.")) {
      return false;
    }

    # Calculate stats based on row count
    $array = $this->_conn->fetchRow();
    $this->_rowCount = $array["rowcount"];
    $this->_pageCount = ceil($this->_rowCount / $this->_itemsPerPage);

    # Running search sql statement
    return $this->_query($sql, "Error searching library member information.");
  }

  /****************************************************************************
   * Executes a query
   * @param string $mbrid Member id of library member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($mbrid) {
    $sql = $this->mkSQL("select member.*, staff.username from member "
                        . "left join staff on member.last_change_userid = staff.userid "
                        . "where mbrid=%N ", $mbrid);
    return $this->_query($sql, "Error accessing library member information.");
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
    $mbr->setLastChangeDt($array["last_change_dt"]);
    $mbr->setLastChangeUserid($array["last_change_userid"]);
    if (isset($array["username"])) {
      $mbr->setLastChangeUsername($array["username"]);
    }
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
    $mbr->setEmail($array["email"]);
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
  function DupBarcode($barcode, $mbrid=0) {
    $sql = $this->mkSQL("select count(*) from member "
                        . "where barcode_nmbr = %Q and mbrid <> %N",
                        $barcode, $mbrid);
    if (!$this->_query($sql, "Error checking for dup barcode.")) {
      return false;
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
    $sql = $this->mkSQL("insert into member "
                        . "values (null, %Q, sysdate(), sysdate(), %N, %Q, %Q, "
                        . " %Q, %Q, %Q, %Q, %N, %N, %Q, %Q, %Q, %Q, %Q, %Q) ",
                        $mbr->getBarcodeNmbr(), $mbr->getLastChangeUserid(),
                        $mbr->getLastName(), $mbr->getFirstName(),
                        $mbr->getAddress1(), $mbr->getAddress2(),
                        $mbr->getCity(), $mbr->getState(),
                        $mbr->getZip(), $mbr->getZipExt(),
                        $mbr->getHomePhone(), $mbr->getWorkPhone(),
                        $mbr->getEmail(), $mbr->getClassification(),
                        $mbr->getSchoolGrade(), $mbr->getSchoolTeacher());

    if (!$this->_query($sql, "Error inserting new library member information.")) {
      return false;
    }
    $mbrid = $this->_conn->getInsertId();
    return $mbrid;
  }

  /****************************************************************************
   * Update a library member in the member table.
   * @param Member $mbr library member to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($mbr) {
    $sql = $this->mkSQL("update member set "
                        . " last_change_dt = sysdate(), last_change_userid=%N, "
                        . " barcode_nmbr=%Q,  last_name=%Q,  first_name=%Q, "
                        . " address1=%Q, address2=%Q, city=%Q, state=%Q, "
                        . " zip=%N, zip_ext=%N, home_phone=%Q, work_phone=%Q, "
                        . " email=%Q, classification=%Q, school_grade=%Q, "
                        . " school_teacher=%Q "
                        . "where mbrid=%N",
                        $mbr->getLastChangeUserid(), $mbr->getBarcodeNmbr(),
                        $mbr->getLastName(), $mbr->getFirstName(),
                        $mbr->getAddress1(), $mbr->getAddress2(),
                        $mbr->getCity(), $mbr->getState(),
                        $mbr->getZip(), $mbr->getZipExt(),
                        $mbr->getHomePhone(), $mbr->getWorkPhone(),
                        $mbr->getEmail(), $mbr->getClassification(),
                        $mbr->getSchoolGrade(), $mbr->getSchoolTeacher(),
                        $mbr->getMbrid());

    return $this->_query($sql, "Error updating library member information.");
  }

  /****************************************************************************
   * Deletes a library member from the member table.
   * @param string $mbrid Member id of library member to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($mbrid) {
    $sql = $this->mkSQL("delete from member where mbrid = %N ", $mbrid);
    return $this->_query($sql, "Error deleting library member information.");
  }

}

?>
