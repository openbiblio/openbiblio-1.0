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
		$tags = array();
		$collection = array();
		while ($row = $rpt->each()) {
			$bib = $biblios->getOne($row['bibid']);
			if (!$bib['marc']) {
				continue;
			}
			$marc = $bib['marc'];
			$flds = $marc->fields;

			$item = array();
			foreach ($flds as $fld) {
//var_dump($fld);echo "\r\n";
				$tag = $fld->tag;
				$sub = $fld->subfields[0]->identifier;
				$val = $fld->subfields[0]->data;
				$marcId = $tag.$sub;
				## construct a list of all tags used
				if (! in_array($marcId, $tags)) $tags[] = $marcId;
				## save information for easy processing
				if (! array_key_exists($marcId, $item)) $item[$marcId] = $val;
				//echo "$tag$sub ==>> $val<br />\r\n";
//echo "<br />. . . . . . .<br />\r\n";
			}
			ksort($item);
			$collection[] = $item;
//exit;
		}
		sort($tags);
		
		## first we print the column headings
		//print_r($tags);;echo "<br />\r\n";
		foreach ($tags as $tag){
			$t = substr($tag,0,3);
			if (($t >= 100) && ($t < 300)) echo "$tag; ";
		}
		echo "<br />\r\n-----------------------------------------<br />\r\n";
		
		## then we print the citation string
		foreach ($collection as $item) {
			//print_r($item);echo "<br />\r\n";
			foreach ($item as $key=>$val){
				$tag = substr($key,0,3);
				if (($tag >= 100) && ($tag < 300)) {
					if ($tag == 245) $val = '"'.$val.'"';
					echo "$val; ";
				}
			}
			echo "<br />\r\n. . . . . . .<br />\r\n";
		}
		echo "-----------------------------------------<br />\r\n";
		//print_r($collection);
	}
}
