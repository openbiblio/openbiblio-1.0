<?php
/**********************************************************************************
 *   Copyright(C) 2002, 2003, 2004 David Stevens
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

/******************************************************************************
 * LabelFormat represents a label format specification
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class PdfLine {
  var $_text = "";
  var $_columnNames;
  var $_columnLocations;
  var $_indent = 0;

  function PdfLine () {
    $this->_columnNames = Array();
    $this->_columnNameLocations = Array();
  }

  /****************************************************************************
   * Getter methods for all fields
   * @return string
   * @access public
   ****************************************************************************
   */
  function getText() {
    return $this->_text;
  }
  function getColumnNames() {
    return $this->_columnNames;
  }
  function getColumnLocations() {
    return $this->_columnLocations;
  }
  function getIndent() {
    return $this->_indent;
  }

  /****************************************************************************
   * Setter methods for all fields
   * @param string $value new value to set
   * @return void
   * @access public
   ****************************************************************************
   */
  function setText($value) {
    $this->_text = $value;
  }
  function addColumn($name,$loc) {
    $this->_columnNames[] = trim($name);
    $this->_columnLocations[] = trim($loc);
  }
  function setIndent($value) {
    if (is_numeric($value)) {
      $this->_indent = $value;
    }
  }

  /****************************************************************************
   * Returns text data with column parameters replaced with data from the database.
   * @param array $rowData array containing result set from database
   * @return string
   * @access public
   ****************************************************************************
   */
  function getFormattedText($rowData) {
    $resultLine = $this->_text;
    $arraySize = sizeof($this->_columnNames);
    for ($i = $arraySize - 1; $i >= 0; $i--) {
      $colName = $this->_columnNames[$i];
      if (isset($rowData[$colName])) {
        $colLoc = $this->_columnLocations[$i];
        $prefix = substr($resultLine,0,$colLoc);
        $suffix = substr($resultLine,$colLoc);
        $resultLine = $prefix.$rowData[$colName].$suffix;
      }
    }
    return $resultLine;
  }

}

?>
