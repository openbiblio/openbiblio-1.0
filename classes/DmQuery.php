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
 * DmQuery data access component for domain tables
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class DmQuery extends Query {
  var $_tableNm = "";

  /****************************************************************************
   * Executes a query
   * @param string $table table name of domain table to query
   * @param int $code (optional) code of row to fetch
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelect($table, $code = "") {
    $this->_tableNm = $table;
    $sql = "select * from ".$table." ";
    if ($code != "") {
      $sql = $sql."where code = ".$code." ";
    }
    $sql = $sql."order by description ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing the ".$table." domain table.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Executes a query
   * @param string $table table name of domain table to query
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execSelectWithStats($table) {
    $this->_tableNm = $table;
    if ($table == "collection_dm") {
      $sql = "select collection_dm.*, count(biblio.bibid) row_count ";
      $sql = $sql."from collection_dm left join biblio on collection_dm.code = biblio.collection_cd ";
      $sql = $sql."group by 1, 2, 3, 4 ";
    } elseif ($table == "material_type_dm") {
      $sql = "select material_type_dm.*, count(biblio.bibid) row_count ";
      $sql = $sql."from material_type_dm left join biblio on material_type_dm.code = biblio.material_cd ";
      $sql = $sql."group by 1, 2, 3, 4, 5 ";
    } else {
      $this->_errorOccurred = true;
      $this->_error = "Can only retrieve stats on collections and material types.";
      return false;
    }
    $sql = $sql."order by description ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing the ".$table." domain table.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Retrieves checkout stats for a particular member.
   * @param string $mbrid Member id of library member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execCheckoutStats($mbrid) {
    $sql = "select mat.* ";
    $sql = $sql.",count(stat.mbrid) row_count ";
    $sql = $sql."from material_type_dm mat left outer join biblio bib on mat.code = bib.material_cd ";
    $sql = $sql."left outer join biblio_status stat on bib.bibid = stat.bibid ";
    $sql = $sql."where stat.mbrid = ".$mbrid." or stat.mbrid is null ";
    $sql = $sql."group by mat.code, mat.description, mat.default_flg, mat.adult_checkout_limit, mat.juvenile_checkout_limit ";
    $sql = $sql."order by mat.description ";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error accessing library member information.";
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_tableNm = "material_type_dm";
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the Dm object.
   * @return Dm returns domain object or false if no more domain rows to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }
    $dm = new Dm();
    $dm->setCode($array["code"]);
    $dm->setDescription($array["description"]);
    $dm->setDefaultFlg($array["default_flg"]);
    if ($this->_tableNm == "collection_dm") {
      $dm->setDaysDueBack($array["days_due_back"]);
    } elseif ($this->_tableNm == "material_type_dm") {
      $dm->setAdultCheckoutLimit($array["adult_checkout_limit"]);
      $dm->setJuvenileCheckoutLimit($array["juvenile_checkout_limit"]);
      $dm->setImageFile($array["image_file"]);
    }
    if (isset($array["row_count"])) {
      $dm->setCount($array["row_count"]);
    }
    return $dm;
  }

  /****************************************************************************
   * Fetches all rows from the query result.
   * @return assocArray returns associative array containing domain codes and values.
   * @access public
   ****************************************************************************
   */
  function fetchRows($col="") {
    if ($col == "") $col = "description";
    while ($result = $this->_conn->fetchRow()) {
      $assoc[$result["code"]] = $result[$col];
    }
    return $assoc;
  }

  /****************************************************************************
   * Inserts a new domain table row.
   * @param string $table table name of domain table to query
   * @param Dm $dm domain object
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insert($table, $dm) {
    # constructing sql
    $sql = "insert into ".$table." values (null, ";
    $sql = $sql."'".$dm->getDescription()."','N', ";
    if ($table == "collection_dm") {
      $sql = $sql.$dm->getDaysDueBack().")";
    } elseif ($table == "material_type_dm") {
      $sql = $sql.$dm->getAdultCheckoutLimit().", ";
      $sql = $sql.$dm->getJuvenileCheckoutLimit().", ";
      $sql = $sql."'".$dm->getImageFile()."')";
    } else {
      $this->_errorOccurred = true;
      $this->_error = "Can only insert rows on collections and material types.";
      return false;
    }

    # running sql
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error inserting into ".$table;
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Update a row in a domain table.
   * @param string $table table name of domain table to query
   * @param Staff $staff staff member to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function update($table, $dm) {
    $sql = "update ".$table." set ";
    $sql = $sql."description='".$dm->getDescription()."', ";
    $sql = $sql." default_flg = 'N', ";
    if ($table == "collection_dm") {
      $sql = $sql." days_due_back = ".$dm->getDaysDueBack()." ";
    } elseif ($table == "material_type_dm") {
      $sql = $sql." adult_checkout_limit = ".$dm->getAdultCheckoutLimit().", ";
      $sql = $sql." juvenile_checkout_limit = ".$dm->getJuvenileCheckoutLimit().", ";
      $sql = $sql." image_file = '".$dm->getImageFile()."' ";
    } else {
      $this->_errorOccurred = true;
      $this->_error = "Can only update rows on collections and material types.";
      return false;
    }
    $sql = $sql."where code = ".$dm->getCode();
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error updating ".$table;
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

  /****************************************************************************
   * Deletes a row from a domain table.
   * @param string $mbrid Member id of library member to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($table, $code) {
    $sql = "delete from ".$table." where code = ".$code;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = "Error deleting from ".$table;
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
