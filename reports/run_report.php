<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab="reports";
	$nav="results";
	if (isset($_REQUEST['tab'])) {
		$tab = $_REQUEST['tab'];
	}
	if (isset($_REQUEST['nav'])) {
		$nav = $_REQUEST['nav'];
	}
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Links.php"));

	if (!$_REQUEST['type']) {
		header('Location: ../reports/index.php');
		exit(0);
	}
	if ($_REQUEST['type'] == 'previous') {
		$rpt = Report::load('Report');
	} else {
		$rpt = Report::create($_REQUEST['type'], 'Report');
	}
	if (!$rpt) {
		header('Location: ../reports/index.php');
		exit(0);
	}

	if ($_REQUEST['type'] == 'previous') {
		if ($_REQUEST['rpt_order_by']) {
			$rpt = $rpt->variant(array('order_by'=>$_REQUEST['rpt_order_by']));
		}
	} else {
		$errs = $rpt->initCgi_el();
		if (!empty($errs)) {
			FieldError::backToForm('../reports/report_criteria.php', $errs);
		}
	}
	if ($_REQUEST['page']) {
		$page = $_REQUEST['page'];
	} else {
		$page = $rpt->curPage();
	}

	foreach ($rpt->layouts() as $l) {
		if ($l['title']) {
			$title = $l['title'];
		} else {
			$title = $l['name'];
		}
		Nav::node('reports/results/'.$l['name'], $title,
			'../shared/layout.php?rpt=Report&name='.U($l['name']));
	}
	Nav::node('reports/results/list', T("Print List"),
		'../shared/layout.php?rpt=Report&name=list');
	Nav::node('reports/reportcriteria', T("Report Criteria"),
		'../reports/report_criteria.php?type='.U($rpt->type()));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Report Results"); ?></h3>
<?php
	if (isset($_REQUEST["msg"]) && !empty($_REQUEST["msg"])) {
		echo '<p class="error">'.H($_REQUEST["msg"]).'</p><br /><br />';
	}

	if ($rpt->count() == 0) {
		echo T("No results found.");
		Page::footer();
		exit();
	}

	$p = array('type'=>'previous', 'tab'=>$tab, 'nav'=>$nav);
	$page_url = new LinkUrl("../reports/run_report.php",
		'page', $p);
	$sort_url = new LinkUrl("../reports/run_report.php",
		'rpt_order_by', $p);
	$disp = new ReportDisplay($rpt);
	echo '<div class="results_count">';
	echo T("%count% results found.", array('count'=>$rpt->count()));
	echo '</div>';
	echo $disp->pages($page_url, $page);
	//echo '<p>'.$rpt->count().' results found.</p>';
?>
<!--table class="resultshead">
	<tr>
			<th><?php echo T("Report Results"); ?></th>
		<td class="resultshead">
<table class="buttons">
<tr-->
<?php
# Fill in report actions here
?>
<!--/tr>
</table>
</td>
	</tr>
</table-->
<fieldset>
<?php
	$t = new TableDisplay;
	$t->columns = $disp->columns($sort_url);
	echo $t->begin();
	$pg = $rpt->pageIter($page);
	while ($r = $pg->next()) {
		echo $t->rowArray($disp->row($r));
	}
	echo $t->end();

	echo $disp->pages($page_url, $page);
?>
</fieldset>
<?php
	Page::footer();
