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

  function fatalError($msg=NULL) {
    global $rptid, $title, $baseSql, $label;
    $s = "Location: ../reports/report_criteria.php"
         . "?rptid=".U($rptid)
         . "&title=".U($title)
         . "&sql=".U($baseSql)
         . "&label=".U($label);
    if ($msg) {
      $s .= '&msg='.U($msg);
    }
    header($s);
    exit();
  }

  #****************************************************************************
  #*  Validate start on label # field
  #****************************************************************************
  $startOnLabel = trim($_POST["startOnLabel"]);
  $_POST["startOnLabel"] = $startOnLabel;
  if (!is_numeric($startOnLabel)) {
    $pageErrors["startOnLabel"] = $loc->getText("displayLabelsStartOnLblErr");
    $_SESSION["pageErrors"] = $pageErrors;
    fatalError();
  }

  #****************************************************************************
  #*  Parse label layout definition xml and validate xml
  #****************************************************************************
  $fileName = $label;
//  $xml = file_get_contents($fileName);
  $xml = fileGetContents($fileName);
  if ($xml === FALSE) {
    fatalError($loc->getText('displayLabelsCannotRead',
               array('fileName' => basename($fileName))));
  }
  $labelDef = new LabelFormat();
  if (!$labelDef->parse($xml)) {
    $msg = $loc->getText("displayLabelsXmlErr");
    $msg = $msg.$labelDef->getXmlErrorString();
    fatalError($msg);
  }
  $validDef = $labelDef->validate();
  if (!$validDef) {
    fatalError($labelDef->getErrorMsg());
  }

//  header("Content-type: application/pdf");
//  header("Content-Disposition: inline; filename=display_labels.pdf");

/*echo "getId()=".H($labelDef->getId());
echo "<br>getTitle()=".H($labelDef->getTitle());
echo "<br>getFontType()=".H($labelDef->getFontType());
echo "<br>getFontSize()=".H($labelDef->getFontSize());
echo "<br>getFontSize()=".H($labelDef->getUnitOfMeasure());
echo "<br>getLeftMargin()=".H($labelDef->getLeftMargin());
echo "<br>getTopMargin()=".H($labelDef->getTopMargin());
echo "<br>getColumns()=".H($labelDef->getColumns());
echo "<br>getWidth()=".H($labelDef->getWidth());
echo "<br>getSubLabelCount()=".H($labelDef->getSubLabelCount());
exit();
*/

  #****************************************************************************
  #*  Creating pdf containing labels
  #****************************************************************************
  $pdf = new Cpdf();

  $fontFile = FONT_DIR.$labelDef->getFontType().".afm";
  $pdf->selectFont($fontFile);
  $fontSize = $labelDef->getFontSize();
  $labelRows = floor(PAGE_HEIGHT / $labelDef->getHeightInPdfUnits());
  $labelCols = min($labelDef->getColumns(), floor(PAGE_WIDTH / $labelDef->getWidthInPdfUnits()));
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
    $y = PAGE_HEIGHT - ($labelDef->getHeightInPdfUnits() * ($row - 1)) - $labelDef->getTopMarginInPdfUnits();
    $x = ($labelDef->getWidthInPdfUnits() * ($col - 1)) + $labelDef->getLeftMarginInPdfUnits();

    // print label and its subLabels
    $subLabels = $labelDef->getSubLabels();
    foreach($subLabels as $subLabel) {
      $suby = $y - $fontSize - $subLabel->getTop();
      $subx = $x + $subLabel->getLeft();
      $subw = $labelDef->getWidthInPdfUnits() - $subLabel->getLeft();
      if ($subw <= 0) {
        $subw = $labelDef->getWidthInPdfUnits();
      }
      foreach($subLabel->getLines() as $line) {
        $resultLine = $line->getFormattedText($array);

//echo "list=".H($line->isList())." groupBy=".H($line->getGroupBy())." text=".H($resultLine)."<br>";
        
        $pdf->addTextWrap($subx,$suby,$subw,$fontSize,$resultLine);
//      $pdf->addText($subx,$suby,$fontSize,$array[$line]);
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
//  exit();
  $pdf->stream();

?>
