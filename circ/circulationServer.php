<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	require_once(REL(__FILE__, "../classes/Biblio.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
		$biblios = new Biblios;
	require_once(REL(__FILE__, "../classes/Copy.php"));
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
/*
	case "getBarcdTitle":
		$copy = $copies->getByBarcode($_GET['barcodeNmbr']);
		if (empty($copy->copyid)) { echo $badBarcodeText.; exit; }
		break;
*/
	case "doItemCheckin":
		$cpy = new BarcdCopy($_POST['barcodeNmbr']);
		$copy = $cpy->getData();
		if (!$copy) { echo $badBarcodeText; exit; }

		if ($copy['status'] != 'out') {echo T("This item not checked out"); exit; }
		if (!$copy['histid']) {echo "no hist id recorded"; exit; }
		if ($copy['hold']) {
			$newStatus = OBIB_STATUS_ON_HOLD;
		} else {
			$newStatus = OBIB_STATUS_SHELVING_CART;
		}

		### post to all related files
		$cpy->setCheckedIn($newStatus);

		### post over-due fees
		$owed = $copy['lateFee'] * $copy['daysLate'];
		if ($owed > 0) {
			$acct->insert(array(
				'mbrid'=>$copy['ckoutMbr'],
				'transaction_type_cd'=>'+c',
				'amount'=>$owed,
				'description'=>T("Late fee").': '.$copy['barcode'],
			));
		}

		$msg = $copy['barcode'].' '.T("added to shelving cart.");
		echo $msg;
		break;

	case 'fetchShelvingCart':
		$scart = $copies->getShelvingCart();
		$rec = array();
		while ($copy = $scart->fetch_assoc()) {
			//$biblio = $biblios->getOne($copy['bibid']);
			$ptr = new Biblio($copy['bibid']);
			$bib = $ptr->getData();
			$status = $history->getOne($copy['histid']);
			$rec[] = array(
				'bibid'=>$copy['bibid'],
				'copyid'=>$copy['copyid'],
				'barcd'=>$copy['barcode_nmbr'],
				'beginDt'=>$status['status_begin_dt'],
				'title'=>$bib['hdr']['title'],
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
