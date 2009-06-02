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
 * StaffQuery data access component for library staff members
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class StaffQuery extends Query {
  /****************************************************************************
   * Executes a query
   * @param string $userid (optional) userid of staff member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($userid="") {
    $sql = "select userid, last_name, first_name, username, circ_flg, catalog_flg, admin_flg, suspended_flg from staff";
    if ($userid != "") {
      $sql = $sql." where userid=".$userid;
    }
    $sql = $sql." order by last_name, first_name";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing staff member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }
  /****************************************************************************
   * Executes a query to verify a signon username and password
   * @param string $username username of staff member to select
   * @param string $pwd password of staff member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function verifySignon($username, $pwd) {
    $sql = "select userid, last_name, first_name, username, circ_flg, catalog_flg, admin_flg, suspended_flg from staff";
    $sql = $sql." where username = lower('".$username."') and pwd = password(lower('".$pwd."'))";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error verifying username and password.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Updates a staff member and sets the suspended flag to yes.
   * @param string $username username of staff member to suspend
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function suspendStaff($username)
  {
    $sql = "update staff set suspended_flg='Y' where username = lower('".$username."')";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error suspending staff member.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Staff object.
   * @return Staff returns staff member or false if no more staff members to fetch
   * @access public
   ****************************************************************************
   */
  function fetchStaff() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }
    $staff = new Staff();
    $staff->setUserid($array["userid"]);
    $staff->setLastName($array["last_name"]);
    $staff->setFirstName($array["first_name"]);
    $staff->setUsername($array["username"]);
    if ($array["circ_flg"] == "Y") {
      $staff->setCircAuth(true);
    } else {
      $staff->setCircAuth(false);
    }
    if ($array["catalog_flg"] == "Y") {
      $staff->setCatalogAuth(true);
    } else {
      $staff->setCatalogAuth(false);
    }
    if ($array["admin_flg"] == "Y") {
      $staff->setAdminAuth(true);
    } else {
      $staff->setAdminAuth(false);
    }
    if ($array["suspended_flg"] == "Y") {
      $staff->setSuspended(true);
    } else {
      $staff->setSuspended(false);
    }
    return $staff;
  }

  /****************************************************************************
   * Returns true if username already exists
   * @param string $username staff member username
   * @param string $userid staff member userid
   * @return boolean returns true if username already exists
   * @access private
   ****************************************************************************
   */
  function _dupUserName($username, $userid=0) {
    $sql = "select count(*) from staff where username = '".$username."'";
    $sql = $sql." and userid <> ".$userid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error checking for dup username.";
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
   * Inserts a new staff member into the staff table.
   * @param Staff $staff staff member to insert
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($staff) {
    $dupUsername = $this->_dupUserName($staff->getUsername());
    if ($this->errorOccurred()) return false;
    if ($dupUsername) {
      $this->_errorOccurred = true;
      $this->_error = "Username is already in use.";
      return false;
    }
    $sql = "insert into staff values (null, curdate(), curdate(), ";
    $sql = $sql."'".$staff->getUsername()."', ";
    $sql = $sql."password('".$staff->getPwd()."'), ";
    $sql = $sql."'".$staff->getLastName()."', ";
    if ($staff->getFirstName() == "") {
      $sql = $sql."null, ";
    } else {
      $sql = $sql."'".$staff->getFirstName()."', ";
    }
    $sql = $sql."'N', ";
    if ($staff->hasAdminAuth()) {
      $sql = $sql."'Y', ";
    } else {
      $sql = $sql."'N', ";
    }
    if ($staff->hasCircAuth()) {
      $sql = $sql."'Y', ";
    } else {
      $sql = $sql."'N', ";
    }
    if ($staff->hasCatalogAuth()) {
      $sql = $sql."'Y')";
    } else {
      $sql = $sql."'N')";
    }
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error inserting new staff member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Update a staff member in the staff table.
   * @param Staff $staff staff member to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($staff) {
    /**************************************************************
     * If changing username check to see if it already exists. 
     **************************************************************/
    $dupUsername = $this->_dupUserName($staff->getUsername(), $staff->getUserid());
    if ($this->errorOccurred()) return false;
    if ($dupUsername) {
      $this->_errorOccurred = true;
      $this->_error = "Username is already in use.";
      return false;
    }

    $sql = "update staff set last_updated_dt = curdate(),";
    $sql = $sql." username='".$staff->getUsername()."',";
    $sql = $sql." last_name='".$staff->getLastName()."',";
    if ($staff->getFirstName() == "") {
      $sql = $sql." first_name=null,";
    } else {
      $sql = $sql." first_name='".$staff->getFirstName()."',";
    }
    if ($staff->isSuspended()) {
      $sql = $sql." suspended_flg='Y',";
    } else {
      $sql = $sql." suspended_flg='N',";
    }
    if ($staff->hasAdminAuth()) {
      $sql = $sql." admin_flg='Y',";
    } else {
      $sql = $sql." admin_flg='N',";
    }
    if ($staff->hasCircAuth()) {
      $sql = $sql." circ_flg='Y',";
    } else {
      $sql = $sql." circ_flg='N',";
    }
    if ($staff->hasCatalogAuth()) {
      $sql = $sql." catalog_flg='Y'";
    } else {
      $sql = $sql." catalog_flg='N'";
    }
    $sql = $sql." where userid = ".$staff->getUserid();
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating staff member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Resets a staff member password in the staff table.
   * @param Staff $staff staff member to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function resetPwd($staff) {
    $sql = "update staff set pwd = password('".$staff->getPwd()."')";
    $sql = $sql." where userid = ".$staff->getUserid();
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error reseting password.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes a staff member from the staff table.
   * @param string $userid userid of staff member to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($userid) {
    $sql = "delete from staff where userid = ".$userid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting staff information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
