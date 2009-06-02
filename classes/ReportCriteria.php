<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../classes/Localize.php");

/******************************************************************************
 * ReportCriteria represents a report selection criteria
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class ReportCriteria {
  var $_fieldid = "";
  var $_comparitor = "";
  var $_name = "";
  var $_type = "";
  var $_isNumeric = FALSE;
  var $_value1 = "";
  var $_value1Error = "";
  var $_value2 = "";
  var $_value2Error = "";
  var $_loc;

  function ReportCriteria () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validateData() {
    $valid = TRUE;
    // validate numeric field
    if ($this->_isNumeric) {
      if (!is_numeric($this->_value1)) {
        $valid = FALSE;
        $this->_value1Error = $this->_loc->getText("reportCriteriaErr1");
      }
      if (($this->_comparitor == "bt") and (!is_numeric($this->_value2))) {
        $valid = FALSE;
        $this->_value2Error = $this->_loc->getText("reportCriteriaErr1");
      }
    }
    // validate datetime field
    if ($this->_type == OBIB_MYSQL_DATETIME_TYPE) {
      if (($timestamp = strtotime($this->_value1)) === -1) {
        $valid = FALSE;
        $this->_value1Error = $this->_loc->getText("reportCriteriaDateTimeErr");
      } else {
        $this->_value1 = date(OBIB_MYSQL_DATETIME_FORMAT,$timestamp);
      }
      if ($this->_comparitor == "bt") {
        if (($timestamp = strtotime($this->_value2)) === -1) {
          $valid = FALSE;
          $this->_value2Error = $this->_loc->getText("reportCriteriaDateTimeErr");
        } else {
          $this->_value2 = date(OBIB_MYSQL_DATETIME_FORMAT,$timestamp);
        }

      }
    }
    // validate date field
    if ($this->_type == OBIB_MYSQL_DATE_TYPE) {
      if (($timestamp = strtotime($this->_value1)) === -1) {
        $valid = FALSE;
        $this->_value1Error = $this->_loc->getText("reportCriteriaDateErr");
      } else {
        $this->_value1 = date(OBIB_MYSQL_DATE_FORMAT,$timestamp);
      }
      if ($this->_comparitor == "bt") {
        if (($timestamp = strtotime($this->_value2)) === -1) {
          $valid = FALSE;
          $this->_value2Error = $this->_loc->getText("reportCriteriaDateErr");
        } else {
          $this->_value2 = date(OBIB_MYSQL_DATE_FORMAT,$timestamp);
        }

      }
    }
    return $valid;
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getFieldid() {
    return $this->_fieldid;
  }
  function getComparitor() {
    return $this->_comparitor;
  }
  function getSqlComparitor() {
    if ($this->_comparitor == "ne") {
      return "<>";
    } elseif ($this->_comparitor == "lt") {
      return "<";
    } elseif ($this->_comparitor == "gt") {
      return ">";
    } elseif ($this->_comparitor == "le") {
      return "<=";
    } elseif ($this->_comparitor == "ge") {
      return ">=";
    } elseif ($this->_comparitor == "bt") {
      return "between";
    }
    return "=";    
  }
  function getName() {
    return $this->_name;
  }
  function getType() {
    return $this->_type;
  }
  function isNumeric() {
    return $this->_isNumeric;
  }
  function getValue1() {
    return $this->_value1;
  }
  function getValue1Error() {
    return $this->_value1Error;
  }
  function getValue2() {
    return $this->_value2;
  }
  function getValue2Error() {
    return $this->_value2Error;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setFieldid($value) {
    $this->_fieldid = trim($value);
  }
  function setComparitor($value) {
    $this->_comparitor = trim($value);
  }
  function setName($value) {
    $this->_name = trim($value);
  }
  function setType($value) {
    $this->_type = trim($value);
  }
  function setNumeric($value) {
    if ($value == TRUE) {
      $this->_isNumeric = TRUE;
    } else {
      $this->_isNumeric = FALSE;
    }
  }
  function setValue1($value) {
    $this->_value1 = trim($value);
  }
  function setValue2($value) {
    $this->_value2 = trim($value);
  }
}

?>
