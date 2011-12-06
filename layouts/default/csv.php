<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../model/Biblios.php'));

class Layout_csv {
	function render($rpt) {
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
				$tag = $fld->tag;
				$sub = $fld->subfields[0]->identifier;
				## construct a list of all tags used
				if (($tag >= 100) && ($tag < 300)) {
					$marcId = $tag.$sub;
					if (!in_array($marcId, $tags)) {
						$tags[] = $marcId;
					}

					## save information for easy processing later
					if (! array_key_exists($marcId, $item)) {
						$item[$marcId] = $fld->subfields[0]->data;
					}
				}
			}
			ksort($item);
			$collection[] = $item;
		}
		natsort($tags);
		
		echo "<pre>";
		## first we print the column headings
		$outStr = '';
		foreach ($tags as $tag){
			$outStr .= "$tag,";
		}
		$out = substr($outStr,0,-1); // remove trailing chars
		echo $out."\n";
		
		## then we print the citation string
		foreach ($collection as $item) {
			$outStr = '';
			foreach ($tags as $tag){
				if (array_key_exists($tag, $item))
					$val = trim($item[$tag]);
				else
					$val = "";
				$val = trim(preg_replace('/[,:;.\s]$/',"",$val));	// trailing char
				$val = preg_replace('/\'\'/',"'",$val); // embeded quotes
				$val = '"'.$val.'"';
				$outStr .= "$val,";
			}
			$out = substr($outStr,0,-1); // remove trailing chars
			echo $out."\n";
		}
		echo "</pre>";
	}
}
