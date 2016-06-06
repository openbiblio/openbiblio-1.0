<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../model/BiblioImages.php"));
	//print_r($_POST);echo "<br />";

	##### do NOT use " on these items #####
	$map['callno'] = array('099$a');
	$map['title'] = array('245$a', '240$a', '246$a');
	$map['author'] = array('100$a');

	switch ($_POST['mode']) {
	/* obsolete, service provided by listSrvr available to all
    case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
	  break;
    */

	case "getPage":
		$db = new BiblioImages;
		$orderBy = $_POST['orderBy'];
		$rslt = $db->getBiblioMatches($map[$orderBy],$orderBy);
		//$numRows = $rslt->num_rows;
        if ($rslt) {
            $recs = $rslt->fetchAll();
            $numRows = count($recs);
        } else {
            $numRows = 0;
        }

		// add amount of search results.
		$perPage = Settings::get('items_per_page');
		if($_POST['firstItem'] == null){
			$firstItem = 0;
		} else {
			$firstItem = $_POST['firstItem'];
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
		//while($row = $rslt->fetch_assoc()) {
        foreach ($recs as $row) {
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
		  echo '<h4 class="error">'.T("invalid mode")."@imageSrvr.php: &gt;".$_POST['mode']."&lt;</h4><br />";
		break;
	}

