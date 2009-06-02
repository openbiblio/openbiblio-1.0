<?php
/**********************************************************************************
 *   Copyright(C) 2004 David Stevens
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

  require_once("../classes/SubLabel.php");
  require_once("../classes/PdfLine.php");
  require_once("../classes/Localize.php");

/******************************************************************************
 * LabelFormat represents a label format specification
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class LetterFormat {
  var $_id = "";
  var $_reportDefFilename = "";
  var $_groupBy = "";
  var $_title = "";
  var $_fontType = "";
  var $_fontSize = "";
  var $_unitOfMeasure = "";
  var $_leftMargin = "";
  var $_rightMargin = "";
  var $_topMargin = "";
  var $_bottomMargin = "";
  var $_lines;
  var $_reportColumnNames;
  var $_reportColumnWidths;
  var $_currentLine;
  var $_parser;
  var $_tag;
  var $_tagLvl = 1;
  var $_errorMsg = "";
  var $_loc;

  function LetterFormat () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
    $this->_lines = Array();
    $this->_reportColumnNames = Array();
    $this->_reportColumnWidths = Array();
    $this->_currentLine = new PdfLine();
    $this->_tag = Array();
    $this->_tag[$this->_tagLvl] = NULL;

    $this->_parser = xml_parser_create();
    xml_set_object($this->_parser, $this);
    xml_set_element_handler($this->_parser, "tag_open", "tag_close");
    xml_set_character_data_handler($this->_parser, "cdata");
  }

  function parse($data) { 
    return xml_parse($this->_parser, $data);
  }

  function tag_open($parser, $tag, $attributes) {
    // grouping tags (layout, sub_label) add 1 to tag level
    if (strcasecmp($tag,"layout") == 0) {
      $this->_tag[$this->_tagLvl++] = $tag;
      $this->_tag[$this->_tagLvl] = NULL;
    } elseif (strcasecmp($tag,"report") == 0) {
      $this->_tag[$this->_tagLvl++] = $tag;
      $this->_tag[$this->_tagLvl] = NULL;
    } elseif (strcasecmp($tag,"line") == 0) {
      $this->_tag[$this->_tagLvl++] = $tag;
      $this->_tag[$this->_tagLvl] = NULL;
      $this->_currentLine = new PdfLine();
      if (isset($attributes['INDENT'])) {
        $indent = $this->_toPdfUnits($attributes["INDENT"]);
        $this->_currentLine->setIndent($indent);
      }

    // non grouping tags
    } elseif (strcasecmp($tag,"column") == 0) {
      $colName = $attributes["NAME"];
      $parentTag = $this->_tag[$this->_tagLvl - 1];
      if (strcasecmp($parentTag,"report") == 0) {
        $colWidth = $this->_toPdfUnits($attributes["WIDTH"]);
        $this->_reportColumnNames[] = $colName;
        $this->_reportColumnWidths[] = $colWidth;
      } else {
        $colLoc = strlen($this->_currentLine->getText());
        $this->_currentLine->addColumn($colName,$colLoc);
      }
    } elseif (strcasecmp($tag,"group_by") == 0) {
      if (isset($attributes['NAME'])) {
        $groupBy = $attributes["NAME"];
        $this->_groupBy = trim($groupBy);
      }
      $this->_tag[$this->_tagLvl] = $tag;
    } else {
      $this->_tag[$this->_tagLvl] = $tag;
    }
  }

  function cdata($parser, $cdata) {
    if (strcasecmp($this->_tag[$this->_tagLvl],"id") == 0) {
      $this->_id = $this->_id.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"report_def_filename") == 0) {
      $this->_reportDefFilename = $this->_reportDefFilename.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"title") == 0) {
      $this->_title = $this->_title.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"font_type") == 0) {
      $this->_fontType = $this->_fontType.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"font_size") == 0) {
      $this->_fontSize = $this->_fontSize.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"unit_of_measure") == 0) {
      $this->_unitOfMeasure = $this->_unitOfMeasure.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"left_margin") == 0) {
      $this->_leftMargin = $this->_leftMargin.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"right_margin") == 0) {
      $this->_rightMargin = $this->_rightMargin.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"top_margin") == 0) {
      $this->_topMargin = $this->_topMargin.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"bottom_margin") == 0) {
      $this->_bottomMargin = $this->_bottomMargin.trim($cdata);
    } else {
      if ($this->_tagLvl > 1) {
        $parentTag = $this->_tag[$this->_tagLvl - 1];
        if (strcasecmp($parentTag,"line") == 0) {
          $this->_currentLine->setText($this->_currentLine->getText().$cdata);
        }
      }
    }
  }

  function tag_close($parser, $tag) {
    // grouping tags (layout, sub_label) subtract 1 to tag level
    if (strcasecmp($tag,"layout") == 0) {
      $this->_tag[$this->_tagLvl--] = NULL;
    } elseif (strcasecmp($tag,"report") == 0) {
      $this->_tag[$this->_tagLvl--] = NULL;
    } elseif (strcasecmp($tag,"line") == 0) {
      $this->_lines[] = $this->_currentLine;
      $this->_tag[$this->_tagLvl--] = NULL;

    // non grouping tags
    } else {      
      $this->_tag[$this->_tagLvl] = NULL;
    }
  }

  function getXmlErrorString() {
    $errStr = "xml error: ".xml_error_string(xml_get_error_code($this->_parser));
    $errStr = $errStr."\nline number: ".xml_get_current_line_number ($this->_parser);
    $errStr = $errStr."\ncolumn number: ".xml_get_current_column_number ($this->_parser);
    return $errStr;
  }

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function validate() {
    $this->_errorMsg = "";
    $valid = TRUE;
    if (!(($this->_fontType == "Courier")
      or ($this->_fontType == "Helvetica")
      or ($this->_fontType == "Times-Roman"))) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatFontErr")."<br>";
    }
    if (!is_numeric($this->_fontSize)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatFontSizeErr")."<br>";
    } else if ($this->_fontSize <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatFontSizeErr2")."<br>";
    }
    if (!is_numeric($this->_leftMargin)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatLMarginErr")."<br>";
    } else if ($this->_leftMargin <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatLMarginErr2")."<br>";
    }
    if (!is_numeric($this->_rightMargin)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatRMarginErr")."<br>";
    } else if ($this->_rightMargin <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatRMarginErr2")."<br>";
    }
    if (!is_numeric($this->_topMargin)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatTMarginErr")."<br>";
    } else if ($this->_topMargin <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatTMarginErr2")."<br>";
    }
    if (!is_numeric($this->_bottomMargin)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatBMarginErr")."<br>";
    } else if ($this->_bottomMargin <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatBMarginErr2")."<br>";
    }
    return $valid;
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
  function getReportDefFilename() {
    return $this->_reportDefFilename;
  }
  function getGroupBy() {
    return $this->_groupBy;
  }
  function getTitle() {
    return $this->_title;
  }
  function getFontType() {
    return $this->_fontType;
  }
  function getFontSize() {
    return $this->_fontSize;
  }
  function getUnitOfMeasure() {
    $returnValue = "cm";
    if (strcasecmp($this->_unitOfMeasure,"in") == 0) {
      $returnValue = "in";
    } elseif (strcasecmp($this->_unitOfMeasure,"cm") == 0) {
      $returnValue = "cm";
    }
    return $returnValue;
  }
  function _toPdfUnits($units) {
    if (!is_numeric($units)) {
      $returnUnits = 0;
    } else {
      $returnUnits = $units;
      if ($this->getUnitOfMeasure() == "in") {
        $returnUnits = $units * 72;
      } elseif ($this->getUnitOfMeasure() == "cm") {
        $returnUnits = $units * 28.35;
      }
    }
    return $returnUnits;
  }
  function getLeftMargin() {
    return $this->_leftMargin;
  }
  function getLeftMarginInPdfUnits() {
    return $this->_toPdfUnits($this->_leftMargin);
  }
  function getRightMargin() {
    return $this->_rightMargin;
  }
  function getRightMarginInPdfUnits() {
    return $this->_toPdfUnits($this->_rightMargin);
  }
  function getTopMargin() {
    return $this->_topMargin;
  }
  function getTopMarginInPdfUnits() {
    return $this->_toPdfUnits($this->_topMargin);
  }
  function getBottomMargin() {
    return $this->_bottomMargin;
  }
  function getBottomMarginInPdfUnits() {
    return $this->_toPdfUnits($this->_bottomMargin);
  }

  function getLines() {
    return $this->_lines;
  }

  function getReportColumnNames() {
    return $this->_reportColumnNames;
  }

  function getReportColumnWidths() {
    return $this->_reportColumnWidths;
  }

  function getErrorMsg() {
    return $this->_errorMsg;
  }

  function destroy() {
    xml_parser_free($this->_parser);
  }
}

?>
