<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Query.php"));
require_once(REL(__FILE__, "../classes/MemberAccountTransaction.php"));

/******************************************************************************
 * MemberAccountQuery data access component for member account transactions
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class MemberAccountQuery extends Query {
  var $_rowCount = 0;

  function MemberAccountQuery () {
    $this->Query();
  }

  function getRowCount() {
    return $this->_rowCount;
  }

  /****************************************************************************
   * Gets all of a member's transactions
   * @param string $mbrid mbrid of member
   * @return array of MemberAccountTransaction
   * @access public
   ****************************************************************************
   */
  function getByMbrid($mbrid) {
    # setting query that will return all the data
    $sql = $this->mkSQL("select member_account.*, "
                        . " transaction_type_dm.description transaction_type_desc "
                        . "from member_account, transaction_type_dm "
                        . "where member_account.transaction_type_cd = transaction_type_dm.code "
                        . " and member_account.mbrid = %N "
                        . "order by create_dt ", $mbrid);
    $rows = $this->eexec($sql);
    return array_map(array($this, '_mkObj'), $rows);
  }

  /****************************************************************************
   * Executes a query to select account information
   * @param string $mbrid mbrid of member
   * @return decimal returns balance due
   * @access public
   ****************************************************************************
   */
  function getBalance($mbrid) {
    # setting query that will return all the data
    $sql = $this->mkSQL("select sum(member_account.amount) balance "
                        . "from member_account "
                        . "where member_account.mbrid = %N ", $mbrid);
    $rows = $this->eexec($sql);
    assert('count($rows) == 1');
    return $rows[0]['balance'];
  }

  function _mkObj($row) {
    $trans = new MemberAccountTransaction();
    $trans->setMbrid($row["mbrid"]);
    $trans->setTransid($row["transid"]);
    $trans->setCreateDt($row["create_dt"]);
    $trans->setCreateUserid($row["create_userid"]);
    $trans->setTransactionTypeCd($row["transaction_type_cd"]);
    $trans->setTransactionTypeDesc($row["transaction_type_desc"]);
    $trans->setAmount($row["amount"]);
    $trans->setDescription($row["description"]);
    return $trans;
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
    return $this->_mkObj($array);
  }

  /****************************************************************************
   * Inserts a new account transaction into the member_account table.
   * @param MemberAccountTransaction $trans account transaction to insert
   * @access public
   ****************************************************************************
   */
  function insert($trans) {
    $this->lock();
    // change trans type payment and credit amount to negative
    $transTypeSign = substr($trans->getTransactionTypeCd(),0,1);
    if ($transTypeSign == "-") {
      $amt = $trans->getAmount() * -1;
    } else {
      $amt = $trans->getAmount();
    }
    $sql = $this->mkSQL("insert into member_account "
                        . "values (%N, null, sysdate(), %N, %Q, %N, %Q) ",
                        $trans->getMbrid(), $trans->getCreateUserid(),
                        $trans->getTransactionTypeCd(), $amt,
                        $trans->getDescription());
    $r = $this->_query($sql, T("Error inserting member account information."));
    $this->unlock();
    return $r;
  }

  /****************************************************************************
   * Deletes history from the biblio_status_hist table.
   * @param string $mbrid member id of history to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($mbrid,$tranid="") {
    $this->lock();
    $sql = $this->mkSQL("delete from member_account where mbrid = %N ", $mbrid);
    if ($tranid != "") {
      $sql .= $this->mkSQL(" and transid = %N ", $tranid);
    }
    $r = $this->_query($sql, T("Error deleting member account information."));
    $this->unlock();
    return $r;
  }


}
