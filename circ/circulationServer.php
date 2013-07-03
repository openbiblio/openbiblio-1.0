<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	require_once(REL(__FILE__, "../model/Biblios.php"));
		$biblios = new Biblios;
	require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
	require_once(REL(__FILE__, "../model/History.php"));
		$history = new History;
	require_once(REL(__FILE__, "../model/Collections.php"));
		$collections = new Collections;
	require_once(REL(__FILE__, "../model/Holds.php"));
		$holds = new Holds;
	require_once(REL(__FILE__, "../model/Bookings.php"));
		$bookings = new Bookings;
	require_once(REL(__FILE__, "../model/MemberAccounts.php"));
		$acct = new MemberAccounts;

	#****************************************************************************
	$badBarcodeText = T("No copy with that barcode");
	
	switch ($_REQUEST['mode']) {
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
	  break;
	 
	case "getBarcdTitle":
		$copy = $copies->getByBarcode($_GET['barcodeNmbr']);
		if (!$copy) {
			echo $badBarcodeText;
			exit;
		}
		$biblio = $biblios->getOne($copy['bibid']);
		echo json_encode(array('title'=>$biblio['marc']->getValue('245$a')));
		break;
		
	case "doItemCheckin":
		$copy = $copies->getByBarcode($_POST['barcodeNmbr']);
		if (!$copy) {
			echo $badBarcodeText;
			exit;
		}
		$status = $history->getOne($copy['histid']);
		## FIXME? item may not have been checked out, wrong valid barcode, etc.
		# 		
		$booking = $bookings->getByHistid($copy['histid']);	# May be null
		
		$hold = $holds->getFirstHold($copy['copyid']);	// is copy on hold?
		if ($hold) {
			$newStatus = OBIB_STATUS_ON_HOLD;
		} else {
			$newStatus = OBIB_STATUS_SHELVING_CART;
		}
		
		$hist = array(
			'bibid'=>$copy['bibid'],
			'copyid'=>$copy['copyid'],
			'status_cd'=>$newStatus,
		);
		if ($booking) {
			$hist['bookingid'] = $booking['bookingid'];
		}
		$history->insert($hist);

		if ($booking) {
			$daysLate = $bookings->getDaysLate($booking);
			$coll = $collections->getByBibid($booking['bibid']);
			$dailyLateFee = $coll['daily_late_fee'];
			if (($daysLate > 0) and ($dailyLateFee > 0)) {
				$fee = $dailyLateFee * $daysLate;
				$acct->insert(array(
					'mbrid'=>$saveMbrid,
					'transaction_type_cd'=>'+c',
					'amount'=>$fee,
					'description'=>T("Late fee (barcode=%barcode%)", array("barcode" => $barcode))
				));
			}
		}
		
		$msg = T("%barcode% added to shelving cart.", array('barcode'=>$barcode));
		if (!$booking) {
			$msg .= T("THIS ITEM WAS NOT CHECKED OUT.");
		}
		echo $msg;
		break;
	
	case 'fetchShelvingCart':
		$scart = $copies->getShelvingCart();
		$rec = array();
		while ($copy = $scart->fetch_assoc()) {
			$biblio = $biblios->getOne($copy['bibid']);
			$status = $history->getOne($copy['histid']);
			$rec[] = array(
				'bibid'=>$copy['bibid'],
				'copyid'=>$copy['copyid'],
				'barcd'=>$copy['barcode_nmbr'],
				'beginDt'=>$status['status_begin_dt'],
				'title'=>$biblio['marc']->getValue('245$a'),
			);
		}
		echo json_encode($rec);
		break;
		
	case 'doShelveItem':
		$bibids = array();
		$copyids = array();
		foreach($_POST as $key => $value) {
			if ($value == "copyid") {
				parse_str($key,$output);
				$bibids[] = $output["bibid"];
				$copyids[] = $output["copyid"];
			}
		}
		if (empty($bibids)) {
			echo "<h3>".T("No items have been selected.")."</h3>";
			//header("Location: ../circ/checkin_form.php?msg=".U($msg));
			exit();
		}
		$copies->checkin($bibids, $copyids);
		break;
		
	case 'doShelveAll':
		$copies->massCheckin();
		break;
		
	//// ====================================////
	default:
	  echo "<h5>".T("invalid mode").": $_REQUEST[mode]</h5>";
	}

?>
