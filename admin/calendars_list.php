<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "admin";
$nav = "calendars";
require_once(REL(__FILE__, "../shared/logincheck.php"));

require_once(REL(__FILE__, "../classes/Report.php"));
require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
require_once(REL(__FILE__, "../classes/TableDisplay.php"));
require_once(REL(__FILE__, "../classes/Links.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

echo '<h3>'.T("Calendars").'</h3>';

if (isset($_REQUEST['msg'])) {
	echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
}

if ($_REQUEST['type'] == 'previous') {
	$rpt = Report::load('Report');
	if ($_REQUEST['rpt_order_by']) {
		$rpt = $rpt->variant(array('order_by'=>$_REQUEST['rpt_order_by']));
	}
} else {
	$rpt = Report::create('_calendars', 'Report');
	$rpt->initCgi();
}

if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
	$currentPageNmbr = $_REQUEST["page"];
} else {
	$currentPageNmbr = $rpt->curPage();
}

if ($rpt->count() == 0) {
	echo T("No calendars have been defined.");
	 ;
	exit();
}

$page_url = new LinkUrl("../admin/calendars_list.php",
	'page', array('type'=>'previous'));
$disp = new ReportDisplay($rpt);
echo '<div class="results_count">';
echo T("%count% calendars.", array('count'=>$rpt->count()));
echo '</div>';
echo $disp->pages($page_url, $currentPageNmbr);

echo "<fieldset>";
$sort_url = new LinkUrl("../admin/calendars_list.php",
	'rpt_order_by', array('type'=>'previous'));
$t = new TableDisplay;
$t->columns = $disp->columns($sort_url);
echo $t->begin();
$page = $rpt->pageIter($currentPageNmbr);
while ($r = $page->next()) {
	echo $t->rowArray($disp->row($r));
}
echo $t->end();
echo "</fieldset>";

echo $disp->pages($page_url, $currentPageNmbr);

require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
