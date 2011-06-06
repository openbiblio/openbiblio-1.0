<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "circulation";
$nav = "search";
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../classes/Report.php"));

if (empty($_REQUEST)) {
	header("Location: ../circ/index.php");
	exit();
}

if ($_REQUEST['type'] == 'previous') {
	$rpt = Report::load('MemberSearch');

	if ($rpt && $_REQUEST['rpt_order_by']) {
		$rpt = $rpt->variant(array('order_by'=>$_REQUEST['rpt_order_by']));
	}
} else {
	$rpt = Report::create('member_search', 'MemberSearch');
	$rpt->initCgi();
}

if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
	$currentPageNmbr = $_REQUEST["page"];
} else {
	$currentPageNmbr = $rpt->curPage();
}

function pageLink($page) {
	return URL('../circ/mbr_search.php?type=previous'
		. '&page={page}', array('page'=>$page));
}

$page_data = array(
	'pagination'=>Page::getPagination($rpt->count(), $currentPageNmbr, 'pageLink'),
	'results'=>$rpt->pageIter($currentPageNmbr)->toArray(),
	'current_bookingid'=>@$_SESSION['currentBookingid'],
	'sort_urls'=>array(),   // filled in below
	'sort_imgs'=>array(), // filled in below
);

foreach (array('name', 'barcode_nmbr', 'site_name') as $sort) {
	if (@$_REQUEST['rpt_order_by'] == $sort) {
		$page_data['sort_urls'][$sort] = URL('../circ/mbr_search.php?type=previous'
			. '&rpt_order_by={@}', $sort.'!r');
		$page_data['sort_imgs'][$sort] = '<img class="sort_arrow" src="../images/down.png" alt="&darr;" />';
	} else if (@$_REQUEST['rpt_order_by'] == $sort.'!r') {
		$page_data['sort_urls'][$sort] = URL('../circ/mbr_search.php?type=previous'
			. '&rpt_order_by={@}', $sort);
		$page_data['sort_imgs'][$sort] = '<img class="sort_arrow" src="../images/up.png" alt="&uarr;" />';
	} else {
		$page_data['sort_urls'][$sort] = URL('../circ/mbr_search.php?type=previous'
			. '&rpt_order_by={@}', $sort);
		$page_data['sort_imgs'][$sort] = '';
	}
}

if (@$_REQUEST["format"] == "json") {
	header('Content-type: application/json');
	echo json_encode($page_data);
	exit();
}

if ($rpt->count() == 1) {
	$mbr = $rpt->row(0);
	header("Location: ".URL("../circ/mbr_view.php?mbrid={mbrid}&reset=Y", $mbr));
	exit();
}

Nav::node('circulation/search/member_list', T("Print List"), '../shared/layout.php?name=member_list&rpt=MemberSearch');
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

# FIXME - do something better with this
$page_data['checkbox_script'] = file_get_contents('mbr_search.js');
echo HTML(file_get_contents('mbr_search.jsont'), $page_data);

 ;
