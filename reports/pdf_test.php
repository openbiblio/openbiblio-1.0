<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "reports";
  $nav = "runreport";

//  include("../shared/logincheck.php");
  include("../lib/pdf/class.pdf.php");
  require_once("../classes/LabelFormat.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  define("FONT_DIR","../lib/pdf/fonts/");
  define("PAGE_HEIGHT",792);
  define("PAGE_WIDTH",612);

//header("Content-type: application/pdf");
//header("Content-Disposition: inline; filename=pdf_test.pdf");

  #****************************************************************************
  #*  Validate criteria and run report
  #****************************************************************************
//$myvar = $_POST["label"];



  $pdf = new Cpdf();
  $pdf->selectFont("../lib/pdf/fonts/Helvetica.afm");
  $fontSize = 10;
  $startY = PAGE_HEIGHT;
  $y = $startY;
  $x = 72;
  $fontHeight = $pdf->getFontHeight($fontSize);
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 1');
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 2 <b>bold</b>');
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 3 <i>italics</i>');
  $pdf->stream();

//coordinates are 612x792
/*  $pdf->addText($x,$y,$fontSize,'Line 1');
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 2 <b>bold</b>');
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 3 <i>italics</i>');

  $pdf->newPage();

  $y = 700;
  $x = 72;
  $fontHeight = $pdf->getFontHeight($fontSize);
  $pdf->addText($x,$y,$fontSize,'Line 1 (page 2)');
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 2 <b>bold</b>');
  $y = $y - $fontHeight;
  $pdf->addText($x,$y,$fontSize,'Line 3 <i>italics</i>');
*/

?>
