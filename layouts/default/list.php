<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Lay.php'));


class Layout_list {
	function write_header(&$pdf, $column_names) {
		$pdf->SetFillColor(255, 0, 0);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(128, 0, 0);
		$pdf->SetLineWidth(0.3);
		$pdf->SetFont('', 'B');

		$num_headers = count($column_names);
		$width = (($pdf->getPageWidth()-($pdf->getMargins()['left'] + $pdf->getMargins()['right']))/$num_headers);
                for($i = 0; $i < $num_headers; ++$i) {
                        $pdf->Cell($width, 7, $column_names[$i], 1, 0, 'C', 1);
                }
                $pdf->Ln();

                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

	}

	function render($rpt) {

		$pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetCreator($_SESSION['username']);
		$pdf->SetAuthor($_SESSION['username']);
		$pdf->SetTitle('OpenBiblio Report');

		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->SetFont('helvetica', '', 12);
		$pdf->AddPage();

		$this->heightWithoutMargins = $pdf->getPageHeight() - ($pdf->getMargins()['top'] + $pdf->getMargins()['bottom']);

		$header = [];
		$data_raw = $rpt->columns();
		foreach($data_raw as $entry) {
			if (!array_key_exists('hidden', $entry)) {
				$header[] = $entry['name'];
			}
		}
		$num_headers = count($header);

		$this->write_header($pdf, $header);

                $width = (($pdf->getPageWidth()-($pdf->getMargins()['left'] + $pdf->getMargins()['right']))/$num_headers);

        	$fill = 0;
		$count_rows = $rpt->count();
		for ($i = 1; $i < $count_rows; $i++) {
			$cell_count = [];
			$startY = $pdf->GetY();
			for($j = 0; $j < $num_headers; ++$j) {
            			$cell_count[] = $pdf->getNumLines($rpt->row($i)[$header[$j]], $width);
			}
			if (($startY + (max($cell_count) * 6)) >= $this->heightWithoutMargins) {
				$pdf->AddPage();
				$startY = $pdf->setY($pdf->getMargins()['top']);
				$this->write_header($pdf, $header);
				$startY = $pdf->getY();
			}
			for($j = 0; $j < $num_headers; ++$j) {
            			$pdf->MultiCell($width, (6 * max($cell_count)), $rpt->row($i)[$header[$j]], 'LRB', 'L', $fill, 0);
			}
            		$pdf->Ln();
            		$fill=!$fill;
        	}

		$pdf->Output('example_011.pdf', 'I');
	}
}
