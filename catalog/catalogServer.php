<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../functions/utilFuncs.php"));
	require_once(REL(__FILE__, "../model/Settings.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/BiblioImages.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/CopyStatus.php"));
	require_once(REL(__FILE__, "../model/CopiesCustomFields.php"));	
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

	require_once(REL(__FILE__, "../classes/Biblio.php"));
	require_once(REL(__FILE__, "../classes/Copy.php"));

/**
 * back-end API for Existing Biblio Management
 * @author Luuk Jansen
 * @author Fred LaPlante
 **/
	
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
	
	## --------------------- ##

	#****************************************************************************
	switch ($_REQUEST['mode']) {
	case 'doBibidSearch':
	  $bib = new Biblio($_REQUEST[bibid]);
	  echo json_encode($bib->getData());
	  break;

	case 'doBarcdSearch':
	  $ptr = new Copies;
	  $copy = $ptr->getByBarcode($_REQUEST['searchBarcd']);
	  if ($copy != NULL) {
	  	$bib = new Biblio($copy['bibid']);
	  	echo json_encode($bib->getData());
	  } else {
        echo '{"data":null}';
	  }
	  break;

	case 'doPhraseSearch':
		## fetch a list of all biblio meeting user search criteria
		$criteria = $_REQUEST;
	    $theDb = new Biblios;
		$biblioLst = $theDb->getBiblioByPhrase($criteria);
        //echo "in catalogSrvr, doPhraseSearch: "; print_r($biblioLst);echo "<br />\n";
		if (sizeof($biblioLst) > 0) {
			$srchRslt = array();
			## succesful search, deal with results
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
			$srchRslt[] = json_encode($rcd);

			## show as many as user settings specify
			## not the most efficient way to get the whole result query, this should be rewritten
			$iterCounter = 0;		
			foreach ($biblioLst as $bibid) {
				## Skip if before requested items and break when amount of items is past - LJ
				$iterCounter++;
				if($iterCounter - 1 < $firstItem) continue;
				if($iterCounter > $lastItem) break;
				## create a Biblio object for each item in range and add content to $srchRslt
		  	$bib = new Biblio($bibid);
		  	$srchRslt[] = json_encode($bib->getData());
				unset($bib); ## object no longer needed, destroy it
			}
			echo json_encode($srchRslt);
		} else {
			echo '[]';
		}
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
		//while ($row = $set->fetch_assoc()) {
        foreach ($set as $row) {
		  $media[$row['code']] = $row['srch_disp_lines'];
		}
		echo json_encode($media);
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
		$theDb = new Biblios;
		$theDb->getBiblioFields();
		break;
		
	case 'getCopyInfo':
	    $bib = new Biblio($_GET['bibid']);
		$bibData = $bib->getData();
		$cpyList = $bibData['cpys'];
		foreach ($cpyList as $cid) {
			$cpy = new Copy($cid);
			$cpys[] = $cpy->getData();
			unset($cpy); # no longer needed
		}
		echo json_encode($cpys);
	  break;

	case 'updateBiblio':
		## fetch biblio object with current DB data
	    $bib = new Biblio($_POST['bibid']);
		## overwrite header with screen content
		$hdr['bibid'] = $_POST['bibid'];
		$hdr['material_cd'] = $_POST['materialCd'];
		$hdr['collection_cd'] = $_POST['collectionCd'];
		$hdr['opac_flg'] = $_POST['opacFlg'];
		$msg = $bib->setHdr($hdr);
		if(isset($msg)) die ($msg);
		## overwrite marc fields with screen content (new or modified))
		foreach ($_POST['fields'] as $key=>$val) {
			$marc[$key] = array('data'=>$val['data'],'codes'=>$val['codes']);
		}
		$msg = $bib->setMarc($marc);
		if(isset($msg)) die ($msg);
		## tell biblio object to post itself to DB
		$msg = $bib->updateDB();
	    echo $msg;
	    break;

	case 'deleteBiblio':
        $bibs = new Biblio($_REQUEST['bibid']);
        $bibs->deleteBiblio();
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
	case 'newCopy':
        $copies = new Copies;
        if ($copies->isDuplicateBarcd($_POST[barcode_nmbr], $_POST[copyid])) {
        	echo "Barcode $_REQUEST[barcode_nmbr]: ". T("Barcode number already in use.");
        	return;
        }
        $theDb = new Copies;
        echo $theDb->insertCopy($_REQUEST['bibid'],$_REQUEST['copyid']);
        break;
	case 'updateCopy':
	    $theDb = new Copies;
	    //echo $theDb->updateCopy($_REQUEST['bibid'],$_REQUEST['copyid']);
		break;
	case 'getBibsFrmCopies':
	  $theDb = new Copies;
		$rslt = $theDb->getBibsForCpys($_GET['cpyList']);
	  echo json_encode($rslt);
	  break;
	case 'deleteCopy':
	    $theDb = new Copies;
		//echo $theDb->deleteCopy($_REQUEST['bibid'],$_REQUEST['copyid']);
		echo $theDb->deleteCopy($_REQUEST['copyid']);
		break;
	case 'deleteMultiCopies':
	    $theDb = new Copies;
		foreach ($_POST['cpyList'] as $copyid) {
			echo $theDb->deleteCopy($copyid);
		}
		break;
		
	//// ====================================////
	case 'getPhoto':
	    $ptr = new BiblioImages;
	    $set = $ptr->getByBibid($_REQUEST['bibid']);
		//while ($row = $set->fetch_assoc()) {
        foreach ($set as $row) {
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
