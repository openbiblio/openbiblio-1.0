<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = cataloging;
$restrictInDemo = true;

require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../classes/Marc.php"));

if (count($_POST) == 0 or !isset($_POST['bibid'])) {
	header("Location: ../catalog/index.php");
	exit();
}

$biblio = array(
	'bibid'=>$_POST["bibid"],
	'material_cd'=>$_POST["materialCd"],
	'collection_cd'=>$_POST["collectionCd"],
	'last_change_userid'=>$_SESSION["userid"],
	'opac_flg'=>isset($_POST["opacFlg"]) ? 'Y' : 'N'
);

function marcError($str) {
	$_SESSION['pageErrors'] = array('marc'=>$str);
	$_SESSION['postVars'] = $_POST;
	$msg = T('biblioMarcEditError');
	header("Location: ../catalog/biblio_marc_edit_form.php?msg=".U($msg));
	exit();
}

$parser = new MarcMnemParser();
$err = $parser->parse($_POST["marc"]);
if (is_a($err, MarcParseError)) {
	marcError($err->toStr());
}
$err = $parser->eof();
if (is_a($err, MarcParseError)) {
	marcError($err->toStr());
}
if (count($parser->records) != 1) {
	marcError(T("You must enter exactly 1 record"));
}

$biblio['marc'] = $parser->records[0];

#**************************************************************************
#*  Insert/Update bibliography
#**************************************************************************
$biblios = new Biblios();
$biblios->update($biblio);

$msg = T("Item successfully updated.");
header("Location: ../catalog/biblio_marc_edit_form.php?bibid=".$biblio['bibid']."&msg=".U($msg));
exit();
