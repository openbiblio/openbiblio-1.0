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

	if ($tab != "cataloging") {
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
		echo "<p class=\"error\">".T("No images found")."</p>\n";
		exit();
	}

?>

<!--**************************************************************************
		*  Printing result stats and page nav
		************************************************************************** -->
<?php
	echo '<div id="rptArea">'."\n";
	echo '<p>'.$rpt->count().' '.T("results found.")."</p>\n";
	$page_url = new LinkUrl("../shared/image_browse.php", 'page',
		array('type'=>'previous', 'tab'=>$tab));
	$disp = new ReportDisplay($rpt);
	echo $disp->pages($page_url, $currentPageNmbr);

	echo '<fieldset><table><tr>'."\n";
	$col = 0;
	$page = $rpt->pageIter($currentPageNmbr);
	while($row = $page->next()) {
		if ($col == 5) {
			echo '</tr>'."\n".'<tr>'."\n";
			$col = 0;
		}
		echo '<td valign="bottom" align="center">'."\n"
				.'	<div class="galleryBox">'."\n"
				.'		<div><img src="../photos/'.H($row['url']).'" class="biblioImage hover" /></div>'."\n"
				.'		<div class="smallType"><a href="../catalog/srchForms.php?tab='.H($tab).'&amp;bibid='.H($row['bibid']).'">'."\n"
				.'			<output >'.H($row['callnum']).'</output>'."\n"
				.'		</a></div>'."\n"
				.'</td>'."\n";
		$col++;
	}
	echo '</tr></table></fieldset>'."\n";

	echo $disp->pages($page_url, $currentPageNmbr);

	echo '</div>'."\n"; // end of rptArea

  require_once(REL(__FILE__,'../shared/footer.php'));
?>

<script language="JavaScript" defer>
"use strict";
	$(document).ready(function () {
		$('#rptArea').show();
	});
</script>

</body>
</html>
