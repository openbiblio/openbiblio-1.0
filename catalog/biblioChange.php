<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once(REL(__FILE__, "../classes/Marc.php"));

/* Not for stand-alone use. Normally 'included' in some other php block */

/* Closure class for sorting subfields */
class SubfieldOrder {
	var $order;
	function SubfieldOrder($order=NULL) {
		if ($order !== NULL) {
			$this->order = strtolower($order);
		} else {
			$order = abcdefghijklmnopqrstuvwxyz0123456789;
		}
	}
	function cmp($a, $b) {
		$apos = strpos($this->order, strtolower($a->identifier));
		$bpos = strpos($this->order, strtolower($b->identifier));
		if ($apos === false and $bpos === false) {
			return strcasecmp($a->identifier, $b->identifier);
		} else if ($apos === false) {
			return -1;
		} else if ($bpos === false) {
			return 1;
		} else {
			return $apos-$bpos;
		}
	}
}

function mkSubfieldCmp($order=NULL) {
	$c = new SubfieldOrder($order);
	//return array($c, cmp);
	return $c->cmp;
}
function fieldCmp($a, $b) {
	$tagcmp = strcasecmp($a->tag, $b->tag);
	if ($tagcmp != 0) {
		return $tagcmp;
	}
	if (empty($a->tag) || empty($b->tag)) return 0;

	/* Use actual display values after we add them -- TODO */
	$dispa = implode(" ", $a->getValues());
	$dispb = implode(" ", $b->getValues());
	return strcasecmp($dispa, $dispb);
}

function postBiblioChange($nav) {
	/**
	 * expected input data format
	 * bibid=nnnn
	 * collectionCd=aa
	 * fields[245$a]['codes']='subfieldid=xx&fieldid=yy'
	 * fields[245$a]['data']='another testing'
	 *
	 * old format was:
	 * bibid=nnnnn
	 * collectionCd=aa
	 * fields[$250$a]['tag']=250
	 * fields[$250$a]['subfield_cd']=aa
	 * fields[$250$a]['fieldidid']=nnnn
	 * fields[$250$a]['subfieldid']=nnnnn
	 * fields[$250$a]['data']='another testing'
	 */
	## --------------------------------------------- ##
	## convert new format to old for MARC processing ##
	function expand ($t, $f) {
		list($tag, $suf, $rep) = explode('$', $t);
    $f['tag'] = $tag;
    $f['subfield_cd'] = $suf;
		$codes = explode('&', $f['codes']);
		$tmp = explode('=',$codes[1]);
		$f['fieldid'] = $tmp[1];
		$tmp = explode('=',$codes[0]);
		$f['subfieldid'] = $tmp[1];
    ## fields[$250$a]['data'] same for both formats
		return $f;
	}
	## --------------------------------------------- ##

	## --- Construct a list of changed fields. --- ##
	/* Because of the way this list is constructed, only one
	 * new field with a particular tag may be added at once.
	 * Also, within a field, only one subfield with a particular
	 * identifier may be added at once.  This should be quite
	 * sufficient for the easy-edit interface.
	 */
	$fields = array();

//	foreach ($_POST['fields'] as $f) {
	foreach ($_POST['fields'] as $tf=>$f) {
		$f = expand($tf,$f);
//echo "update field-tf {$tf}==>";print_r($f);echo"<br/>\n";
//echo "working field-tf {$tf}<br/>\n";
		if ((strlen($f['tag']) < 3) or (strlen($f['subfield_cd']) > 1)) {
			echo "f: Encountered SHORT marc code '{$f['tag']}'<br />or too long subfield code '{$f['subfield']}'.<br/>\n";
			continue;
		}
		$fidx = $f['tag'].'-';

		## Only do this when there is no field yet with this field value
		$fidxSuffix = null;
		foreach ($_POST['fields'] as $ts=>$s){
		$s = expand($ts,$s);
//echo "update field-ts {$ts}==>";print_r($s);echo"<br/>\n";
//echo "working field-ts {$ts}<br/>\n";
			if (strlen($s['tag']) != 3 or strlen($s['subfield_cd']) != 1) {
				echo "s: Encountered SHORT marc code '{$s['tag']}'<br />or too long subfield code '{$s['subfield_cd']}'.<br/>\n";
				continue;
			}
			if($s['tag'] == $f['tag']){
				if ($s['fieldid']) {
					$fidxSuffix = $s['fieldid'];
				} elseif(!isset($fidxSuffix)) {
					$fidxSuffix = 'new';
				}			
			}
		}	
		$fidx .= $fidxSuffix;
		
		if (!is_array($fields[$fidx])) {
			$fields[$fidx] = array();
		}
		$sfidx = $f['subfield_cd'].'-';
		if ($f['subfieldid']) {
			$sfidx .= $f['subfieldid'];
		} else {
			$sfidx .= 'new';
		}

		//$fields[$fidx][$sfidx] = new MarcSubfield($f[subfield_cd], trim($f[data]));
		if (!array_key_exists($sfidx,$fields[$fidx])) {
			$fields[$fidx][$sfidx] = new MarcSubfield($f['subfield_cd'], stripslashes(trim($f['data'])));
		}
	}
echo"fields===>";print_r($fields);echo"<br/>\n";

	$mrc = new MarcRecord();
	//$mrc->setLeader($biblio[marc]->getLeader());
	$ldr = $mrc->getLeader();
	$mrc->setLeader($ldr);

	foreach ($_POST['fields'] as $tf=>$f) {
echo"field===>";print_r($_POST['fields'][$tf]);echo"<br/>\n";
		$f = expand($tf,$f);
		$fidx = $f['tag'] .'-'. $f['fieldid'];
		if (is_a($f, 'MarcControlField') or !array_key_exists($fidx, $fields)) {
			//array_push($mrc->fields, $f);
			$mrc->addFields($f);  ## adds $f to the mrc array
			continue;
		}
//		$fld = new MarcDataField($f->tag, $f->indicators);
		$fld = new MarcDataField($f['tag'], $f['subfield_cd']); ## new $fld has indicators & subfields
		## Add/remove current/updated/deleted fields/subfields ##
//		foreach ($f->subfields as $sf) {
		foreach ($fld as $sf) {
echo"sf===>";print_r($sf);echo"<br/>\n";
			$sfidx = $sf->indicator .'-'. $f['subfieldid'];
			if (!array_key_exists($sfidx, $fields[$fidx])) {
				array_push($fld->subfields, $sf);
			} else if (strlen($fields[$fidx][$sfdix]->data) != 0) {
				array_push($fld->subfields, $fields[$fidx][$sfidx]);
				unset($fields[$fidx][$sfidx]);
			}
		}
		foreach($fields[$fidx] as $sf) {
			//if (strlen($sf->data) != 0) {
			if (!empty($sf->data)) {
				array_push($fld->subfields, $sf);
			}
		}
		unset($fields[$fidx]);
		if (!empty($fld->subfields)) {
			//array_push($mrc->fields, $fld);
			$mrc->addFields($fld);
		}
	}
echo"mrc before 'add'===>";print_r($mrc);echo"<br/>\n";

	/* Add new fields */
	foreach ($fields as $fidx => $subfields) {
		$fld = new MarcDataField(substr($fidx, 0, 3));
		foreach ($subfields as $sf) {
			if (strlen($sf->data) != 0) {
				array_push($fld->subfields, $sf);
			}
		}
		if (!empty($fld->subfields)) {
			//array_push($mrc->fields, $fld);
			$mrc->addFields($fld);
		}
	}
echo"mrc after 'add'===>";print_r($mrc);echo"<br/>\n";

// ----- following does not appear to be used ----
///* Sort subfields and apply "smart" processing for particular fields */
//	$fields = $mrc->getFields();
//	for ($i=0; $i < count($fields); $i++) {
//		//usort($mrc->fields[$i]->subfields, mkSubfieldCmp());
//		/* Special processing for 245$a -- FIXME, this should be generalized */
//		if ($fields[$i]->tag == 245) {
//			/* No title added entry. */
//			$fields[$i]->indicators{0} = 0;
//			$a = $fields[$i]->getValue(a);
//			/* Set non-filing characters */
//			if (preg_match("/^((a |an |the )?[^a-z0-9]*)/i", $a, $regs) and strlen($regs[1]) <= 9) {
//				$fields[$i]->indicators{1} = strlen($regs[1]);
//			} else {
//				$fields[$i]->indicators{1} = 0;
//			}
//		}
//	}
//
//	/* Set field display values -- TODO */
//	/* Sort fields by tag and display value */
//	usort($fields, fieldCmp);
////echo"fields revised&sorted===>";print_r($mrc);echo"<br/>\n";

	## prepare the update/insert structure
	## note: relocated from top of function to clarify useage ##
	if (!isset ($biblios)) $biblios = new Biblios();
	if ($_POST["bibid"]) {
		## update existing, so get copy of DB data
		$biblio = $biblios->getOne($_POST["bibid"]);
	} else {
		## create new empty structure
		$biblio = array(marc=>new MarcRecord);
	}
	assert($biblio != NULL);
echo "biblio==>";print_r($biblio);echo"<br/>\n";

  ## over-write with update material
	$biblio['marc'] = $mrc;
	if (empty($_POST['material_cd']))
		$biblio['material_cd'] = $_POST["materialCd"];
	else
		$biblio['material_cd'] = $_POST['material_cd'];
	if (empty($_POST['material_cd']))
		$biblio['collection_cd'] = $_POST["collectionCd"];
	else
		$biblio['collection_cd'] = $_POST['collection_cd'];
	$biblio['last_change_userid'] = $_POST["userid"];
	$biblio['opac_flg'] = isset($_POST["opac_flg"]) ? Y : N;

	#**************************************************************************
	#*  Insert/Update bibliography
	#**************************************************************************
	if ($nav == "newconfirm") {
echo "biblio rdy to insert==>";print_r($biblio);echo"<br/>\n";
		$bibid = $biblios->insert($biblio);
		$msg = '{"bibid":"' . $bibid .'"}';
	} else {
echo "biblio rdy to update<br/>\n";
		$bibid = $_POST["bibid"]; /** ??? what's this for ??? **/
		$biblios->update($biblio);
//echo "back from updating<br/>\n";
		// system assumes ANY OTHER message implies failure
		// dont change this string unless you are VERY sure
		$msg = "!!success!!";
	}
	return $msg;
}

