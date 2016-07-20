<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once("../classes/ObServer.php");

	// Check that anything revealing personal data passes an HMAC check
	$sensitive_modes = Array( 'doNameFragSearch');
	if (False !== array_search($_POST['mode'], $sensitive_modes)) {
		if (!ObServer::check_hmac()) {
			$err = new ObErr('Authentication failed');
			echo $err->toStr();
			exit;
		}
	}
	switch ($_POST['mode']) {
		case 'getMbrType':
			require_once(REL(__FILE__, "../model/MemberTypes.php"));
			$mbrTypes = new MemberTypes;
			break;
		case 'getCustomFlds':
			require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
			require_once(REL(__FILE__, "../model/MemberCustomFields_DM.php"));
			$customFlds = new MemberCustomFields_DM;
			break;
		case 'getAcnts':
		case 'addAcntTrans':
		case 'd-3-L-3-tAcntTrans':
			require_once(REL(__FILE__, "../model/MemberAccounts.php"));
			$acct = new MemberAccounts;
			break;
		case 'getAcntTranTypes':
			require_once(REL(__FILE__, "../model/TransactionTypes.php"));
			$transtypes = new TransactionTypes;
			break;
		case 'doGetMbr':
		case 'getNewBarCd':
		case 'doBarcdSearch':
		case 'doNameFragSearch':
		case 'addNewMember':
		case 'updateMember':
		case 'd-3-L-3-tMember':
			require_once(REL(__FILE__, "../model/Members.php"));
			$members = new Members;
			require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
			require_once(REL(__FILE__, "../model/MemberCustomFields_DM.php"));
			$customFlds = new MemberCustomFields_DM;
			break;
		case 'getAcntActivity':
			require_once(REL(__FILE__, "../model/MemberAccounts.php"));
			$acct = new MemberAccounts;
			require_once(REL(__FILE__, "../model/TransactionTypes.php"));
			$transtypes = new TransactionTypes;
			break;
		case 'getChkOuts':
			ini_set('display_errors', '1');
			require_once(REL(__FILE__, "../model/Copies.php"));
			$copies = new Copies;
		    require_once(REL(__FILE__, "../classes/Copy.php"));
			require_once(REL(__FILE__, "../model/Biblios.php"));
			$biblios = new Biblios;
			break;
		case 'doCheckout':
			require_once(REL(__FILE__, "../model/Bookings.php"));
			$bookings = new Bookings;
			break;
		case 'getHist':
			require_once(REL(__FILE__, "../model/History.php"));
			$history = new History;
			require_once(REL(__FILE__, "../classes/Biblio.php"));
			break;
		case 'doHold':
		case 'getHolds':
		case 'd-3-L-3-tHold':
			require_once(REL(__FILE__, "../model/Holds.php"));
			$holds = new Holds;
			require_once(REL(__FILE__, "../model/Copies.php"));
			$copies = new Copies;
		    require_once(REL(__FILE__, "../classes/Copy.php"));
			require_once(REL(__FILE__, "../classes/Biblio.php"));
			break;
	}


	require_once(REL(__FILE__, "../classes/Report.php"));

	require_once(REL(__FILE__, "../model/Collections.php"));
	$colls = new CircCollections;
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	$mediaTypes = new MediaTypes;
	require_once(REL(__FILE__, "../model/Sites.php"));
	$sites = new Sites;


	#****************************************************************************
	function mbrArray() {
		$mbr = array(
			'mbrid'=>$_POST["mbrid"],
			'siteid'=>$_POST["siteid"],
			'barcode_nmbr'=>$_POST["barcode_nmbr"],
			'last_name'=>$_POST["last_name"],
			'first_name'=>$_POST["first_name"],
			'address1'=>$_POST["address1"],
			'address2'=>$_POST["address2"],
			'city'=>$_POST["city"],
			'state'=>$_POST["state"],
			'zip'=>$_POST["zip"],
			'zip_ext'=>$_POST["zip_ext"],
			'home_phone'=>$_POST["home_phone"],
			'work_phone'=>$_POST["work_phone"],
			'email'=>$_POST["email"],
			'password'=>$_POST["password"],
			'confirm-pw'=>$_POST["confirm-pw"],
			'classification'=>$_POST["classification"],
	  );
	  return $mbr;
	}

	#****************************************************************************
	switch ($_POST['mode']) {
		case 'getOpts':
			$opts = Settings::getAll();
			echo json_encode($opts);
	  		break;
		case 'getMbrType':
			$type = $mbrTypes->getOne($_GET['classification']);
			echo json_encode($type);
			break;
		case 'getCustomFlds':
			$rslt = $customFlds->getAll();
			foreach ($rslt as $row){
				$flds[] = $row;
			}
			echo json_encode($flds);
			break;
		case 'getSite':
			$site = $sites->getOne($_GET['siteid']);
    			echo json_encode($site);
			break;
		case 'getAcnts':
			$mbr['balance'] = $acct->getBalance($mbrid);
			break;
		case 'getAcntTranTypes':
			$type = $transtypes->getSelect();
			echo json_encode($type);
			break;

		case 'doGetMbr':
			$mbrDflt = $members->maybeGetOne($_GET['mbrid']);
			$cstmFlds = $members->getCustomfields($_GET['mbrid']);
			$mbrCstm = array();
			foreach ($cstmFlds as $fld) {
				$mbrCstm[$fld['code']] = $fld['data'];
			}
			$mbr = array_merge($mbrDflt, $mbrCstm);
			echo json_encode($mbr);
	  		break;
		case 'getNewBarCd':
			$barCd = $members->getNewBarCode($_GET['width']);
			echo $barCd;
			break;
		case 'doBarcdSearch':
			$mbrDflt = $members->getMbrByBarcode($_GET['barcdNmbr']);
			$cstmFlds = $members->getCustomfields($mbrDflt['mbrid']);
			$mbrCstm = array();
			foreach ($cstmFlds as $fld) {
				$mbrCstm[$fld['code']] = $fld['data'];
			}
			$mbr = array_merge($mbrDflt, $mbrCstm);
			echo json_encode($mbr);
	  		break;
		case 'doNameFragSearch':
			$mbrs = array();
			$rows_name = $members->getMbrByName($_POST['nameFrag']);
			foreach ($rows_name as $row) {
				$mbrs[] = $row;
			}
			$rows_legal_name = $members->getMbrByLegalName($_POST['nameFrag']);
			foreach ($rows_legal_name as $row) {
				$mbrs[] = $row;
			}
			echo json_encode($mbrs);
			break;
		
		case 'getAcntActivity':
			$transactions = $acct->getByMbrid($_POST['mbrid']);
			$tranList = array();
			foreach ($transactions as $tran) {
				$tranList[] = $tran;
			}
			echo json_encode($tranList);
			break;
		case 'addAcntTrans':
			list($id, $errs) = $acct->insert_el(array(
				'mbrid'=>$_POST['mbrid'],
				'transaction_type_cd'=>$_POST["transaction_type_cd"],
				'amount'=>trim($_POST["amount"]),
				'description'=>trim($_POST["description"]),
			));
			echo json_encode($errs);
			break;
		case 'd-3-L-3-tAcntTrans':
			$acct->deleteOne($_POST['transid']);
			break;

	//// ====================================////
		case 'getChkOuts':
			$chkOutList = array();
			$cpys = $copies->getMemberCheckouts($_POST['mbrid']);
			foreach ($cpys as $row) {
				$ptr = new Copy($row['copyid']);
				$copy = $ptr->getData();

				$biblio = new Biblio($copy['bibid']);
				$bibData = $biblio->getData();
				$bibMarc = $bibData['marc'];
			/*
			$a = $bibMarc['240$a'];
			$b = $bibMarc['245$a'];
			$c = $bibMarc['245$b'];
			$d = $bibMarc['246$a'];
			$e = $bibMarc['246$b'];
			if (!empty($a) || !empty($b) || !empty($c)) $copy['title'] = $a.' '.$b.' '.$c;
			if (!empty($d) || !empty($e)) $copy['title'] = $d.' '.$e;
			*/
      				$copy['title'] = $bibData['hdr']['title'];
				$chkOutList[] = $copy;
			}
	  		echo json_encode($chkOutList);
			break;
		case 'doCheckout':
			$_POST["barcodeNmbr"] = str_pad($_POST["barcodeNmbr"],$_SESSION['item_barcode_width'],'0',STR_PAD_LEFT);
			$err = $bookings->quickCheckout_e($_POST["barcodeNmbr"], $_POST['calCd'], array($_POST["mbrid"]));
			if ($err) {
				if(is_array($err)){
					$errors = ""; $nErr = 0;
					foreach($err as $error)	{
						if ($nErr > 0) $errors .= '<br />';
						$errors .= $error->toStr();
						$nErr++;
					}
				} elseif (is_object($err)) {
					$errors = $err->toStr();
				} else {
					$errors = $err;
				}
			}
			echo $errors;
			break;

	//// ====================================////
	case 'getHist':
		$sql = "SELECT h.* "
				 . "FROM booking_member m, booking b, biblio_status_hist h "
				 . "WHERE (m.mbrid = ".$_GET['mbrid'].") "
				 . "  AND (b.bookingid = m.bookingid) "
				 . "  AND ((h.histid = b.out_histid) OR (h.histid = b.ret_histid)) "
				 . " ORDER BY h.bibid, b.out_dt ASC";
		$rslt = $history->select($sql);
//		if ($rslt->num_rows == 0) die(T("Nothing Found"));
		$histRcds = array();
		foreach ($rslt as $row) {
//echo"row==>";print_r($row);echo"<br/>\n";
			$biblio = new Biblio($row['bibid']);
			$bibData = $biblio->getData();
			$row['title'] = $bibData['hdr']['title'];
			$histRcds[] = $row;
//echo"row==>";print_r($row);echo"<br/>\n";
		}
		echo json_encode($histRcds);
		break;

	//// ====================================////
	case 'getHolds':
		$rslt = $holds->getByMember($_GET['mbrid']);
		$holdList = array();
		foreach ($rslt as $row) {
			$rcd['hold_dt'] = $row['hold_begin_dt'];
			$rcd['holdid'] = $row['holdid'];
			$copy = new Copy($row['copyid']);
			$cpyData = $copy->getData();
			$rcd['barcode'] = $cpyData['barcode'];
			$rcd['copyid'] = $cpyData['copyid'];
			$rcd['bibid'] = $cpyData['bibid'];
			$rcd['status'] = $cpyData['status'];
			$rcd['due_dt'] = $cpyData['due_dt'];
			$biblio = new Biblio($cpyData['bibid']);
			$bibData = $biblio->getData();
			$rcd['title'] = $bibData['hdr']['title'];
			$holdList[] = $rcd;
		}
		echo json_encode($holdList);
		break;
	case "doHold":
		$copy = $copies->getByBarcode($_POST['barcodeNmbr']);
		if (is_null($copy)) die(T("Barcode does not exist").'.');
		$holds->insert(array(
			'bibid'=>$copy['bibid'],
			'copyid'=>$copy['copyid'],
			'mbrid'=>$_POST['mbrid'],
		));
		/*
		* it is not clear that there is a good reason to track hold in status_hist.
		* If it is, it can mask the in/out condition. Also if the hold is removed,
		* what status should the biblio return to? - FL
		$status = $history->getOne($copy['histid']);
		if ($status['status_cd'] == OBIB_DEFAULT_STATUS || $status['status_cd'] == OBIB_STATUS_IN || $status['status_cd'] == OBIB_STATUS_SHELVING_CART) {
			$hist = array(
				'bibid'=>$copy['bibid'],
				'copyid'=>$copy['copyid'],
				'status_cd'=>OBIB_STATUS_ON_HOLD,
			);
			$history->insert($hist);
		}
		*/
		echo T("Success");
		break;
	case 'd-3-L-3-tHold':
		$holds->deleteOne($_POST['holdid']);
		break;
		
	//// ====================================////
	case 'updateMember':
		$mbr = mbrArray();
	  $errors = $members->update_el($mbr);
		if (!empty($errors)) {
			echo json_encode($errors);
			exit;
		}
		$cstmArray = array();
		foreach ($_POST as $key => $value) {
			if (substr($key,0,7) == 'custom_') {
				$theKey = substr($key,7);
				$cstmArray[$theKey] = $value;	 
			}
		}
	  echo $members->setCustomFields($_POST['mbrid'], $cstmArray);
		break;
	case 'addNewMember':
		$_POST["barcode_nmbr"] = $members->getNextMbr();
		$mbr = mbrArray();
		$response = $members->insert_el($mbr);
		if ($response instanceof OBErr) {
			echo $response->toStr();
			exit;
		}
		$cstmArray = array();
		foreach ($_POST as $key => $value) {
			if (substr($key,0,7) == 'custom_') {
				$theKey = substr($key,7);
				$cstmArray[$theKey] = $value;	 
			}
		}
	  echo $members->setCustomFields($mbrid, $cstmArray);
		break;
	case 'd-3-L-3-tMember':
		$members->deleteOne($_POST['mbrid']);
		$members->deleteCustomFields($_POST['mbrid']);
		break;
		
	//// ====================================////
	default:
	  echo "<h5>".T("invalid mode").": $_POST[mode]</h5>";
	}

