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
require_once("../classes/BiblioField.php");
require_once("../classes/Localize.php");

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
  var $_loc;
  var $_fieldsInBiblio;

  function BiblioQuery () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
    $this->_fieldsInBiblio = array(
      '100a' => 'author',
      '245a' => 'title',
      '245b' => 'title_remainder',
      '245c' => 'responsibility_stmt',
      '650a' => 'topic1',
      '650a1' => 'topic2',
      '650a2' => 'topic3',
      '650a3' => 'topic4',
      '650a4' => 'topic5',
    );
  }

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
   * @param string $bibid bibid of bibliography to select
   * @return Biblio a bibliography object or false if error occurs
   * @access public
   ****************************************************************************
   */
  /*###########################
    # WORKS WITH NEW FORMAT   #
    ###########################*/
  function query($bibid) {
    # reset rowNmbr
    $this->_rowNmbr = 0;
    $this->_currentRowNmbr = 1;
    $this->_rowCount = 1;
    $this->_pageCount = 1;

    /***********************************************************
     *  Reading biblio data
     ***********************************************************/
    # setting query that will return all the data in biblio
    $sql = "select biblio.*, ";
    $sql = $sql."staff.username ";
    $sql = $sql."from biblio ";
    $sql = $sql." left join staff on biblio.last_change_userid = staff.userid ";
    $sql = $sql."where biblio.bibid = ".$bibid;

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioQueryQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    $array = $this->_conn->fetchRow();
    $bib = new Biblio();
    $bib->setBibid($array["bibid"]);
    $bib->setCreateDt($array["create_dt"]);
    $bib->setLastChangeDt($array["last_change_dt"]);
    $bib->setLastChangeUserid($array["last_change_userid"]);
    if (isset($array["username"])) {
      $bib->setLastChangeUsername($array["username"]);
    }
    $bib->setMaterialCd($array["material_cd"]);
    $bib->setCollectionCd($array["collection_cd"]);
    $bib->setCallNmbr1($array["call_nmbr1"]);
    $bib->setCallNmbr2($array["call_nmbr2"]);
    $bib->setCallNmbr3($array["call_nmbr3"]);
    if ($array["opac_flg"] == "Y") {
      $bib->setOpacFlg(true);
    } else {
      $bib->setOpacFlg(false);
    }

    /***********************************************************
     *  Reading biblio_field data
     ***********************************************************/
    # setting query that will return all the data in biblio
    $sql = "select biblio_field.* ";
    $sql = $sql."from biblio_field ";
    $sql = $sql."where biblio_field.bibid = ".$bibid;
    $sql = $sql." order by tag, subfield_cd";
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioQueryQueryErr2");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    /***********************************************************
     *  Adding fields from biblio to Biblio object 
     ***********************************************************/
    foreach ($this->_fieldsInBiblio as $key => $name) {
      $tag = substr($key, 0, 3);
      $subfieldCd = substr($key, 3, 1);
      $subfieldIdx = '';
      if (count($key) > 4) {
        $index = substr($key, 4);
      }
      $this->_addField($tag, $subfieldCd, $array[$name], $bib, $subfieldIdx);
    }

    /***********************************************************
     *  Adding fields from biblio_field to Biblio object 
     ***********************************************************/
    # subfieldIdx will be used to construct index
    $subfieldIdx = 0;
    $saveTag = "";
    $saveSubfield = "";
    while ($array = $this->_conn->fetchRow()) {
      $tag=$array["tag"];
      $subfieldCd=$array["subfield_cd"];

      # checking for tag and subfield break in order to set the subfield Idx correctly.
      if (($tag == $saveTag) and ($subfieldCd == $saveSubfield)) {
        $subfieldIdx = $subfieldIdx + 1;
      } else {
        $subfieldIdx = 0;
        $saveTag = $tag;
        $saveSubfield = $subfieldCd;
      }

      # setting the index.
      # format is ttts[i] where 
      #    t=tag
      #    s=subfield code
      #    i=subfield index if > 0
      # examples: 020a 650a 650a1 650a2
      $index = sprintf("%03d",$tag).$subfieldCd;
      if ($subfieldIdx > 0) {
        $index = $index.$subfieldIdx;
      }

      $bibFld = new BiblioField();
      $bibFld->setBibid($array["bibid"]);
      $bibFld->setFieldid($array["fieldid"]);
      $bibFld->setTag($array["tag"]);
      $bibFld->setInd1Cd($array["ind1_cd"]);
      $bibFld->setInd2Cd($array["ind2_cd"]);
      $bibFld->setSubfieldCd($array["subfield_cd"]);
      $bibFld->setFieldData($array["field_data"]);
      $bib->addBiblioField($index,$bibFld);
    }
    return $bib;
  }


  /****************************************************************************
   * Utility function to add a field to a Biblio object
   * @return void
   * @access private
   ****************************************************************************
   */
  /*###########################
    # WORKS WITH NEW FORMAT   #
    ###########################*/
  function _addField($tag,$subfieldCd,$value,&$bib,$seq="") {
    if ($value == "") {
      return;
    }
    $index = sprintf("%03d",$tag).$subfieldCd;
    if ($seq != "") {
      $index = $index.$seq;
    }
    $bibFld = new BiblioField();
    $bibFld->setTag($tag);
    $bibFld->setSubfieldCd($subfieldCd);
    $bibFld->setFieldData($value);
    $bib->addBiblioField($index,$bibFld);
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
   * Inserts new bibliography info into the biblio and biblio_field tables.
   * @param Biblio $biblio bibliography to insert
   * @return int returns bibid or false, if error occurs
   * @access public
   ****************************************************************************
   */
  /*###########################
    # WORKS WITH NEW FORMAT   #
    ###########################*/
  function insert($biblio) {
    // inserting biblio row
    $biblioFlds = $biblio->getBiblioFields();

    $bibfields = array();	// fields in biblio table
    foreach ($this->_fieldsInBiblio as $key => $name) {
      if (array_key_exists($key, $biblioFlds) and $biblioFlds[$key]->getFieldid() == '') {
        $bibfields[$name] = $biblioFlds[$key]->getFieldData();
        unset($biblioFlds[$key]);
      } else {
        $bibfields[$name] = '';
      }
    }

    $sql = "insert into biblio values (null, ";
    $sql = $sql."sysdate(), sysdate(), ";
    $sql = $sql.addslashes($biblio->getLastChangeUserid()).", ";
    $sql = $sql.addslashes($biblio->getMaterialCd()).", ";
    $sql = $sql.addslashes($biblio->getCollectionCd()).", ";
    $sql = $sql."'".addslashes($biblio->getCallNmbr1())."', ";
    $sql = $sql."'".addslashes($biblio->getCallNmbr2())."', ";
    $sql = $sql."'".addslashes($biblio->getCallNmbr3())."', ";
    $sql = $sql."'".addslashes($bibfields['title'])."', ";
    $sql = $sql."'".addslashes($bibfields['title_remainder'])."', ";
    $sql = $sql."'".addslashes($bibfields['responsibility_stmt'])."', ";
    $sql = $sql."'".addslashes($bibfields['author'])."', ";
    $sql = $sql."'".addslashes($bibfields['topic1'])."', ";
    $sql = $sql."'".addslashes($bibfields['topic2'])."', ";
    $sql = $sql."'".addslashes($bibfields['topic3'])."', ";
    $sql = $sql."'".addslashes($bibfields['topic4'])."', ";
    $sql = $sql."'".addslashes($bibfields['topic5'])."', ";
    if ($biblio->showInOpac()) {
      $sql = $sql."'Y')";
    } else {
      $sql = $sql."'N')";
    }
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioQueryInsertErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $bibid = $this->_conn->getInsertId();

    # inserting biblio_field rows
    if (!($this->_insertFields($biblioFlds, $bibid))) {
      return false;
    }

    return $bibid;
  }

  /****************************************************************************
   * Updates a bibliography in the biblio table.
   * @param Biblio $biblio bibliography to update
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  /*###########################
    # WORKS WITH NEW FORMAT   #
    ###########################*/
  function update($biblio) {
    $biblioFlds = $biblio->getBiblioFields();

    // updating biblio table
    $sql = "update biblio set last_change_dt = sysdate(), ";
    $sql = $sql."last_change_userid=".addslashes($biblio->getLastChangeUserid()).", ";
    $sql = $sql."material_cd=".addslashes($biblio->getMaterialCd()).", ";
    $sql = $sql."collection_cd=".addslashes($biblio->getCollectionCd()).", ";
    $sql = $sql."call_nmbr1='".addslashes($biblio->getCallNmbr1())."', ";
    $sql = $sql."call_nmbr2='".addslashes($biblio->getCallNmbr2())."', ";
    $sql = $sql."call_nmbr3='".addslashes($biblio->getCallNmbr3())."', ";
    foreach ($this->_fieldsInBiblio as $key => $name) {
      if (array_key_exists($key, $biblioFlds) and $biblioFlds[$key]->getFieldid() == '') {
        $sql .= $name."='".addslashes($biblioFlds[$key]->getFieldData())."', ";
        unset($biblioFlds[$key]);
      } else {
        $sql .= $name."='', ";
      }
    }
    if ($biblio->showInOpac()) {
      $sql = $sql."opac_flg='Y'";
    } else {
      $sql = $sql."opac_flg='N'";
    }
    $sql = $sql." where bibid=".$biblio->getBibid();

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioQueryUpdateErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    // inserting (or upating) biblio_field rows from update Biblio object.
    if (!($this->_insertFields($biblioFlds, $biblio->getBibid()))) {
      return false;
    }

    return $result;
  }


  /****************************************************************************
   * Inserts biblio_field rows
   * @param array reference $biblioFlds an array of BiblioField objects
   * @param int bibid id of bibliography to insert fields into
   * @return boolean returns false if error occurs
   * @access private
   ****************************************************************************
   */
  function _insertFields(&$biblioFlds, $bibid) {
    foreach ($biblioFlds as $key => $value) {
      if ($value->getFieldData() == "") {
        // value has been set to spaces, we may need to delete it
        if ($value->getFieldid() == "") {
          $sql = NULL;
        } else {
          $sql = "delete from biblio_field ";
          $sql = $sql."where bibid=".$value->getBibid();
          $sql = $sql." and fieldid=".$value->getFieldid();
        }          
      } elseif ($value->getFieldid() == "") {
        // new value
        $sql = "insert into biblio_field values (".$bibid.",null,";
        $sql = $sql."'".addslashes($value->getTag())."', ";
        $sql = $sql."'N','N',";
        $sql = $sql."'".addslashes($value->getSubfieldCd())."',";
        $sql = $sql."'".addslashes($value->getFieldData())."')";
      } else {
        // existing value
        $sql = "update biblio_field set field_data = '".addslashes($value->getFieldData())."' ";
        $sql = $sql."where bibid=".$value->getBibid();
        $sql = $sql." and fieldid=".$value->getFieldid();
      }
    
      if ($sql != NULL) {
        $result = $this->_conn->exec($sql);
        if ($result == FALSE) {
          $this->_errorOccurred = TRUE;
          $this->_error = $this->_loc->getText("biblioQueryInsertErr2");
          $this->_dbErrno = $this->_conn->getDbErrno();
          $this->_dbError = $this->_conn->getDbError();
          $this->_SQL = $sql;
          return FALSE;
        }
      }
    }
    return true;
  }

  /****************************************************************************
   * Deletes a bibliography from the biblio table.
   * @param string $bibid bibliography id of bibliography to delete
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($bibid) {
    $sql = "delete from biblio_field where bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioQueryDeleteErr");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $sql = "delete from biblio where bibid = ".$bibid;
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioQueryDeleteErr");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    return $result;
  }

}

?>
