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
    $sql = $this->mkSQL("select * from %I ", $table);
    if ($code != "") {
      $sql .= $this->mkSQL("where code = %N ", $code);
    }
    $sql .= "order by description ";
    return $this->_query($sql, "Error accessing the ".$table." domain table.");
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
      $sql .= "from collection_dm left join biblio on collection_dm.code = biblio.collection_cd ";
      $sql .= "group by 1, 2, 3, 4, 5 ";
    } elseif ($table == "material_type_dm") {
      $sql = "select material_type_dm.*, count(biblio.bibid) row_count ";
      $sql .= "from material_type_dm left join biblio on material_type_dm.code = biblio.material_cd ";
      $sql .= "group by 1, 2, 3, 4, 5, 6 ";
    } else {
      $this->_errorOccurred = true;
      $this->_error = "Can only retrieve stats on collections and material types.";
      return false;
    }
    $sql .= "order by description ";
    return $this->_query($sql, "Error accessing the ".$table." domain table.");
  }

  /****************************************************************************
   * Retrieves checkout stats for a particular member.
   * @param string $mbrid Member id of library member to select
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function execCheckoutStats($mbrid) {
    $sql = $this->mkSQL("select mat.*, count(copy.mbrid) row_count "
                        . "from material_type_dm mat "
                        . " left outer join biblio bib on mat.code = bib.material_cd "
                        . " left outer join biblio_copy copy on bib.bibid = copy.bibid "
                        . "where copy.mbrid = %N or copy.mbrid is null "
                        . "group by mat.code, mat.description, mat.default_flg, "
                        . " mat.adult_checkout_limit, mat.juvenile_checkout_limit "
                        . "order by mat.description ", $mbrid);
    if (!$this->_query($sql, "Error accessing library member information.")) {
      return false;
    }
    $this->_tableNm = "material_type_dm";
    return true;
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
      $dm->setDailyLateFee($array["daily_late_fee"]);
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
    $sql .= $this->mkSQL("insert into %I values (null, %Q, 'N', ",
                         $table, $dm->getDescription());
    if ($table == "collection_dm") {
      $sql .= $this->mkSQL("%N, %N)", $dm->getDaysDueBack(), $dm->getDailyLateFee());
    } elseif ($table == "material_type_dm") {
      $sql .= $this->mkSQL("%N, %N, %Q)", $dm->getAdultCheckoutLimit(),
                           $dm->getJuvenileCheckoutLimit(), $dm->getImageFile());
    } else {
      $this->_errorOccurred = true;
      $this->_error = "Can only insert rows on collections and material types.";
      return false;
    }

    # running sql
    return $this->_query($sql, "Error inserting into ".$table);
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
    $sql = $this->mkSQL("update %I set description=%Q, default_flg='N', ",
                         $table, $dm->getDescription());
    if ($table == "collection_dm") {
      $sql .= $this->mkSQL("days_due_back=%N, daily_late_fee=%N ",
                           $dm->getDaysDueBack(), $dm->getDailyLateFee());
    } elseif ($table == "material_type_dm") {
      $sql .= $this->mkSQL("adult_checkout_limit=%N, "
                          . "juvenile_checkout_limit=%N, "
                          . "image_file=%Q ",
                          $dm->getAdultCheckoutLimit(),
                          $dm->getJuvenileCheckoutLimit(),
                          $dm->getImageFile());
    } else {
      $this->_errorOccurred = true;
      $this->_error = "Can only update rows on collections and material types.";
      return false;
    }
    $sql .= $this->mkSQL("where code=%N ", $dm->getCode());
    return $this->_query($sql, "Error updating ".$table);
  }

  /****************************************************************************
   * Deletes a row from a domain table.
   * @param string $mbrid Member id of library member to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($table, $code) {
    $sql = $this->mkSQL("delete from %I where code = %N", $table, $code);
    return $this->_query($sql, "Error deleting from ".$table);
  }

}

?>
