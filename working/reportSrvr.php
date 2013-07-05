<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../classes/Report.php"));
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	//print_r($_REQUEST);echo "<br />";

	function getRpt() {
		//global $tab;
		if ($_REQUEST['searchType'] == 'previous') {
			$rpt = Report::load('Images');
			if ($rpt && $_REQUEST['orderBy']) {
				$rpt = $rpt->variant(array('order_by'=>$_REQUEST['orderBy']));
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


	switch ($_REQUEST['mode']) {
	case "getPage":
		$rpt = getRpt();
		$page_url = new LinkUrl("#", 'page', array('type'=>'previous', 'tab'=>$_REQUEST['tab']));
		$disp = new ReportDisplay($rpt);

		if (isset($_REQUEST["page"]) && is_numeric($_REQUEST["page"])) {
			$currentPageNmbr = $_REQUEST["page"];
		} else {
			$currentPageNmbr = $rpt->curPage();
		}

		$col = 0;
		$page = $rpt->pageIter($currentPageNmbr);
		$tbl = '<tbody><tr>';
		while($row = $page->next()) {
			if ($col == 7) {
				$tbl .= '</tr>'."\n".'<tr>'."\n";
				$col = 0;
			}
			$tbl .= '<td valign="bottom" align="center">'."\n"
						 .'	<div class="galleryBox">'."\n"
						 .'		<div><img src="../photos/'.H($row['url']).'" class="biblioImage hover" /></div>'."\n"
						 .'		<div class="smallType"><a href="../catalog/srchForms.php?tab='.H($tab).'&amp;bibid='.H($row['bibid']).'">'."\n"
						 .'			<output >'.H($row['callnum']).'</output>'."\n"
						 .'		</a></div>'."\n"
						 .'</td>'."\n";
			$col++;
		}
		$tbl .= '</tr></tbody>';
    $ndx = $disp->pages($page_url, $currentPageNmbr);
		$nmbr = $rpt->count();
		$curPage = $rpt->curPage();
		$rslt = ["nmbr"=>$nmbr, "ndx"=>$ndx, "tbl"=>$tbl, "curPage"=>$curPage];
		echo json_encode($rslt);
		break;

	default:
		  echo '<h4 class="error">'.T("invalid mode")."@calendarSrvr.php: &gt;".$_REQUEST['mode']."&lt;</h4><br />";
		break;
	}

