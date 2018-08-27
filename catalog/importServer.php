<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/MarcBlocks.php"));
	require_once(REL(__FILE__, "../model/MarcTags.php"));
	require_once(REL(__FILE__, "../model/MarcSubfields.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/Cart.php"));
	require_once(REL(__FILE__, "../classes/Marc.php"));

	# Big uploads take a while
//	set_time_limit(120);

//	$recordTerminator="\n";

	## ---------------------------------------------------------------------- ##

function doPostNewBiblio($rcrd) {
	$_POST = $rcrd;
  	require_once(REL(__FILE__,'../catalog/biblioChange.php'));
  	$rtn = PostBiblioChange("newconfirm");
  	$rslt = json_decode($rtn);
  	return $rslt->bibid;
}

## main body of code
//echo "params: ";print_r($_POST);echo "<br />\n";
switch ($_POST['mode']){
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
//echo "in importServer, process MarcFile: <br />";
		$fn = $_FILES['imptSrce']['tmp_name'];
		if (is_uploaded_file($fn)) {
			//$f = @fopen($fn, rb);
			$f = fopen($fn, rb);
			assert($f);

			$biblios = new Biblios();
			$p = new MarcParser();
			$cart = new Cart('bibid');
			$nfnd = 0;
			$nimp = 0;

			$opac_flg = (isset($_POST['opac']) AND $_POST['opac'] == 'Y') ? 'Y' : 'N';

			if ($_POST["test"]=="true") {
				echo '<p class="info">Test mode was selected,<br />input file was error checked,  nothing has been imported.</p><br />';
			}

			while($buf = fread($f, 8192)) {
				$err = $p->parse($buf);
				if (is_a($err, 'MarcParseError')) {
					echo '<p class="error"><br />'.T("Bad MARC record, giving up").'<br />';
					print_r($err);
					echo "<br /> See file ".$fn."</p>";
					break;
				}
//echo "good record found<br />";
				foreach ($p->records as $rec) {
					$nfnd += 1;
					if ($_POST["test"]=="true") {
						if ($_POST["verbose"]=="true") {
							echo '<p>RECORD To be Imported:<pre>';
							echo $rec->getMnem();
//							print_r($rec);
							echo '</pre></p><br />';
						} else {
							continue;
						}
					} else {
						$biblio = array(
							'last_change_userid' => $_SESSION["userid"],
							'material_cd' => $_POST["materialCd"],
							'collection_cd' => $_POST["collectionCd"],
							'opac_flg' => $opac_flg,
							'marc' => $rec,
						);
echo "processing record #$nimp<br />";
						$bibid = $biblios->insert($biblio);
echo "bibid ".print_r($bibid);echo " added to Biblios<br />";
						/* Posting to the cart currently gives all items in the batch the same session ID
						   so the process fails with a duplicate primary key. I'm not sure how this should work,
						   so have commented it out for now - FL July 2018
						*/
						//$cart->add($bibid);
						//echo "bibid $bibid added to Cart<br />";
						$nimp += 1;
					}
				}
				$p->records = array();
			}
			fclose($f);
			echo '<p>'.$nfnd.' '.T("recordsFound").'</p>';
			echo '<p>'.$nimp.' '.T("recordsImported").'</p>';
			if ($_POST["test"] != "true") {
				$text = '<a href="../shared/req_cart.php?tab='.HURL($tab).'">'.'</a>';
				//echo '<p>'.T("Records added to %url%Cart", array('url'=>$text)).'</p>';
				echo '<p>'.T("Records added to Cart")." ".$text.'</p>';
			}
		} else {
			echo	T("error - file did not load successfully!!")."<br />";
			print_r($_FILES);echo "<br />\n";
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
			$barCd = $cpys->getNewBarCode($_SESSION['item_barcode_width']);
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
	    echo T("invalid mode").": $_POST['mode'] <br />";
		break;
}


