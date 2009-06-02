<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

  $tab = "reports";
  $nav = "runreport";

  // IE gets messed up if you send a cache control in the header of a pdf.  Therefore, the following
  // command will send no cache control.
  session_cache_limiter('private_no_expire'); 
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
         . "?rptid=".U($rptid)
         . "&title=".U($title)
         . "&sql=".U($baseSql)
         . "&letter=".U($letter)
         . "&initialSort=".U($initialSort);
    if ($msg) {
      $s .= '&msg='.U($msg);
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

/*echo "getId()=".H($letterDef->getId());
echo "<br>getGroupBy()=".H($letterDef->getGroupBy());
echo "<br>getTitle()=".H($letterDef->getTitle());
echo "<br>getFontType()=".H($letterDef->getFontType());
echo "<br>getFontSize()=".H($letterDef->getFontSize());
echo "<br>getUnitOfMeasure()=".H($letterDef->getUnitOfMeasure());
echo "<br>getLeftMargin()=".H($letterDef->getLeftMargin());
echo "<br>getRightMargin()=".H($letterDef->getRightMargin());
echo "<br>getTopMargin()=".H($letterDef->getTopMargin());
echo "<br>getBottomMargin()=".H($letterDef->getBottomMargin());
echo "<br>Lines:";
foreach($letterDef->getLines() as $line) {
  echo "<br>&nbsp;&nbsp;text=".H($line->getText());
  echo "<br>&nbsp;&nbsp;column count=".H(sizeof($line->getColumnNames()));
}
echo "<br>Report Column Names:";
foreach($letterDef->getReportColumnNames() as $name) {
  echo "<br>&nbsp;&nbsp;name=".H($name);
}
echo "<br>Report Column Widths:";
foreach($letterDef->getReportColumnWidths() as $width) {
  echo "<br>&nbsp;&nbsp;width=".H($width);
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
        $x = $letterDef->getLeftMarginInPdfUnits() + $line->getIndent();
        $w = PAGE_WIDTH - $x - $letterDef->getRightMarginInPdfUnits();
        $resultLine = $line->getFormattedText($array);
        # Ugly on top of ugly
        foreach (explode("\n", $resultLine) as $l) {
          $y = $y - $fontHeight;
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
          $pdf->addTextWrap($x,$y,$w,$fontSize,$l);
        }
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
      $x = $x + $colWidth;
    }

  }

  $reportQ->close();
  $pdf->stream();

  exit();

?>
