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


  $tab = "reports";
  $nav = "runreport";

  // IE gets messed up if you send a cache control in the header of a pdf.  Therefore, the following
  // command will send no cache control.
  session_cache_limiter('private_no_expire'); 
  require_once("../shared/common.php");
  include("../shared/logincheck.php");
  include("../lib/pdf/class.pdf.php");
  require_once("../functions/fileIOFuncs.php");
  require_once("../classes/LetterFormat.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  define("FONT_DIR","../lib/pdf/fonts/");
  define("PAGE_HEIGHT",792);
  define("PAGE_WIDTH",612);

  #****************************************************************************
  #*  Validate criteria and run report
  #****************************************************************************
  include("../reports/run_report.php");

  function fatalError($msg=NULL) {
    global $rptid, $title, $baseSql, $letter, $initialSort;
    $s = "Location: ../reports/report_criteria.php"
         . "?rptid=".urlencode($rptid)
         . "&title=".urlencode($title)
         . "&sql=".urlencode($baseSql)
         . "&letter=".urlencode($letter)
         . "&initialSort=".urlencode($initialSort);
    if ($msg) {
      $s .= '&msg='.urlencode($msg);
    }
    header($s);
    exit();
  }

  #****************************************************************************
  #*  Parse letter layout definition xml and validate xml
  #****************************************************************************
  $fileName = $letter;
//  $xml = file_get_contents($fileName);
  $xml = fileGetContents($fileName);
  if ($xml === FALSE) {
    fatalError($loc->getText('Cannot read letter file: %fileName%',
               array('fileName' => basename($fileName))));
  }
  $letterDef = new LetterFormat();
  if (!$letterDef->parse($xml)) {
    $msg = $loc->getText("displayLabelsXmlErr");
    $msg = $msg.$letterDef->getXmlErrorString();
    fatalError($msg);
  }
  $validDef = $letterDef->validate();
  if (!$validDef) {
    fatalError($letterDef->getErrorMsg());
  }

//  header("Content-type: application/pdf");
//  header("Content-Disposition: inline; filename=display_labels.pdf");

/*echo "getId()=".$letterDef->getId();
echo "<br>getGroupBy()=".$letterDef->getGroupBy();
echo "<br>getTitle()=".$letterDef->getTitle();
echo "<br>getFontType()=".$letterDef->getFontType();
echo "<br>getFontSize()=".$letterDef->getFontSize();
echo "<br>getUnitOfMeasure()=".$letterDef->getUnitOfMeasure();
echo "<br>getLeftMargin()=".$letterDef->getLeftMargin();
echo "<br>getRightMargin()=".$letterDef->getRightMargin();
echo "<br>getTopMargin()=".$letterDef->getTopMargin();
echo "<br>getBottomMargin()=".$letterDef->getBottomMargin();
echo "<br>Lines:";
foreach($letterDef->getLines() as $line) {
  echo "<br>&nbsp;&nbsp;text=".$line->getText();
  echo "<br>&nbsp;&nbsp;column count=".sizeof($line->getColumnNames());
}
echo "<br>Report Column Names:";
foreach($letterDef->getReportColumnNames() as $name) {
  echo "<br>&nbsp;&nbsp;name=".$name;
}
echo "<br>Report Column Widths:";
foreach($letterDef->getReportColumnWidths() as $width) {
  echo "<br>&nbsp;&nbsp;width=".$width;
}
exit();
*/

  #****************************************************************************
  #*  Functions used for creating pdf
  #****************************************************************************
  function printHeading(&$loc,&$pdf,&$letterDef,&$y) {
    $fontSize = $letterDef->getFontSize();
    $colNames = $letterDef->getReportColumnNames();
    $colWidths = $letterDef->getReportColumnWidths();
    $fontHeight = $pdf->getFontHeight($fontSize);

    $y = $y - $fontHeight;
    $x = $letterDef->getLeftMarginInPdfUnits();
    for ($i=0; $i<count($colNames); $i++) {
      $colName = $colNames[$i];
      $colWidth = $colWidths[$i];
      $pdf->addTextWrap($x,$y,$colWidth,$fontSize,$loc->getText($colName));
      $pdf->line($x,$y - 5,$x + $colWidth - 5,$y - 5);
      $x = $x + $colWidth;
    }
    $y = $y - 5;
  }

  #****************************************************************************
  #*  Creating pdf containing letters
  #****************************************************************************
  $pdf = new Cpdf();
  $fontFile = FONT_DIR.$letterDef->getFontType().".afm";
  $pdf->selectFont($fontFile);
  $fontSize = $letterDef->getFontSize();
  $fontHeight = $pdf->getFontHeight($fontSize);
  $colNames = $letterDef->getReportColumnNames();
  $colWidths = $letterDef->getReportColumnWidths();
  $groupBy = $letterDef->getGroupBy();
  $saveGroupByValue = "";
  $firstPage = TRUE;
  $minY = $letterDef->getBottomMarginInPdfUnits();

  while ($array = $reportQ->fetchRowAssoc()) {
    $groupByValue = $array[$groupBy];
    if ($groupByValue != $saveGroupByValue) {
      // new letter
      $y = PAGE_HEIGHT - $letterDef->getTopMarginInPdfUnits();
      if ($firstPage) {
        $firstPage = FALSE;
      } else {
        $pdf->newPage();
      }
      foreach($letterDef->getLines() as $line) {
        $y = $y - $fontHeight;
        $x = $letterDef->getLeftMarginInPdfUnits() + $line->getIndent();
        $w = PAGE_WIDTH - $x - $letterDef->getRightMarginInPdfUnits();
        $resultLine = $line->getFormattedText($array);
        //*************************************************************
        //*  addTextWrap - addes a line to the pdf
        //*  arguments:
        //*    x - location from left of page in pdf units.
        //*    y - location from bottom of page in pdf units.
        //*    w - width in pdf units of text area.  Text exceeding
        //*        this width will be truncated.
        //*  addText - same as addTextWrap but allows text to go on
        //*    without being indented.  Format is addText(x,y,fontSize,text)
        //*************************************************************
        $pdf->addTextWrap($x,$y,$w,$fontSize,$resultLine);
      }
      
      // add a blank line
      $y = $y - $fontHeight;
      $saveGroupByValue = $groupByValue;

      // show report headings
      printHeading($loc,$pdf,$letterDef,$y);
    }
    // show report data

    // check for end of page
    $y = $y - $fontHeight;
    $x = $letterDef->getLeftMarginInPdfUnits();
    if ($y < $minY) {
        $pdf->newPage();
        $y = PAGE_HEIGHT - $letterDef->getTopMarginInPdfUnits();
        $y = $y - $fontHeight;
        $pdf->addText($x,$y,$fontSize,"<i>continued from previous page.</i>");
        $y = $y - $fontHeight;
        printHeading($loc,$pdf,$letterDef,$y);
        $y = $y - $fontHeight;
    }

    for ($i=0; $i<count($colNames); $i++) {
      $colName = $colNames[$i];
      $colWidth = $colWidths[$i];
      $colValue = $array[$colName];
      $pdf->addTextWrap($x,$y,$colWidth,$fontSize,$colValue);
//      echo "<br>x=".$x." y=".$y." w=".$colWidth." font=".$fontSize." val=".$colValue;
      $x = $x + $colWidth;
    }

  }

  $reportQ->close();
  $pdf->stream();

  exit();

?>