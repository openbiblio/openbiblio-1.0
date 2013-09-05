<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/MarcDBs.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));

	# Big uploads take a while
	set_time_limit(120);

	$recordTerminator="\n";

	## ---------------------------------------------------------------------- ##

function doPostNewBiblio($rcrd) {
	$_POST = $rcrd;
  require_once(REL(__FILE__,'../catalog/biblioChange.php'));
  $rtn = PostBiblioChange("newconfirm");
  $rslt = json_decode($rtn);
  return $rslt->bibid;
}

## main body of code
switch ($_REQUEST[mode]){
  #-.-.-.-.-.-.-.-.-.-.-.-.-
  case 'isDupBarCd':
  	$cpys = new Copies;
  	$rslt = $cpys->isDuplicateBarcd($_GET['barCd']);
  	echo $rslt;
  	break;
  	
  #-.-.-.-.-.-.-.-.-.-.-.-.-
  case 'getCollections':
		$cols = new Collections;
		$collections = $cols->getSelect();
		echo json_encode($collections);
  	break;
  case 'getMediaTypes':
		$meds = new MediaTypes;
		$medTypes = $meds->getSelect();
		echo json_encode($medTypes);
  	break;
  case 'getMarcDesc':
	  $tag = explode('$', $_GET['code']);
		$ptr = new MarcSubfields;
		$params = array('tag' =>$tag[0], 'subfield_cd' =>$tag[1] );
	  $vals = array();
		$rslt = $ptr->getMatches($params, 'subfield_cd');
		while ($row = $rslt->fetch_assoc()) {
		  $vals[] = $row;
		}
		$val = $vals[0]['description'];
	  echo $val;
  	break;
  	
  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'fetchCsvFile': 
		$fn = $_FILES['imptSrce']['tmp_name'];
		if (is_uploaded_file($fn)) {
			$rows =  explode($recordTerminator, file_get_contents($fn));
			//check the last record if there is content after the delimiter
			$row = array_pop($rows);
			if (trim($row) != "") {
			  array_push($rows, $row);
			}
			echo json_encode($rows);
		} else {
			echo	T("error - file did not load successfully!!")."<br />";
		}
  	break;
  
	case 'postCsvData':
	  $theDb = new Copies;
		$cpys = new Copies;

		$rec = $_POST['record'];
		$barCd = $rec['barcode_nmbr'];
		$cpyAction = $rec['copy_action'];
		if ($barCd == 'autogen') {
			$barCd = $cpys->getNewBarCode($_SESSION[item_barcode_width]);
			$rec['barcode_nmbr'] = $barCd;
		}

		$bibid = doPostNewBiblio($rec);
		$text = "Biblio #$bibid posted successfully.";
		if (($cpyAction >= 1) && (isset($rec['barcode_nmbr']))) {
			if ($theDb->insertCopy($bibid,NULL) == '!!success!!') {
				$text .= "<br />\n".T("Copy successfully created.")." ".T("Barcode")." = ".$barCd.". ";
			} else {
				$text .= "<br />\n".T("Error creating New Copy")." ".T("Barcode")." = ".$barCd.". ";
			}
		} else {
		}
		echo $text;
		break;
				
  #-.-.-.-.-.-.-.-.-.-.-.-.-
	default:
	  echo T("invalid mode").": $_POST[mode] <br />";
		break;
}

?>
