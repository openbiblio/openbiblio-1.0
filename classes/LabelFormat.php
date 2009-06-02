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

  require_once("../classes/SubLabel.php");
  require_once("../classes/Localize.php");

/******************************************************************************
 * LabelFormat represents a label format specification
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class LabelFormat {
  var $_id = "";
  var $_title = "";
  var $_fontType = "";
  var $_fontSize = "";
  var $_leftMargin = "";
  var $_topMargin = "";
  var $_columns = "";
  var $_width = "";
  var $_height = "";
  var $_subLabels;
  var $_subLabelCount = 0;
  var $_currentSubLabel = NULL;
  var $_currentLine = "";
  var $_parser;
  var $_tag;
  var $_tagLvl = 1;
  var $_errorMsg = "";
  var $_loc;

  function LabelFormat () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
    $this->_subLabels = Array();
    $this->_currentSubLabel = new SubLabel();
    $this->_tag = Array();
    $this->_tag[$this->_tagLvl] = NULL;

    $this->_parser = xml_parser_create();
    xml_set_object($this->_parser, &$this);
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
    } elseif (strcasecmp($tag,"sub_label") == 0) {
      $left = 0;
      $top = 0;
      foreach($attributes as $attr => $attrValue) {
        if (strcasecmp($attr,"left") == 0) {
          $left = $attrValue;
        } else if (strcasecmp($attr,"top") == 0) {
          $top = $attrValue;
        }
      }
      $this->_currentSubLabel->setLeft($left);
      $this->_currentSubLabel->setTop($top);
      $this->_tag[$this->_tagLvl++] = $tag;
      $this->_tag[$this->_tagLvl] = NULL;
    // non grouping tags
    } else {
      $this->_tag[$this->_tagLvl] = $tag;
    }
  }

  function cdata($parser, $cdata) {
    if (strcasecmp($this->_tag[$this->_tagLvl],"id") == 0) {
      $this->_id = $this->_id.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"title") == 0) {
      $this->_title = $this->_title.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"font_type") == 0) {
      $this->_fontType = $this->_fontType.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"font_size") == 0) {
      $this->_fontSize = $this->_fontSize.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"left_margin") == 0) {
      $this->_leftMargin = $this->_leftMargin.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"top_margin") == 0) {
      $this->_topMargin = $this->_topMargin.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"columns") == 0) {
      $this->_columns = $this->_columns.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"width") == 0) {
      $this->_width = $this->_width.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"height") == 0) {
      $this->_height = $this->_height.trim($cdata);
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"line") == 0) {
      $this->_currentLine = $this->_currentLine.trim($cdata);
    }
  }

  function tag_close($parser, $tag) {
    // grouping tags (layout, sub_label) subtract 1 to tag level
    if (strcasecmp($tag,"layout") == 0) {
      if ($this->_tagLvl == 2) {
        $this->_subLabels[0] = $this->_currentSubLabel;
        $this->_subLabelCount = 1;
      }
      $this->_tag[$this->_tagLvl--] = NULL;
    } elseif (strcasecmp($tag,"sub_label") == 0) {
      $this->_subLabels[] = $this->_currentSubLabel;
      $this->_subLabelCount++;
      $this->_currentSubLabel = new SubLabel();
      $this->_tag[$this->_tagLvl--] = NULL;
    // non grouping tags
    } elseif (strcasecmp($this->_tag[$this->_tagLvl],"line") == 0) {
      $this->_currentSubLabel->addLine($this->_currentLine);
      $this->_currentLine = "";
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
    if (!is_numeric($this->_topMargin)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatTMarginErr")."<br>";
    } else if ($this->_topMargin <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatTMarginErr2")."<br>";
    }
    if (!is_numeric($this->_columns)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatColErr")."<br>";
    } else if ($this->_columns <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatColErr2")."<br>";
    }
    if (!is_numeric($this->_width)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatWidthErr")."<br>";
    } else if ($this->_width <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatWidthErr2")."<br>";
    }
    if (!is_numeric($this->_height)) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatHeightErr")."<br>";
    } else if ($this->_height <= 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatHeightErr2")."<br>";
    }
    if ($this->_subLabelCount == 0) {
      $valid = FALSE;
      $this->_errorMsg = $this->_errorMsg.$this->_loc->getText("labelFormatNoLabelsErr")."<br>";
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
  function getTitle() {
    return $this->_title;
  }
  function getFontType() {
    return $this->_fontType;
  }
  function getFontSize() {
    return $this->_fontSize;
  }
  function getLeftMargin() {
    return $this->_leftMargin;
  }
  function getTopMargin() {
    return $this->_topMargin;
  }
  function getColumns() {
    return $this->_columns;
  }
  function getWidth() {
    return $this->_width;
  }
  function getHeight() {
    return $this->_height;
  }
  function getSubLabels() {
    return $this->_subLabels;
  }
  function getSubLabelCount() {
    return $this->_subLabelCount;
  }
  function getErrorMsg() {
    return $this->_errorMsg;
  }

  function destroy() {
    xml_parser_free($this->_parser);
  }
}

?>
