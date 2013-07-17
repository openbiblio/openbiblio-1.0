<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once("../shared/global_constants.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../classes/Report.php"));

	require_once(REL(__FILE__, "../model/Members.php"));
		$members = new Members;
	require_once(REL(__FILE__, "../model/MemberTypes.php"));
		$mbrTypes = new MemberTypes;
	require_once(REL(__FILE__, "../model/MemberCustomFields.php"));
		$customFlds = new MemberCustomFields;
	require_once(REL(__FILE__, "../model/Sites.php"));
		$sites = new Sites;
	require_once(REL(__FILE__, "../model/MemberAccounts.php"));
		$acct = new MemberAccounts;
	require_once(REL(__FILE__, "../model/TransactionTypes.php"));
		$transtypes = new TransactionTypes;
	require_once(REL(__FILE__, "../model/Biblios.php"));
		$biblios = new Biblios;
	require_once(REL(__FILE__, "../model/Collections.php"));
		$colls = new CircCollections;
	require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
	require_once(REL(__FILE__, "../model/History.php"));
		$history = new History;
	require_once(REL(__FILE__, "../model/Holds.php"));
		$holds = new Holds;
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$mediaTypes = new MediaTypes;
	require_once(REL(__FILE__, "../model/Bookings.php"));
		$bookings = new Bookings;
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
	switch ($_REQUEST['mode']) {
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
	  break;
	case 'getMbrType':
		$type = $mbrTypes->getOne($_GET['classification']);
		echo json_encode($type);
		break;
	case 'getCustomFlds':
		$flds = $customFlds->getSelect();
		echo json_encode($flds);
		break;
	case 'getSites':
		$mbr['site'] = $sites->getOne($mbr['siteid']);
	  break;
	case 'getAcnts':
		$mbr['balance'] = $acct->getBalance($mbrid);
		break;
	case 'getAcntTranTypes':
		$type = $transtypes->getSelect();
		echo json_encode($type);
		break;
/*
	case 'getMediaType':
		break;
	case 'getHistory':
		break;
	case 'getBiblios':
		break;
	case 'getBookings':
		break;
*/
	case 'getSite':
		$site = $sites->getOne($_GET['siteid']);
    echo json_encode($site);
		break;

	//// ====================================////
	case 'doGetMbr':
		$mbrDflt = $members->maybeGetOne($_GET['mbrid']);
		$cstmFlds = $members->getCustomfields($_GET['mbrid']);
		$mbrCstm = array();
		while ($fld = $cstmFlds->fetch_assoc()) {
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
		while ($fld = $cstmFlds->fetch_assoc()) {
			$mbrCstm[$fld['code']] = $fld['data'];
		}
		$mbr = array_merge($mbrDflt, $mbrCstm);
		echo json_encode($mbr);
	  break;
	case 'doNameFragSearch':
		$rows = $members->getMbrByName($_GET['nameFrag']);
		$mbrs = array();
		while ($row = $rows->fetch_assoc()) {
			$mbrs[] = $row;
		}
		echo json_encode($mbrs);
		break;
		
	//// ====================================////
	case 'getAcntActivity':
		$transactions = $acct->getByMbrid($_GET['mbrid']);
		$tranList = array();
		while ($tran = $transactions->fetch_assoc()) {
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
		$types = $mediaTypes->getAll();
		$mediaTypeDm = array();
		$mediaImageFiles = array();
		while ($type = $types->fetch_assoc()) {
			$mediaTypeDm[$type['code']] = $type['description'];
			$mediaImageFiles[$type['code']] = $type['image_file'];
		}

		$checkouts = $copies->getMemberCheckouts($_GET['mbrid']);
		$chkOutList = array();
		while ($copy = $checkouts->fetch_assoc()) {
			$biblio = $biblios->getOne($copy['bibid']);
			$a = $biblios->marcRec->getValue('240$a');
			$b = $biblios->marcRec->getValue('245$a');
			$c = $biblios->marcRec->getValue('245$b');
			$d = $biblios->marcRec->getValue('246$a');
			$e = $biblios->marcRec->getValue('246$b');
			if (!empty($a) || !empty($b) || !empty($c)) $copy['title'] = $a.' '.$b.' '.$c;
			if (!empty($d) || !empty($e)) $copy['title'] = $d.' '.$e;
			$copy['status'] = $history->getOne($copy['histid']);
			$copy['booking'] = $bookings->getByHistid($copy['histid']);
			$copy['booking']['days_late'] = $bookings->getDaysLate($copy['booking']);
			$copy['material_img_url'] = '../images/'.$mediaImageFiles[$biblio['material_cd']];
			$copy['material_type'] = $mediaTypeDm[$biblio['material_cd']];
			$col = $colls->getOne($biblio['collection_cd']);
			$fee = $col['daily_late_fee'];
			$copy['booking']['fee'] = $fee;
			$copy['booking']['owed'] = $copy['booking']['days_late'] * $fee;
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
	case 'getHolds':
		$holds = Report::create('holds');
		$holds->init(array('mbrid'=>$_GET['mbrid']));
		$holdList = array();
		while ($hold = $holds->next()) {
			$holdList[] = $hold;
		}
	  echo json_encode($holdList);
		break;
	case "doHold":
		$copy = $copies->getByBarcode($_POST['barcodeNmbr']);
		$holds->insert(array(
			'bibid'=>$copy['bibid'],
			'copyid'=>$copy['copyid'],
			'mbrid'=>$_POST['mbrid'],
		));
		$status = $history->getOne($copy['histid']);
		if ($status['status_cd'] == OBIB_DEFAULT_STATUS || $status['status_cd'] == OBIB_STATUS_IN || $status['status_cd'] == OBIB_STATUS_SHELVING_CART) {
			$hist = array(
				'bibid'=>$copy['bibid'],
				'copyid'=>$copy['copyid'],
				'status_cd'=>OBIB_STATUS_ON_HOLD,
			);
			$history->insert($hist);
		}
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
		list($mbrid, $errors) = $members->insert_el($mbr);
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
	  echo $members->setCustomFields($mbrid, $cstmArray);
		break;
	case 'd-3-L-3-tMember':
		$members->deleteOne($_POST['mbrid']);
		$members->deleteCustomFields($_POST['mbrid']);
		break;
		
	//// ====================================////
	default:
	  echo "<h5>".T("invalid mode").": $_REQUEST[mode]</h5>";
	}

?>
