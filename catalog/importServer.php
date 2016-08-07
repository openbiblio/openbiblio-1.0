<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/MarcBlockss.php"));
	require_once(REL(__FILE__, "../model/MarcTags.php"));
	require_once(REL(__FILE__, "../model/MarcMarcSubfields.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/Cart.php"));
	require_once(REL(__FILE__, "../classes/Marc.php"));

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
switch ($_POST[mode]){
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
	case 'processMarcFile':
		$fn = $_FILES['imptSrce']['tmp_name'];
		if (is_uploaded_file($fn)) {
			$f = @fopen($fn, rb);
			assert($f);
			$p = new MarcParser();
			$biblios = new Biblios();
			$cart = new Cart('bibid');
			$nrecs = 0;

			$opac_flg = (isset($_POST['opac']) && $_POST['opac'] == 'Y') ? 'Y' : 'N';

			while($buf = fread($f, 8192)) {
				$err = $p->parse($buf);
				if (is_a($err, 'MarcParseError')) {
					echo '<p class="error">'.T("Bad MARC record, giving up: %err%", array('err'=>$err->toStr())).'</p>';
					break;
				}
				foreach ($p->records as $rec) {
					if ($_POST["test"]=="true") {
						echo '<p><pre>';
						echo $rec->getMnem();
						echo '</pre></p>';
						continue;
					}
					$biblio = array(
						'last_change_userid' => $_SESSION["userid"],
						'material_cd' => $_POST["materialCd"],
						'collection_cd' => $_POST["collectionCd"],
						'opac_flg' => $opac_flg,
						'marc' => $rec,
					);
					$bibid = $biblios->insert($biblio);
					$cart->add($bibid);
					$nrecs += 1;
				}
				$p->records = array();
			}
			fclose($f);
			echo '<p>'.$nrecs.' '.T("recordsImported").'</p>';
			if ($_POST["test"] != "true") {
				$text = '<a href="../shared/req_cart.php?tab='.HURL($tab).'">'.'</a>';
				echo '<p>'.T("Records added to %url%Cart", array('url'=>$text)).'</p>';
			}
		} else {
			echo	T("error - file did not load successfully!!")."<br />";
		}
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
