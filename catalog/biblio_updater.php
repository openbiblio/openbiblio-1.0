<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

//require_once("../shared/common.php");

$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../classes/Marc.php"));

#****************************************************************************
#*  Checking for post vars.  Go back to search if none found.
#****************************************************************************
if (count($_POST) == 0) {	exit(); }

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
	return array($c, cmp);
}

#****************************************************************************
#*  Validate data
#****************************************************************************
$biblios = new Biblios();
if ($_POST["bibid"]) {
	$biblio = $biblios->getOne($_POST["bibid"]);
} else {
	$biblio = array(marc=>new MarcRecord);
}
assert($biblio != NULL);
$biblio[material_cd] = $_POST["materialCd"];
$biblio[collection_cd] = $_POST["collectionCd"];
$biblio[last_change_userid] = $_POST["userid"];
$biblio[opac_flg] = isset($_POST["opacFlg"]) ? Y : N;

/* Construct a list of changed fields. */
$fields = array();
/* Because of the way this list is constructed, only one
 * new field with a particular tag may be added at once.
 * Also, within a field, only one subfield with a particular
 * identifier may be added at once.  This should be quite
 * sufficient for the easy-edit interface.
 */
foreach ($_POST[fields] as $f) {
print_r($f);echo"<br />";

	if (strlen($f[tag]) != 3 or strlen($f[subfield_cd]) != 1) {
		continue;
	}
	$fidx = $f[tag].'-';
	if ($f['fieldid']) {
		$fidx .= $f['fieldid'];
	} else {
		$fidx .= 'new';
	}
	if (!is_array($fields[$fidx])) {
		$fields[$fidx] = array();
	}
	$sfidx = $f['subfield_cd'].'-';
	if ($f['subfieldid']) {
		$sfidx .= $f['subfieldid'];
	} else {
		$sfidx .= 'new';
	}

	$fields[$fidx][$sfidx] = new MarcSubfield($f[subfield_cd], trim($f[data]));
}

$mrc = new MarcRecord();
$mrc->setLeader($biblio[marc]->getLeader());

foreach ($biblio[marc]->fields as $f) {
	$fidx = $f->tag .'-'. $f->fieldid;
	if (is_a($f, 'MarcControlField') or !array_key_exists($fidx, $fields)) {
		array_push($mrc->fields, $f);
		continue;
	}
	$fld = new MarcDataField($f->tag, $f->indicators);
	/* Add/remove current/updated/deleted fields/subfields */
	foreach ($f->subfields as $sf) {
		$sfidx = $sf->identifier .'-'. $sf->subfieldid;
		if (!array_key_exists($sfidx, $fields[$fidx])) {
			array_push($fld->subfields, $sf);
		} else if (strlen($fields[$fidx][$sfdix]->data) != 0) {
			array_push($fld->subfields, $fields[$fidx][$sfidx]);
			unset($fields[$fidx][$sfidx]);
		}
	}
	foreach($fields[$fidx] as $sf) {
		if (strlen($sf->data) != 0) {
			array_push($fld->subfields, $sf);
		}
	}
	unset($fields[$fidx]);
	if (!empty($fld->subfields)) {
		array_push($mrc->fields, $fld);
	}
}

/* Add new fields */
foreach ($fields as $fidx => $subfields) {
	$fld = new MarcDataField(substr($fidx, 0, 3));
	foreach ($subfields as $sf) {
		if (strlen($sf->data) != 0) {
			array_push($fld->subfields, $sf);
		}
	}
	if (!empty($fld->subfields)) {
		array_push($mrc->fields, $fld);
	}
}

/* Sort subfields and apply "smart" processing for particular fields */
for ($i=0; $i < count($mrc->fields); $i++) {
	usort($mrc->fields[$i]->subfields, mkSubfieldCmp());
	/* Special processing for 245$a -- FIXME, this should be generalized */
	if ($mrc->fields[$i]->tag == 245) {
		/* No title added entry. */
		$mrc->fields[$i]->indicators{0} = 0;
		$a = $mrc->fields[$i]->getValue(a);
		/* Set non-filing characters */
//		if (eregi("^((a |an |the )?[^a-z0-9]*)", $a, $regs) and strlen($regs[1]) <= 9) {
		if (preg_match('/^((a |an |the )?[^a-z0-9]*)/i', $a, $regs) and strlen($regs[1]) <= 9) {
			$mrc->fields[$i]->indicators{1} = strlen($regs[1]);
		} else {
			$mrc->fields[$i]->indicators{1} = 0;
		}
	}
}
/* Set field display values -- TODO */
/* Sort fields by tag and display value */
function fieldCmp($a, $b) {
	$tagcmp = strcasecmp($a->tag, $b->tag);
	if ($tagcmp != 0) {
		return $tagcmp;
	}
	/* Use actual display values after we add them -- TODO */
	$dispa = implode(" ", $a->getValues());
	$dispb = implode(" ", $b->getValues());
	return strcasecmp($dispa, $dispb);
}
usort($mrc->fields, fieldCmp);

$biblio[marc] = $mrc;

#**************************************************************************
#*  Insert/Update bibliography
#**************************************************************************

if ($nav == "newconfirm") {
	$bibid = $biblios->insert($biblio);
} else {
	$bibid = $_POST["bibid"];
	$biblios->update($biblio);
}

echo T("Item successfully updated.");

//#### changed to eliminate an editing loop. Now goes directly to the new copy entry form - Fred
////header("Location: ../catalog/biblio_edit_form.php?bibid=".$bibid."&msg=".U($msg));
//header("Location: ../catalog/biblio_copy_new_form.php?resey=Y&bibid=".$bibid."&msg=".U($msg));
//exit();
