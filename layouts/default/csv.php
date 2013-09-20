<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, '../../classes/Biblio.php'));

/**
 * this class generates the CSV file output for a report
 * @author F.LaPlante
 */

class Layout_csv {
	public function render($rpt) {
		$tags = array();
		$collection = array();
		$cache = $rpt->getCache();
		$nBibs = $cache['count'];
		for ($i=0; $i<$nBibs; $i++) {
			## consider a single document from those selected
			$row = $cache['rows'][$i];
			$bib = new Biblio($row['bibid']);
			$biblio = $bib->getData();
			if (!$biblio['marc']) continue; // if no MARC data, skip it
			$marc = $biblio['marc'];

			## scan all fields of this document
			$item = array();	// new empty document container
			foreach ($marc as $k=>$fld) {
				## extract MARC Id components
				$parts = explode('$', $k);
				$tag = $parts[0]; //$fld->tag;
				if ($tag == 'LDR') continue;
				$sub = $parts[1]; //$fld->subfields[0]->identifier;
				if ($sub == '') continue;
				## construct a list of all tags used
				if (($tag == 20) || (($tag >= 99) && ($tag <= 300)) || ($tag==505)) {
					## create MARC Id as concatenation of tag & subfield id
					$marcId = $k; //$tag.$sub;
					## add this MarcId to list, if not already present
					if (!in_array($marcId, $tags)) {
						$tags[] = $marcId;
					}
					## only save first occurrence of any MarcId
					if (! array_key_exists($marcId, $item)) {
						$item[$marcId] = $fld['value']; //subfields[0]->data;
					}
				}
			}
			//ksort($item); //no need to sort document content since they are extracted in MarcId order
			$collection[] = $item;
		}
		natsort($tags); // ensure sort order is lexically correct

		echo "<h3>Select the following text and Save to a '.CSV' file of your choice</h3>\n";
		echo "<hr>";
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
		echo "<hr>";
	}
}
