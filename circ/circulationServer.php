<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

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
	switch ($_REQUEST['mode']) {
	case 'getOpts':
		$opts = Settings::getAll();
		echo json_encode($opts);
	  break;
	  
	case "doShelfItem":
		$copy = $copies->getByBarcode($_POST['barcodeNmbr']);
		if (!$copy) {
			echo T("No copy with that barcode");
			exit;
		}
		$status = $history->getOne($copy['histid']);
		## FIXME? book may not have been checked out, wrong valid barcode, etc.
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
			$msg .= T(" THIS ITEM WAS NOT CHECKED OUT.");
		}
		echo $msg;
		break;
		
	case 'doItemCheckin':
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
			$msg = T("No items have been selected.");
			header("Location: ../circ/checkin_form.php?msg=".U($msg));
			exit();
		}
		$copies->checkin($bibids, $copyids);
		break;
		
	case 'doMassCheckin':
		$copies->massCheckin();
		break;
		
	//// ====================================////
	default:
	  echo "<h5>Invalid mode: $_REQUEST[mode]</h5>";
	}

?>
