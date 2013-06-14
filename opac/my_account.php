<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "opac";
$nav = "account";

require_once(REL(__FILE__, "../model/Members.php"));
require_once(REL(__FILE__, "../model/Sites.php"));
require_once(REL(__FILE__, "../classes/Report.php"));
require_once(REL(__FILE__, "../classes/Links.php"));
require_once(REL(__FILE__, "../classes/InfoDisplay.php"));
require_once(REL(__FILE__, "../classes/TableDisplay.php"));


if (!isset($_SESSION['authMbrid'])) {
	header('Location: ../opac/index.php');
	exit();
}

$members = new Members;
$mbr = $members->getOne($_SESSION['authMbrid']);
$sites = new Sites;
$site = $sites->getOne($mbr['siteid']);

Page::header_opac(array('nav'=>$nav, 'title'=>''));

$d = new InfoDisplay;
$d->title = T("Member Information");
$d->buttons = array(array(T("Edit Info"), '../opac/edit_account.php'));
echo $d->begin();
echo $d->row(T("Name:"), H($mbr['last_name'].', '.$mbr['first_name']));
echo $d->row(T("Site:"), H($site['name']));
echo $d->row(T("Card Number").':', H($mbr['barcode_nmbr']));
echo $d->row(T("EmailAddress"), H($mbr['email']));
echo $d->row(T("SchoolGrade"), H($mbr['school_grade']));
echo $d->end();

$rpt = Report::create('checkouts');
$rpt->init(array('mbrid'=>$_SESSION['authMbrid']));
$t = new TableDisplay;
$t->title = T("Current Checkouts");
$t->columns = array(
	$t->mkCol(T("Barcode")),
	$t->mkCol(T("Price")),
	$t->mkCol(T("Title")),
	$t->mkCol(T("Date Due")),
	$t->mkCol(T("Days Late")),
);
echo $t->begin();
while ($r = $rpt->each()) {
	echo $t->row(
		H($r['barcode_nmbr']),
		H($r['price']),
		Links::mkLink(biblio, H($r['bibid']), H($r['title'])),
		H($r['due_dt']),
		H($r['days_late'])
	);
}
echo $t->end();

 ;
