<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../functions/marcFuncs.php"));
	require_once(REL(__FILE__, "../functions/utilFuncs.php"));
	require_once(REL(__FILE__, "../classes/Query.php"));
	require_once(REL(__FILE__, "../model/Settings.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/BiblioImages.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/CopyStatus.php"));
	require_once(REL(__FILE__, "../model/CopiesCustomFields.php"));	
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

	
	## Load session data in case of OPAC (eg no user logged on)
	if(empty($_SESSION['show_checkout_mbr'])) $_SESSION['show_checkout_mbr'] = Settings::get('show_checkout_mbr');	
	if(empty($_SESSION['show_detail_opac'])) $_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');	
	if(empty($_SESSION['multi_site_func'])) $_SESSION['multi_site_func'] = Settings::get('multi_site_func');
	if(empty($_SESSION['show_item_photos'])) $_SESSION['show_item_photos'] = Settings::get('show_item_photos');	
	if(empty($_SESSION['items_per_page'])) $_SESSION['items_per_page'] = Settings::get('items_per_page');
	
	## Adjusted, so that if 'library_name' contains a string, the site is put by default on 1.
	if(empty($_SESSION['current_site'])) {
		if(isset($_COOKIE['OpenBiblioSiteID'])) {
			$_SESSION['current_site'] = $_COOKIE['OpenBiblioSiteID'];				
		} elseif($_SESSION['multi_site_func'] > 0){
			$_SESSION['current_site'] = $_SESSION['multi_site_func']; 			
		} else {
			$_SESSION['current_site'] = 1;
		}		
	}

	## fetch opts here for general use as needed
	$opts['lookupAvail'] = in_array('lookup2',$_SESSION);
	$opts['current_site'] = $_SESSION['current_site'];
	$opts['showBiblioPhotos'] = $_SESSION['show_item_photos'];
	$opts['barcdWidth'] = $_SESSION['item_barcode_width'];
	
	require_once(REL(__FILE__, "../classes/SrchDb.php"));
	
	## --------------------- ##

function mkBiblioArray($dbObj) {
 	$rslt['barCd'] = $dbObj->barCd;
 	$rslt['bibid'] = $dbObj->bibid;
 	$rslt['imageFile'] = $dbObj->imageFile;
 	$rslt['daysDueBack'] = $dbObj->daysDueBack;
 	$rslt['createDt'] = $dbObj->createDt;
 	$rslt['matlCd'] = $dbObj->matlCd;
 	$rslt['collCd'] = $dbObj->collCd;
 	$rslt['opacFlg'] = $dbObj->opacFlg;
 	$rslt['data'] = $dbObj->getBiblioDetail();
 	return $rslt;
}

	#****************************************************************************
	switch ($_REQUEST['mode']) {
	case 'getOpts':
		//setSessionFmSettings(); // only activate for debugging!
		$db = new Settings;
		$opts = $db->getAll();
		echo json_encode($opts);
	  break;
	case 'getCrntMbrInfo':
		require_once(REL(__FILE__, "../functions/info_boxes.php"));
		echo currentMbrBox();
	  break;
 	case 'getMediaDisplayInfo':
		require_once(REL(__FILE__, "../model/MaterialFields.php"));
		$theDb = new MaterialFields;
		$media = $theDb->getDisplayInfo($_GET['howMany']);
		echo json_encode($media);
		break;
	case 'getMediaLineCnt':
		require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$theDb =new MediaTypes;
		$set = $theDb->getAll('code');
		while ($row = $set->fetch_assoc()) {
		  $media[$row['code']] = $row['srch_disp_lines'];
		}
		echo json_encode($media);
		break;

	case 'doBarcdSearch':
	  $theDb = new SrchDB;
	  $rslt = $theDb->getBiblioByBarcd($_REQUEST['searchBarcd']);
	  if ($rslt != NULL) {
	  	$theDb->getBiblioInfo($theDb->bibid);
	  	echo json_encode(mkBiblioArray($theDb));
		} else {
			echo '{"data":null}';
		}
	  break;

	case 'doBibidSearch':
	  $theDb = new SrchDB;
  	$theDb->getBiblioInfo($_REQUEST[bibid]);
	  	echo json_encode(mkBiblioArray($theDb));
	  break;

	case 'doPhraseSearch':
	  $theDb = new SrchDB;
		$params = makeTagObj(getSrchTags($_REQUEST[searchType]));

		# Add search params
		$searchTags = "";
		
		if(isset($_REQUEST['sortBy'])){
				switch ($_REQUEST['sortBy']){
				case 'author': $searchTags .= '{"orderTag":"100","orderSuf":"a"}'; break;
				case 'callno': $searchTags .= '{"orderTag":"099","orderSuf":"a"}'; break;
				case 'title':  $searchTags .= '{"orderTag":"245","orderSuf":"a"},
																			 {"orderTag":"245","orderSuf":"b"},
																			 {"orderTag":"240","orderSuf":"a"}'; break;
				default: $searchTags .= '{"orderTag":"245","orderSuf":"a"},
																 {"orderTag":"245","orderSuf":"b"}'; break;
			}
		}		

		if($_REQUEST['advanceQ']=='Y'){
			if(isset($_REQUEST['srchSites']) && $_REQUEST['srchSites'] != 'all'){
				$searchTags .= ',{"siteTag":"xxx","siteValue":"'. $_REQUEST['srchSites'] . '"}';
			}
			if(isset($_REQUEST['materialCd']) && $_REQUEST['materialCd'] != 'all'){
				//Not sure about the tag, but leave it as is for the moment, as it is a field in bibid (material_cd)
				$searchTags .= ',{"mediaTag":"099","mediaSuf":"a","mediaValue":"'. $_REQUEST['materialCd'] . '"}';
			}
			if(isset($_REQUEST['collectionCd']) && $_REQUEST['collectionCd'] != 'all'){
				//Not sure about the tag, but leave it as is for the moment, as it is a field in bibid (material_cd)
				$searchTags .= ',{"collTag":"099","collSuf":"a","collValue":"'. $_REQUEST['collectionCd'] . '"}';
			}
			if(isset($_REQUEST['audienceLevel'])){
				//Not sure which field this, so leave this for now - LJ
				//$searchTags .= ',{"audienceTag":"099","audienceSuf":"a","audienceValue":"'. $_REQUEST['audienceLevel'] . '"}';
			}
			if(isset($_REQUEST['to']) && strlen($_REQUEST['to']) == 4){
				//$searchTags .= ',{"toTag":"260","toSuf":"c","toValue":"'. $_REQUEST['to'] . '"}';
				$searchTags .= ',{"toTag":"260","toSuf":"c","toTag":"773","toSuf":"d","toValue":"'. $_REQUEST['to'] . '"}';
			}
			if(isset($_REQUEST['from']) && strlen($_REQUEST['from']) == 4){
				//$searchTags .= ',{"fromTag":"260","fromSuf":"c","fromValue":"'. $_REQUEST['from'] . '"}';
				$searchTags .= ',{"fromTag":"260","fromSuf":"c","fromTag":"773","fromSuf":"d","fromValue":"'. $_REQUEST['from'] . '"}';
			}
		}			
		
		/* - - - - - - - - - - - - - */
		/* Actual Search begins here */		
		$paramStr = "[" . $params . "," . $searchTags . "]";
		$biblioLst = $theDb->getBiblioByPhrase($type, $paramStr);
		if (sizeof($biblioLst) > 0) {
			// Add amount of search results.
			if($_REQUEST['firstItem'] == null){
				$firstItem = 0;
			} else {
				$firstItem = $_REQUEST['firstItem'];
			}
			if($_SESSION['items_per_page'] <= sizeof($biblioLst) - $firstItem){
				$lastItem = $firstItem + $_SESSION['items_per_page'];
			} else {
				$lastItem = sizeof($biblioLst);
			}
			
			## multi-page record header
			$rcd['totalNum'] = sizeof($biblioLst);
			$rcd['firstItem'] = $firstItem;
			$rcd['lastItem'] = $lastItem;
			$rcd['itemsPage'] = $_SESSION['items_per_page'];
			$biblio[] = json_encode($rcd);

			# Only show as many as in the settings (not the most efficient way to get the whole result query, this should be rewritten
			$iterCounter = 0;		
			foreach ($biblioLst as $bibid) {
				# Skip if before requested items and break when amount of items is past - LJ
				$iterCounter++;
				if($iterCounter - 1 < $firstItem) continue;
				if($iterCounter > $lastItem) break;
				$theDb->getBiblioInfo($bibid);
	  		$rslt = mkBiblioArray($theDb);
				if($_SESSION['show_detail_opac'] == 'Y')
					$rslt['avIcon'] = $theDb->avIcon;
	  		$biblio[] = json_encode($rslt);
			}
			echo json_encode($biblio);
		} else {
			echo '[]';
		}
		break;
		
	case 'addToCart':
		require_once(REL(__FILE__, "../model/Cart.php"));
		$name = $_REQUEST['name'];
		$cart = getCart($name);
		if (isset($_REQUEST['id'])) {
			foreach ($_REQUEST['id'] as $id) {
				$rslt = $cart->contains($id);
				if (!$rslt) $cart->add($id);
			}
		}
	  break;

	case 'getNewBarcd':
		//require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
		$temp['barcdNmbr'] = $copies->getNewBarCode($_SESSION[item_barcode_width]);
		echo json_encode($temp);
	  break;	  
	  
	case 'chkBarcdForDupe':
	  $copies = new Copies;
	  if ($copies->isDuplicateBarcd($_REQUEST[barcode_nmbr],$_REQUEST[copyid]))
			echo "Barcode $_REQUEST[barcode_nmbr]: ". T("Barcode number already in use.");
		break;
		
	case 'getBiblioFields':
		require_once(REL(__FILE__, "../model/MaterialFields.php"));
		$theDb = new SrchDB;
		$theDb->getBiblioFields();
		break;
		
	case 'getCopyInfo':
	  $theDb = new SrchDB;
	  echo json_encode($theDb->getCopyInfo($_REQUEST[bibid]));
	  break;

	case 'updateBiblio':
	  require_once(REL(__FILE__,"biblioChange.php"));
	  $nav = '';
	  $_POST["material_cd"] = $_POST['materialCd'];
	  $_POST["collection_cd"] = $_POST['collectionCd'];
	  $_POST["opac_flg"] = $_POST["opacFlg"];
  	$msg = PostBiblioChange($nav);
  	if (is_object($msg)) {
  		$rslt = json_decode($msg);
  		$bibid = $rslt->bibid;
		}
	  echo $msg;
	  break;

	case 'deleteBiblio':
	  $bibs = new Biblios;
	  $bibs->deleteOne($_REQUEST['bibid']);
	  echo T("Delete completed");
	  break;
	case 'deleteMultiBiblios':
		$bibs = new Biblios;
		foreach ($_POST['bibList'] as $bibid) {
			$bibs->deleteOne($bibid);
		}
		echo T("Delete completed");
		break;

	//// ====================================////
	case 'updateCopy':
	case 'newCopy':
	  $copies = new Copies;
	  if ($copies->isDuplicateBarcd($_POST[barcode_nmbr], $_POST[copyid])) {
			echo "Barcode $_REQUEST[barcode_nmbr]: ". T("Barcode number already in use.");
			return;
		}
	  $theDb = new SrchDB;
		if ($_POST[mode] == 'updateCopy') {
	  	echo $theDb->updateCopy($_REQUEST[bibid],$_REQUEST[copyid]);
		} else {	
			echo $theDb->insertCopy($_REQUEST[bibid],$_REQUEST[copyid]);
		}
		break;
	case 'getBibsFrmCopies':
	  $theDb = new SrchDB;
		$rslt = $theDb->getBibsForCpys($_GET['cpyList']);
	  echo json_encode($rslt);
	  break;
	case 'deleteCopy':
	  $theDb = new SrchDB;
		echo $theDb->deleteCopy($_REQUEST['bibid'],$_REQUEST['copyid']);
		break;
	case 'deleteMultiCopies':
	  $theDb = new SrchDB;
		foreach ($_POST['cpyList'] as $copyid) {
			echo $theDb->deleteCopy($_REQUEST['bibid'],$_REQUEST['copyid']);
		}
		break;
/*
	case 'deleteMultiCopies':
		$copies = new Copies;
		foreach ($_POST['cpyList'] as $copyid) {
			$copies->deleteOne($copyid);
		}
		echo T("Delete completed");
		break;
*/	  
		
	//// ====================================////
	case 'getPhoto':
	  $ptr = new BiblioImages;
	  $set = $ptr->getByBibid($_REQUEST['bibid']);
		while ($row = $set->fetch_assoc()) {
		  $imgs[] = $row;
		}
		echo json_encode($imgs);
	  break;
	case 'updatePhoto':
	  $ptr = new BiblioImages;
	  ### left as an exercise for the motivated - FL (I'm burned out on this project)
		break;
	case 'addNewPhoto':
		define('UPLOAD_DIR', '../photos/');
		$file = UPLOAD_DIR . $_POST['url'];
		$img = $_POST['img'];
		if (substr($file, -3,3) == 'png')
			$imgFmt = 'png';
		 else
			$imgFmt = 'jpeg';
		$img = str_replace('data:image/'.$imgFmt.';base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$success = file_put_contents($file, $data);
		if ($success) {
	  	$ptr = new BiblioImages;
			$err = $ptr->appendLink_e($_POST['bibid'], $_POST['caption'], $data, $_POST['url']);
			if(isset($err)) {
				print_r($err);
				break;
			}
	  	$set = $ptr->getByBibid($_REQUEST['bibid']);
			while ($row = $set->fetch_assoc()) {
			  $imgs[] = $row;
			}
			echo json_encode($imgs);
		} else {
			echo 'Unable to save the file.';
			print_r($_POST);
		}
		break;
	case 'deletePhoto':
	  $ptr = new BiblioImages;
		$ptr->deleteByBibid($_POST['bibid']);
		break;

	//// ====================================////
	default:
	  echo "<h5>Invalid mode: $_REQUEST[mode]</h5>";
	}
