<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once("../classes/Report.php");
	require_once("../classes/Params.php");
	require_once(REL(__FILE__, "../classes/ReportDisplay.php"));
	require_once(REL(__FILE__, "../classes/TableDisplay.php"));
	require_once(REL(__FILE__, "../classes/Iter.php"));

/**
 * backend api for reports package
 * all web pages should call here for services as needed
 * @author Fred LaPlante
 */

	//print_r($_POST);echo "<br />";

	##### do NOT use double quotes (") on these items #####
	//$map['callno'] = ['099$a'];
	//$map['title'] = ['245$a', '240$a', '246$a'];
	//$map['author'] = ['100$a'];

	switch ($_POST['mode']) {
	case 'getOpts':
		//$opts = Settings::getAll();
		//echo json_encode($opts);
        echo json_encode($_SESSION);
	  break;

	case "resetReport":
		//Report::clearCache();
		break;

	case "getCriteriaForm":
		$rpt = Report::create($_POST['type']);
		if (!$rpt) die("no report available");
		echo T($rpt->title())."~|~".$rpt->type()."~|~";
		Params::printForm($rpt->paramDefs());
	  break;

	case "getPage":
		## add amount of search results.
		$perPage = Settings::get('items_per_page');
		if($_POST['firstItem'] == null){
			$firstItem = 0;
			$page = 1;
		} else {
			$firstItem = $_POST['firstItem'];
			$page = floor($firstItem / $perPage)+1;
		}

		if ($_POST['type'] == 'previous') {
			$rpt = Report::load('Report', $firstItem, $perPage);
			if ($_POST['rpt_order_by']) {
				$rpt = $rpt->getVariant(array('order_by'=>$_POST['rpt_order_by']));
			}
		} else {
			$rpt = Report::create($_POST['type'], 'Report', $firstItem, $perPage);
			$errs = $rpt->initCgi_el();
			if (!empty($errs)) die($errs);
		}

		$numRows = $rpt->count();
		if ($numRows == 0) die(T("No results found."));
		if(((int)$numRows - (int)$firstItem) >= (int)$perPage ){
			$lastItem = (int)$firstItem + (int)$perPage;
		} else {
			$lastItem = (int)$numRows;
		}

		## record header
		$rcd['type'] = 'previous';
		$rcd['nmbr'] = (string)$numRows;
		$rcd['firstItem'] = (string)$firstItem;
		$rcd['lastItem'] = (string)$lastItem;
		$rcd['perPage'] = (string)$perPage;
		$rcd['columns'] = (string)Settings::get('item_columns');
		echo "| ".json_encode($rcd)."| ";

		$disp = new ReportDisplay($rpt);
		$t = new TableDisplay;

		// get column headings
		$t->columns = $disp->columns($sort_url);

		// create and display actual data rows
		// actual display of biblio content controlled by ../classes/BiblioRows.php (see function next())
		echo $t->begin();
		$pg = $rpt->pageIter($page);
		while ($r = $pg->next()) {
			echo $t->rowArray($disp->row($r));
		}
		echo $t->end();
	  break;

	default:
		  echo '<h4 class="error">'.T("invalid mode").": &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

