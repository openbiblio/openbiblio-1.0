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

  require_once("../shared/common.php");
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