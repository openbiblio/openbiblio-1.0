<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "opac";
	if (isset($_REQUEST["tab"])) {
		$tab = $_REQUEST["tab"];
	}
	$_REQUEST['tab'] = $tab;

	$nav = "browse_images";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));


	function getRpt() {
		global $tab;
		if ($_REQUEST['searchType'] == 'previous') {
			$rpt = Report::load('Images');

			if ($rpt && $_REQUEST['rpt_order_by']) {
				$rpt = $rpt->variant(array('order_by'=>$_REQUEST['rpt_order_by']));
			}
			return $rpt;
		}

		$rpt = Report::create('images', 'Images');
		if (!$rpt) {
			return false;
		}
		$rpt->initCgi();
		return $rpt;
	}
	/* ---------------------------------------------------*/

	$rpt = getRpt();

	if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
		$currentPageNmbr = $_REQUEST["page"];
	} else {
		$currentPageNmbr = $rpt->curPage();
	}

	if (isset($_REQUEST["msg"])) {
		$msg = $_REQUEST["msg"];
	} else {
		$msg = '';
	}

	if ($tab == "opac") {
		//Page::header_opac(array('nav'=>$nav, 'title'=>''));
		Page::header(array('nav'=>$nav, 'title'=>''));
	} else {
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	}
	echo "<h3>Browse Images</h3>";

	if ($msg) {
		echo '<p class="error">'.H($msg).'</p><br /><br />';
	}

	# Display no results message if no results returned from search.
	if ($rpt->count() == 0) {
		echo "<p class=\"error\">".T("No images found")."</p>";
		 ;
		exit();
	}

?>

<!--**************************************************************************
		*  Printing result stats and page nav
		************************************************************************** -->
<?php
	echo $rpt->count().' '.T("results found.");
	$page_url = new LinkUrl("../shared/image_browse.php", 'page',
		array('type'=>'previous', 'tab'=>$tab));
	$disp = new ReportDisplay($rpt);
	echo $disp->pages($page_url, $currentPageNmbr);

	echo '<fieldset><table><tr>';
	$col = 0;
	$page = $rpt->pageIter($currentPageNmbr);
	while($row = $page->next()) {
		if ($col == 4) {
			echo '</tr><tr>';
			$col = 0;
		}
		echo '<td valign="bottom" align="center" style="padding-bottom: 15px">'
				.'	<a href="../shared/biblio_view.php?tab='.H($tab).'&amp;bibid='.H($row['bibid']).'">'
				.'		<img src="../photos/'.H($row['url']).'" width="100" height="150" /><br />'
				.			H($row['callnum'])
				.'	</a>'
				.'</td>';
		$col++;
	}
	echo '</tr></table></fieldset>';

	echo $disp->pages($page_url, $currentPageNmbr);
	 ;
