<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "mbr";
$focus_form_name = "barcodesearch";
$focus_form_field = "barcodeNmbr";

require_once(REL(__FILE__, "../functions/inputFuncs.php"));
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Members.php"));
require_once(REL(__FILE__, "../model/MemberTypes.php"));
require_once(REL(__FILE__, "../model/Sites.php"));
require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/History.php"));
require_once(REL(__FILE__, "../model/MaterialTypes.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../model/MemberAccounts.php"));
require_once(REL(__FILE__, "../classes/Report.php"));

if (count($_GET) == 0) {
	header("Location: ../circ/index.php");
	exit();
}

$mbrid = @$_GET["mbrid"];

$members = new Members;
$mbr = $members->maybeGetOne($mbrid);

if (!$mbr) {
	if (@$_GET['format'] == 'json') {
		header('Content-type: application/json');
		echo 'null';
		exit();
	} else {
		header('Location: ../circ/index.php');
		exit();
	}
}

// FIXME - handle these on the client end
$_SESSION['currentMbrid'] = $mbrid;
if (isset($_SESSION['pageErrors'])) {
	$mbr['errors'] = $_SESSION['pageErrors'];
}

$mbrTypes = new MemberTypes;
$mbr['type'] = $mbrTypes->getOne($mbr['classification']);
# FIXME - this should me the type's code, not description
if ($mbr['type']['description'] == 'denied') {
	$mbr['checkouts_allowed'] = false;
} else {
	$mbr['checkouts_allowed'] = true;
}

$sites = new Sites;
$mbr['site'] = $sites->getOne($mbr['siteid']);

$acct = new MemberAccounts;
$mbr['balance'] = $acct->getBalance($mbrid);

$mattypes = new MaterialTypes;
$types = $mattypes->getAll();
$materialTypeDm = array();
$materialImageFiles = array();
while ($type = $types->next()) {
	$materialTypeDm[$type['code']] = $type['description'];
	$materialImageFiles[$type['code']] = $type['image_file'];
}
$history = new History;
$biblios = new Biblios;
$bookings = new Bookings;
$copies = new Copies;
$checkouts = $copies->getMemberCheckouts($mbrid);
$mbr['checkouts'] = array();
while ($copy = $checkouts->next()) {
	$biblio = $biblios->getOne($copy['bibid']);
	$copy['title'] = $biblio['marc']->getValue('245$a');
	$copy['status'] = $history->getOne($copy['histid']);
	$copy['booking'] = $bookings->getByHistid($copy['histid']);
	if ($copy['booking'] == NULL) {
		Fatal::internalError(T("Broken histid/booking reference"));
	}
	$copy['booking']['days_late'] = $bookings->getDaysLate($copy['booking']);
	$copy['material_img_url'] = '../images/'.$materialImageFiles[$biblio['material_cd']];
	$copy['material_type'] = $materialTypeDm[$biblio['material_cd']];
	$mbr['checkouts'][] = $copy;
}

$holds = Report::create('holds');
$holds->init(array('mbrid'=>$mbrid));
$mbr['holds'] = array();
while ($hold = $holds->next()) {
	$mbr['holds'][] = $hold;
}

if (@$_GET['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($mbr);
	exit();
}

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
echo HTML(file_get_contents('mbr_view.jsont'), $mbr);
Page::footer();
