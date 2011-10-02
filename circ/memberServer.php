<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
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
	require_once(REL(__FILE__, "../model/Biblios.php"));
		$biblios = new Biblios;
	require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
	require_once(REL(__FILE__, "../model/History.php"));
		$history = new History;
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
		$mediaTypes = new MediaTypes;
	require_once(REL(__FILE__, "../model/Bookings.php"));
		$bookings = new Bookings;

	#****************************************************************************
	switch ($_REQUEST[mode]) {
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
	  break;
	case 'getMbrTypes':
		$mbr['type'] = $mbrTypes->getOne($mbr['classification']);
		# FIXME - this should me the type's code, not description
		if ($mbr['type']['description'] == 'denied') {
			$mbr['checkouts_allowed'] = false;
		} else {
			$mbr['checkouts_allowed'] = true;
		}
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
	case 'getMediaType':
		break;
	case 'getHistory':
		break;
	case 'getBiblios':
		break;
	case 'getBookings':
		break;
		
	//// ====================================////
	case 'doGetMbr':
		$mbrDflt = $members->maybeGetOne($_GET['mbrid']);
		$cstmFlds = $members->getCustomfields($_GET['mbrid']);
		$mbrCstm = array();
		while ($fld = $cstmFlds->next()) {
			$mbrCstm[$fld['code']] = $fld['data'];
		}
		$mbr = array_merge($mbrDflt, $mbrCstm);
		echo json_encode($mbr);
	  break;
	case 'doBarcdSearch':
		$mbrDflt = $members->getMbrByBarcode($_GET['barcdNmbr']);
		$cstmFlds = $members->getCustomfields($mbrDflt['mbrid']);
		$mbrCstm = array();
		while ($fld = $cstmFlds->next()) {
			$mbrCstm[$fld['code']] = $fld['data'];
		}
		$mbr = array_merge($mbrDflt, $mbrCstm);
		echo json_encode($mbr);
	  break;

	//// ====================================////
	case 'getChkOuts':
		$types = $mediaTypes->getAll();
		$mediaTypeDm = array();
		$mediaImageFiles = array();
		while ($type = $types->next()) {
			$mediaTypeDm[$type['code']] = $type['description'];
			$mediaImageFiles[$type['code']] = $type['image_file'];
		}

		$checkouts = $copies->getMemberCheckouts($_GET['mbrid']);
		$chkOutList = array();
		while ($copy = $checkouts->next()) {
			$biblio = $biblios->getOne($copy['bibid']);
			$copy['title'] = $biblio['marc']->getValue('245$a');
			$copy['status'] = $history->getOne($copy['histid']);
			$copy['booking'] = $bookings->getByHistid($copy['histid']);
			$copy['booking']['days_late'] = $bookings->getDaysLate($copy['booking']);
			$copy['material_img_url'] = '../images/'.$mediaImageFiles[$biblio['material_cd']];
			$copy['material_type'] = $mediaTypeDm[$biblio['material_cd']];
			$chkOutList[] = $copy;
		}
	  echo json_encode($chkOutList);
		break;
	case 'getHolds':
		$holds = Report::create('holds');
		$holds->init(array('mbrid'=>$_GET['mbrid']));
		$holdList = array();
		while ($hold = $holds->next()) {
			$holdList[] = $hold;
		}
	  echo json_encode($holdList);
		break;
	case 'updateMember':
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
	  $errors = $members->update_el($mbr);
		if (!empty($errors)) return $errors;
		
		$cstmArray = array();
		foreach ($_POST as $key => $value) {
			if (substr($key,0,7) == 'custom_') {
				$theKey = substr($key,7);
				$cstmArray[$theKey] = $value;	 
			}
		}
	  echo $members->setCustomFields($_POST['mbrid'], $cstmArray);
		break;
	case 'd-3-L-3-tMember':
		$members->deleteOne($_POST['mbrid']);
		$members->deleteCustomFields($_POST['mbrid']);
		break;
		
	//// ====================================////
	default:
	  echo "<h5>Invalid mode: $_REQUEST[mode]</h5>";
	}

?>
