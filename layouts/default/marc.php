<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../model/Biblios.php'));

class Layout_marc {
	function render($rpt) {
		header('Content-Type: application/marc');
		header('Content-disposition: inline; filename="export.mrc"');
		$biblios = new Biblios;
		while ($row = $rpt->each()) {
			$bib = $biblios->getOne($row['bibid']);
			if (!$bib['marc']) {
				continue;
			}
			list($rec, $err) = $bib['marc']->get();
			assert('!$err');
			echo $rec;
		}
	}
}
