<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../model/CopiesCustomFields.php"));
require_once(REL(__FILE__, "../model/History.php"));

/**
 * BiblioCopy-specific specification & search facilities for use with the Report generator
 * @author Micah Stetson
 */

class Copies extends CoreTable {
	public function __construct() {
		parent::__construct();
		$this->setName('biblio_copy');
		$this->setFields(array(
			'bibid'=>'number',
			'copyid'=>'number',
			'barcode_nmbr'=>'string',
			'copy_desc'=>'string',
			#'vendor'=>'string',
			#'fund'=>'string',
			#'price'=>'string',
			#'expiration'=>'string',
			'histid'=>'number',
			'siteid'=>'number',
		));
		$this->setKey('copyid');
		$this->setSequenceField('copyid');
		$this->setForeignKey('bibid', 'biblio', 'bibid');
		$this->setForeignKey('histid', 'biblio_status_hist', 'histid');
		$this->setForeignKey('siteid', 'site', 'siteid');
		
		$this->custom = new CopiesCustomFields;
		$this->custom->setName('biblio_copy_fields');
		$this->custom->setFields(array(
			'copyid'=>'string',
			'code'=>'string',
			'data'=>'string',
		));
		$this->custom->setKey('copyid', 'code');
	 }

	public function getCpyList($bibid) {
		$rslt = $this->getKeyList('copyid',array('bibid'=>$bibid));
		$cpys = array();
		while($row = $rslt->fetch_assoc()) {
			$cpys[] = $row['copyid'];
		}
		return $cpys;
	}
	public function getByBarcode($barcode) {
		$rows = $this->getMatches(array('barcode_nmbr'=>$barcode));
		if ($rows->num_rows == 0) {
			$barcode = $this->normalizeBarcode($barcode);
			$rows = $this->getMatches(array('barcode_nmbr'=>$barcode));
		}
		if ($rows->num_rows == 0) {
			return NULL;
		} else if ($rows->num_rows == 1) {
			return $rows->fetch_assoc();
		} else {
			Fatal::internalError(T("Duplicate barcode: %barcode%", array('barcode'=>$barcode)));
		}
	}
/*
	function getNextCopy() {
	  ## deprecated - retained for compatability with legacy code
		$sql = $this->mkSQL("select max(copyid) as nextCopy from biblio_copy");
		$nextCopy = $this->select1($sql);
		return $nextCopy["nextCopy"]+1;
	}
*/
	public function getNewBarCode($width) {
		//$sql = $this->mkSQL("select max(copyid) as lastCopy from biblio_copy");
		$sql = $this->mkSQL("select max(barcode_nmbr) as lastNmbr from biblio_copy");
		$cpy = $this->select1($sql);
	  if(empty($width)) $w = 13; else $w = $width;
		return sprintf("%0".$w."s",($cpy[lastNmbr]+1));
	}
	
	protected function insert_el($copy) {
		$this->lock();
		list($id, $errors) = parent::insert_el($copy);
		if (!$errors) {
			$history = new History;
			$history->insert(array(
				'bibid'=>$copy['bibid'], 'copyid'=>$id, 'status_cd'=>'in', 'siteid'=>$copy['siteid'],
			));
		}
		$this->unlock();
		return array($id, $errors);
	}
	protected function validate_el($copy, $insert) {
		$errors = array();
		foreach (array('bibid', 'barcode_nmbr') as $req) {
			if ($insert and !isset($copy[$req])
					or isset($copy[$req]) and $copy[$req] == '') {
				$errors[] = new FieldError($req, T("Required field missing"));
			}
		}
		if($this->isDuplicateBarcd($copy['barcode_nmbr'], $copy['copyid'])){
			$errors[] = new FieldError('barcode_nmbr', T("Barcode number already in use."));
		}
		return $errors;
	}
	public function isDuplicateBarcd($barcd,$cpyid) {
		/* Check for duplicate barcodes */
		/* broken out from validate_el() for access by client via AJAX - fl*/
		if (isset($barcd)) {
			$sql = $this->mkSQL("select count(*) count from biblio_copy "
				. "where barcode_nmbr=%Q ", $barcd);
			if (isset($cpyid)) {
				$sql .= $this->mkSQL("and not copyid=%N ", $cpyid);
			}
			$duplicates = $this->select1($sql);
			if ($duplicates['count'] > 0) {
				$errors[] = new FieldError('barcode_nmbr', T("Barcode number already in use."));
				return true;
			}
			return false;
		}
	}

	/**
	 * Convert a barcode to the preferred form.
	 * Currently this strips leading zeros, possibly after an
	 * alphabetic prefix.
	 */
	protected function normalizeBarcode($barcode) {
		return preg_replace('/^([A-Za-z]+)?0*(.*)/', '\\1\\2', $barcode);
	}

	public function getMemberCheckouts($mbrid) {
		$sql = "select c.copyid "
			. "from biblio_copy c, booking b, booking_member m "
			. "where c.histid = b.out_histid "
			. "and m.bookingid = b.bookingid ";
		$sql .= $this->mkSQL("and m.mbrid=%N ", $mbrid);
		return $this->select($sql);
	}

	public function getCheckoutMember($histid) {
		$sql = "select mbr.* "
				 . "from member mbr, booking bk, booking_member bkm "
				 . "where mbr.mbrid=bkm.mbrid "
				 . "and bkm.bookingid=bk.bookingid ";
		$sql .= $this->mkSQL("and bk.out_histid=%N ", $histid);
		$result = $this->select($sql);
		return ($result->fetch_assoc());
	}

	public function getHoldMember($copyid) {
		$sql = "select mbr.* "
				 . "from member mbr, biblio_hold bh "
				 . "where mbr.mbrid=bh.mbrid ";
		$sql .= $this->mkSQL("and bh.copyid=%N order by bh.hold_begin_dt", $copyid);
		$result = $this->select($sql);
		return ($result->fetch_assoc());
	}	

	public function lookupBulk_el($barcodes) {
		$copyids = array();
		$bibids = array();
		$errors = array();
		foreach ($barcodes as $b) {
			$copy = $this->getByBarcode($b);
			if (!$copy) {
				$errors[] = new Error(T("No copy with barcode %barcode%", array('barcode'=>$b)));
			} else {
				if (!in_array($copy['copyid'], $copyids)) {
					$copyids[] = $copy['copyid'];
				}
				if (!in_array($copy['bibid'], $bibids)) {
					$bibids[] = $copy['bibid'];
				}
			}
		}
		return array($copyids, $bibids, $errors, $barcodes);
	}
	public function lookupAvability ($bibid) {
		$sql = "select c.copyid, c.siteid, h.status_cd "
				 . "from biblio_copy c, biblio_status_hist h "
				 . "where (c.bibid = {$bibid}) "
				 . "  and (h.histid = c.histid) ";
		$rslt = $this->select($sql);
		$nCpy = $rslt->num_rows;

		## default - copy not available
		$avIcon = "circle_red.png";

		while ($row = $rslt->fetch_assoc()) {
			if($row['status_cd'] == OBIB_STATUS_IN) {
				// See on which site
				if($_SESSION['current_site'] == $row['siteid'] || !($_SESSION['multi_site_func'] > 0)){
					$avIcon = "circle_green.png"; // one or more available
					break;
				} else {
					$avIcon = "circle_orange.png"; // one or more available on another site
				}
			}
			// Removed && $this->avIcon != "circle_orange.png" as and extra clause, as it is better to show the book is there, even if not available
			else if($copy[status_cd] == OBIB_STATUS_ON_HOLD || $copy[status_cd] == OBIB_STATUS_NOT_ON_LOAN) {
				$avIcon = "circle_blue.png"; // only copy is on hold
			}
		}
		$rcd['nCpy'] = $nCpy;
		$rcd['avIcon'] = $avIcon;
		return $rcd;
	}
	public function lookupNoCopies($bibids, $del_copyids) {
		$no_copies = array();
		foreach ($bibids as $bibid) {
			$has_copies = false;
			$copies = $this->getMatches(array('bibid'=>$bibid));
			while ($c = $copies->fetch_assoc()) {
				if (!in_array($c['copyid'], $del_copyids)) {
					$has_copies = true;
					break;
				}
			}
			if (!$has_copies) {
				$no_copies[] = $bibid;
			}
		}
		return $no_copies;
	}
	public function getShelvingCart() {
		$sql = "select bc.* "
			. "from biblio_copy bc, biblio_status_hist bsh "
			. "where bc.histid=bsh.histid "
			. $this->mkSQL("and bsh.status_cd=%Q ",
				OBIB_STATUS_SHELVING_CART);
		//echo "sql=$sql<br />\n";
		return $this->select($sql);
	}
	/* way incomplete, done in Copy object now
	function checkin($bibids,$copyids) {
		$this->lock();
		$history = new History;
		for ($i=0; $i < count($bibids); $i++) {
		 $hist = array(
			 'bibid'=>$bibids[$i],
			 'copyid'=>$copyids[$i],
			 'status_cd'=>OBIB_STATUS_IN,
		 );
		 $history->insert($hist);
		}
		$this->unlock();
	}
	*/
	public function massCheckin() {
		$this->lock();
		$cart = $this->getShelvingCart();
		$bibids = array();
		$copyids = array();
		while ($copy = $cart->fetch_assoc()) {
			array_push($bibids, $copy['bibid']);
			array_push($copyids, $copy['copyid']);
		}
		$this->checkin($bibids, $copyids);
		$this->unlock();
	}
	public function getCustomFields($copyid, $arrayWanted=false) {
		$rslt = $this->custom->getMatches(array('copyid'=>$copyid));
		if ($arrayWanted) {
			while ($row = $rslt->fetch_assoc()) {
				$flds[] = $row;
			}
			return $flds;
		}
		return $rslt;
	}
	public function deleteCustomFields($copyid) {
		return $this->custom->deleteMatches(array('copyid'=>$copyid));
	}

	public function setCustomFields($copyid, $customFldsarr) {
		$this->custom->deleteMatches(array('copyid'=>$copyid));
		foreach ($customFldsarr as $code => $data) {
			$fields= array(
				copyid=>$copyid ,
				code=>$code,
				data=>$data
			);
			$this->custom->insert($fields);
		}
	}
}
