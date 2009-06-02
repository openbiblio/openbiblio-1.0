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


  $tab = "reports";
  $nav = "runreport";

  // IE gets messed up if you send a cache control in the header of a pdf.  Therefore, the following
  // command will send no cache control.
  session_cache_limiter('private_no_expire'); 
  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  include("../lib/pdf/class.pdf.php");
  require_once("../functions/fileIOFuncs.php");
  require_once("../classes/LabelFormat.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  define("FONT_DIR","../lib/pdf/fonts/");
  define("PAGE_HEIGHT",792);
  define("PAGE_WIDTH",612);

  #****************************************************************************
  #*  Validate criteria and run report
  #****************************************************************************
  include("../reports/run_report.php");

  #****************************************************************************
  #*  Validate start on label # field
  #****************************************************************************
  $startOnLabel = trim($HTTP_POST_VARS["startOnLabel"]);
  $HTTP_POST_VARS["startOnLabel"] = $startOnLabel;
  if (!is_numeric($startOnLabel)) {
    $pageErrors["startOnLabel"] = $loc->getText("displayLabelsStartOnLblErr");
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    $urlTitle = urlencode($title);
    $urlSql = urlencode($baseSql);
    header("Location: ../reports/report_criteria.php?rptid=".$rptid."&title=".$urlTitle."&sql=".$urlSql."&label=".$label);
    exit();
  }

  #****************************************************************************
  #*  Parse label layout definition xml and validate xml
  #****************************************************************************
  $fileName = $label;
//  $xml = file_get_contents($fileName);
  $xml = fileGetContents($fileName);
  $labelDef = new LabelFormat();
  if (!$labelDef->parse($xml)) {
    $msg = $loc->getText("displayLabelsXmlErr");
    $msg = $msg.$labelDef->getXmlErrorString();
    $msg = urlencode($msg);
    $urlTitle = urlencode($title);
    $urlSql = urlencode($baseSql);
    header("Location: ../reports/report_criteria.php?rptid=".$rptid."&title=".$urlTitle."&sql=".$urlSql."&label=".$label."&msg=".$msg);
    exit();
  }
  $validDef = $labelDef->validate();
  if (!$validDef) {
    $msg = $labelDef->getErrorMsg();
    $msg = urlencode($msg);
    $urlTitle = urlencode($title);
    $urlSql = urlencode($baseSql);
    header("Location: ../reports/report_criteria.php?rptid=".$rptid."&title=".$urlTitle."&sql=".$urlSql."&label=".$label."&msg=".$msg);
    exit();
  }

//  header("Content-type: application/pdf");
//  header("Content-Disposition: inline; filename=display_labels.pdf");

/*echo "getId()=".$labelDef->getId();
echo "<br>getTitle()=".$labelDef->getTitle();
echo "<br>getFontType()=".$labelDef->getFontType();
echo "<br>getFontSize()=".$labelDef->getFontSize();
echo "<br>getLeftMargin()=".$labelDef->getLeftMargin();
echo "<br>getTopMargin()=".$labelDef->getTopMargin();
echo "<br>getColumns()=".$labelDef->getColumns();
echo "<br>getWidth()=".$labelDef->getWidth();
echo "<br>getSubLabelCount()=".$labelDef->getSubLabelCount();
exit();
*/

  #****************************************************************************
  #*  Creating pdf containing labels
  #****************************************************************************
  $pdf = new Cpdf();

  $fontFile = FONT_DIR.$labelDef->getFontType().".afm";
  $pdf->selectFont($fontFile);
  $fontSize = $labelDef->getFontSize();
  $labelRows = floor(PAGE_HEIGHT / $labelDef->getHeight());
  $labelCols = min($labelDef->getColumns(), floor(PAGE_WIDTH / $labelDef->getWidth()));
  $fontHeight = $pdf->getFontHeight($fontSize);

  $col = 1;
  $row = 1;


  // increase col and row settings if start label specified
  for($i = 1; $i < $startOnLabel; $i++) {
    $col++;
    if ($col > $labelCols) {
      $col = 1;
      $row++;
      if ($row > $labelRows) {
        $row = 1;
        $pdf->newPage();
      }
    }
  }
  
  // main report loop
  while ($array = $reportQ->fetchRowAssoc()) {
    // calc x and y based on col and row
    $y = PAGE_HEIGHT - ($labelDef->getHeight() * ($row - 1)) - $labelDef->getTopMargin();
    $x = ($labelDef->getWidth() * ($col - 1)) + $labelDef->getLeftMargin();

    // print label and its subLabels
    $subLabels = $labelDef->getSubLabels();
    foreach($subLabels as $subLabel) {
      $suby = $y - $fontSize - $subLabel->getTop();
      $subx = $x + $subLabel->getLeft();
      $subw = $labelDef->getWidth() - $subLabel->getLeft();
      if ($subw <= 0) {
        $subw = $labelDef->getWidth();
      }
      foreach($subLabel->getLines() as $line) {
        $pdf->addTextWrap($subx,$suby,$subw,$fontSize,$array[$line]);
//        $pdf->addText($subx,$suby,$fontSize,$array[$line]);
        $suby = $suby - $fontSize;
      }
    }
     
    // column, row and page breaks
    $col++;
    if ($col > $labelCols) {
      $col = 1;
      $row++;
      if ($row > $labelRows) {
        $row = 1;
        $pdf->newPage();
      }
    }

  }
  $reportQ->close();
  $pdf->stream();

?>