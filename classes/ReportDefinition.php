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
class ReportDefinition {
  var $_id;
  var $_title;
  var $_sql;
  var $_parser;
  var $_tag = NULL;
  var $_loc;

  function ReportDefinition () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");

    $this->_parser = xml_parser_create();
    xml_set_object($this->_parser, $this);
    xml_set_element_handler($this->_parser, "tag_open", "tag_close");
    xml_set_character_data_handler($this->_parser, "cdata");
  }

  function parse($data) { 
    return xml_parse($this->_parser, $data);
  }

  function tag_open($parser, $tag, $attributes) {
    $this->_tag = $tag;
  }

  function cdata($parser, $cdata) {
    if (strcasecmp($this->_tag,"id") == 0){
      $this->_id = $this->_id.$cdata;
    } else if (strcasecmp($this->_tag,"title") == 0){
      $this->_title = $this->_title.$cdata;
    } else if (strcasecmp($this->_tag,"sql") == 0){
      $this->_sql = $this->_sql.$cdata;
    }
  }

  function tag_close($parser, $tag) {
    $this->_tag = NULL;
  }

  function getXmlErrorString() {
    $errStr = "xml error: ".xml_error_string(xml_get_error_code($this->_parser));
    $errStr = $errStr."\nline number: ".xml_get_current_line_number ($this->_parser);
    $errStr = $errStr."\ncolumn number: ".xml_get_current_column_number ($this->_parser);
    return $errStr;
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getId() {
    return $this->_id;
  }
  function getTitle() {
    return $this->_title;
  }
  function getSql() {
    return $this->_sql;
  }
  function destroy() {
    xml_parser_free($this->_parser);
  }
}

?>
