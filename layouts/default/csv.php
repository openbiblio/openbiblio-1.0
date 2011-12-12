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
			## consider a single document from those selected
			$bib = $biblios->getOne($row['bibid']);
			if (!$bib['marc']) continue; // if no MARC data, skip it
			$marc = $bib['marc'];
			$flds = $marc->fields;

			## scan all fields of this document
			$item = array();	// new empty document container
			foreach ($flds as $fld) {
				## extract MARC Id components
				$tag = $fld->tag;
				$sub = $fld->subfields[0]->identifier;
				## construct a list of all tags used
				if if (($tag == 20) || (($tag >= 99) && ($tag <= 300)) || ($tag==505)) {
					## create MARC Id as concatenation of tag & subfield id
					$marcId = $tag.$sub;
					## add this MarcId to list, if not already present
					if (!in_array($marcId, $tags)) {
						$tags[] = $marcId;
					}
					## only save first occurrence of any MarcId
					if (! array_key_exists($marcId, $item)) {
						$item[$marcId] = $fld->subfields[0]->data;
					}
				}
			}
			//ksort($item); //no need to sort document content since they are extracted in MarcId order
			$collection[] = $item;
		}
		natsort($tags); // ensure sort order is lexically correct
		
		echo "<pre>";
		## first we print the column headings
		$outStr = '';
		foreach ($tags as $tag){
			$outStr .= "$tag,";	// NOTE comma seperator has no trailing spaces
		}
		$out = substr($outStr,0,-1); // remove last trailing seperator
		echo $out."\n";
		
		## then we print the citation string
		foreach ($collection as $item) {
			$outStr = '';
			## extract item values in MarcId order, nomatter how stored
			foreach ($tags as $tag){
				## does this item have a value for this MarcId
				if (array_key_exists($tag, $item))
					$val = trim($item[$tag]);	// actual value from database
				else
					$val = "";								// empty field to keep columns aligned
				## clean up trailing trash
				$val = trim(preg_replace('/[,:;.\s]$/',"",$val));	// remove any trailing char listed within '[]'
				$val = preg_replace('/\'\'/',"'",$val); // embeded doubled single quotes converted to single
				$val = '"'.$val.'"';	// ensure all data is within '"'
				$outStr .= "$val,";		// NOTE comma seperator has no trailing spaces
			}
			$out = substr($outStr,0,-1); // remove trailing seperator from last field
			echo $out."\n";
		}
		echo "</pre>";
	}
}
