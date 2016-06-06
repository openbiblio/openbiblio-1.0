<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

//require_once(REL(__FILE__, '../../model/Biblios.php'));
require_once(REL(__FILE__, '../../model/MarcStore.php'));

/**
 * this class generates the MARC file output for a report
 * @author (orig) Micah Stetson
 * @author (mod) Fred LaPlante
 */

class Layout_marc {
	public function render($rpt) {
/*
		header('Content-Type: application/marc');
		header('Content-disposition: inline; filename="export.mrc"');
		$biblios = new Biblios;
		while ($row = $rpt->each()) {
			$bib = $biblios->getOne($row['bibid']);
			if (!$bib['marc']) continue;

			list($rec, $err) = $bib['marc']->get();
			assert('!$err');
			echo $rec;
		}
*/
		$cache = $rpt->getCache();
		$nBibs = $cache['count'];
		$marc = new MarcStore();
		for ($i=0; $i<$nBibs; $i++) {
			## consider a single document from those selected
			$row = $cache['rows'][$i];
echo "bibid==>{$row['bibid']}<br/>\n";
			$bib = $marc->get($row['bibid']);
var_dump($bib);echo"<br/>\n";
		}
	}
}
