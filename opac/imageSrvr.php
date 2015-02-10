<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../model/BiblioImages.php"));
	//print_r($_REQUEST);echo "<br />";

	##### do NOT use " on these items #####
	$map['callno'] = ['099$a'];
	$map['title'] = ['245$a', '240$a', '246$a'];
	$map['author'] = ['100$a'];

	switch ($_REQUEST['mode']) {
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
	  break;

	case "getPage":
		$db = new BiblioImages;
		$orderBy = $_GET['orderBy'];
		$rslt = $db->getBiblioMatches($map[$orderBy],$orderBy);
		$numRows = $rslt->num_rows;

		// add amount of search results.
		$perPage = Settings::get('items_per_page');
		if($_REQUEST['firstItem'] == null){
			$firstItem = 0;
		} else {
			$firstItem = $_REQUEST['firstItem'];
		}
		if($perPage <= ($numRows - $firstItem)){
			$lastItem = $firstItem + $perPage;
		} else {
			$lastItem = $numRows;
		}

		## record header
		$rcd['nmbr'] = $numRows;
		$rcd['firstItem'] = $firstItem;
		$rcd['lastItem'] = $lastItem;
		$rcd['perPage'] = $perPage;
		$rcd['columns'] = Settings::get('item_columns');
		$rcd['fotoWidth'] = Settings::get('thumbnail_width');

		$imgCntr = 0;
		$tbl = array();
		while($row = $rslt->fetch_assoc()) {
			$imgCntr++;
			if($imgCntr-1 < $firstItem) continue;
			if($imgCntr   > $lastItem) break;
			if ($col == 7) {
				$col = 0;
			}
			$tbl[] = ["bibid"=>$row['bibid'],"url"=>$row['url'],$orderBy=>$row['data']];
			
			$col++;
		}

		$rcd['tbl'] = $tbl;
		echo json_encode($rcd);
		break;

	default:
		  echo '<h4 class="error">'.T("invalid mode")."@imageSrvr.php: &gt;".$_REQUEST['mode']."&lt;</h4><br />";
		break;
	}

