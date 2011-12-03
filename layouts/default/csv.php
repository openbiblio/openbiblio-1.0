<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../model/Biblios.php'));

class Layout_csv {
	function render($rpt) {
//		header('Content-Type: application/text');
//		header('Content-disposition: inline; filename="export.txt"');
		$biblios = new Biblios;
		while ($row = $rpt->each()) {
			$bib = $biblios->getOne($row['bibid']);
			if (!$bib['marc']) {
				continue;
			}
//var_dump($bib);echo "\r\n";
//echo ".=.=.=.=.=.=.=.=.=.=.=.\r\n";
			$marc = $bib['marc'];
//var_dump($marc);echo "\r\n";
//echo ".=.=.=.=.=.=.=.=.=.=.=.\r\n";
			$flds = $marc->fields;
//var_dump($flds);echo "\r\n";
			foreach ($flds as $fld) {
//var_dump($fld);echo "\r\n";
				$tag = $fld->tag;
				$sub = $fld->subfields[0]->identifier;
				$val = $fld->subfields[0]->data;
				echo "$tag$sub ==>> $val<br />\r\n";
//echo ". . . . . . .\r\n";
			}
echo "-----------------------------------------<br />\r\n";
//exit;
		}
	}
}
