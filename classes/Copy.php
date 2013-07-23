<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Collections.php"));
require_once(REL(__FILE__, "../model/Holds.php"));
require_once(REL(__FILE__, "../model/MediaTypes.php"));

class BarcdCopy extends Copy {
	public function __construct ($barcd) {
		$ptr = new Copies;
		$cpy = $ptr->getByBarcode($_POST['barcodeNmbr']);
		parent::__construct ($cpy['copyid']);
	}
}

class Copy {
	private $copyid;
	## object pointers
	private $cpy;
	private $hist;
	private $bib;
	private $book;
	private $hold;
	private $cCol;
	private $media;

	##----------------------##
	public function __construct($copyid) {
		$this->copyid = $copyid;
		$this->fetch_copy();
		$this->fetch_status();
	}
	public function getData() {
		return $this->hdrFlds;
	}
	public function setCheckedIn($statusCd) {
		$this->hdrFlds['status'] = $statusCd;
		$newHistid = $this->hist->insert(array(
			'bibid'=>$this->hdrFlds['bibid'],
			'copyid'=>$this->copyid,
			'status_cd'=>$statusCd,
			'bookingid'=>$this->hdrFlds['bookingid'],
		));
		$this->cpy->update(array(
			'copyid'=>$this->copyid,
			'histid'=>$newHistid,
		));
		$this->book->update(array(
			'bookingid'=>$this->hdrFlds['bookingid'],
			'ret_histid'=>$newHistid,
			'ret_dt'=>date('Y-m-d H:i:s'),
			'mbrids'=>array($this->hdrFlds['ckoutMbr']),
		));
		$this->hdrFlds['histid'] = $newHistid;
	}
	public function setShelved($statusCd) {
		$this->hdrFlds['status'] = $statusCd;
		$this->insert_statusHist();
		$this->update_copy();
	}
	##----------------------##
	private function insert_statusHist () {
		$newHistid = $this->hist->insert(array(
			'bibid'=>$this->hdrFlds['bibid'],
			'copyid'=>$this->copyid,
			'status_cd'=>$this->hdrFlds['status'],
			'bookingid'=>$this->hdrFlds['bookingid'],
		));
		$this->hdrFlds['histid'] = $newHistid;
	}
	private function update_copy () {
		$this->cpy->update(array(
			'copyid'=>$this->copyid,
			'histid'=>$this->hdrFlds['histid'],
		));
	}
	private function update_booking () {
		$this->book->update(array(
			'bookingid'=>$this->hdrFlds['bookingid'],
			'ret_histid'=>$this->hdrFlds['histid'],
			'ret_dt'=>date('Y-m-d H:i:s'),
			'mbrids'=>array($this->hdrFlds['ckoutMbr']),
		));
	}
	private function fetch_copy() {
		$ptr = new Copies;
		$this->cpy = $ptr;
		$rslt = $ptr->getOne($this->copyid);
		$this->hdrFlds['copyid'] = $rslt['copyid'];
		$this->hdrFlds['bibid'] = $rslt['bibid'];
		$this->hdrFlds['barcode'] = $rslt['barcode_nmbr'];
		$this->hdrFlds['siteid'] = $rslt['siteid'];
		$this->hdrFlds['histid'] = $rslt['histid'];
		$this->hdrFlds['desc'] = $rslt['copy_desc'];
	}
	private function fetch_status() {
		$ptr = new History;
		$this->hist = $ptr;
		$rslt = $ptr->getOne($this->hdrFlds['histid']);
		$this->hdrFlds['status'] = $rslt['status_cd'];
		if ($rslt['status_cd'] == 'out') {
			$mbr = $this->cpy->getCheckoutMember($this->hdrFlds['histid']);
			$this->hdrFlds['ckoutMbr'] = $mbr['mbrid'];

			$ptr = new Biblios;
			$this->bib = $ptr;
			$rslt = $ptr->getOne($this->hdrFlds['bibid']);
			$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
			$this->hdrFlds['material_cd'] = $rslt['material_cd'];

			$ptr = new Bookings;
			$this->book = $ptr;
			$rslt = $ptr->getByHistid($this->hdrFlds['histid']);
      $this->hdrFlds['bookingid'] = $rslt['bookingid'];
      $this->hdrFlds['out_dt'] = explode(' ', $rslt['out_dt'])[0];
      $this->hdrFlds['due_dt'] = $rslt['due_dt'];
      $this->hdrFlds['daysLate'] = $ptr->getDaysLate($rslt);

			$ptr = new Holds;
			$this->hold = $ptr;
			$rslt = $ptr->getFirstHold($this->hdrFlds['copyid']);
			$this->hdrFlds['hold_cd'] = $rslt;

			$ptr = new CircCollections;
			$this->cCol = $ptr;
			$rslt = $ptr->getOne($this->hdrFlds['collection_cd']);
			$this->hdrFlds['lateFee'] = $rslt['daily_late_fee'];

			$ptr = new MediaTypes;
			$this->media = $ptr;
			$rslt = $ptr->getOne($this->hdrFlds['material_cd']);
			$this->hdrFlds['media'] = $rslt['description'];
		}
	}
}
?>
