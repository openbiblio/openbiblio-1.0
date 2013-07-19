<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/Bookings.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Collections.php"));
require_once(REL(__FILE__, "../model/MediaTypes.php"));

class Copy {
	private $copyid;
	##----------------------##
	public function __construct($copyid) {
		$this->copyid = $copyid;
		$this->fetch_copy();
		$this->fetch_status();
	}
	public function getData() {
		//$data = [];
		//$data = array($this->hdrFlds);
		//return $data;
		return $this->hdrFlds;
	}
	##----------------------##
	private function fetch_copy() {
		$ptr = new Copies;
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
		$rslt = $ptr->getOne($this->hdrFlds['histid']);
		$this->hdrFlds['status'] = $rslt['status_cd'];
		if ($rslt['status_cd'] == 'out') {
			$ptr = new Biblios;
			$rslt = $ptr->getOne($this->hdrFlds['bibid']);
			$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
			$this->hdrFlds['material_cd'] = $rslt['material_cd'];

			$ptr = new Bookings;
			$rslt = $ptr->getByHistid($this->hdrFlds['histid']);
      $this->hdrFlds['out_dt'] = explode(' ', $rslt['out_dt'])[0];
      $this->hdrFlds['due_dt'] = $rslt['due_dt'];
      $this->hdrFlds['daysLate'] = $ptr->getDaysLate($rslt);

			$ptr = new CircCollections;
			$rslt = $ptr->getOne($this->hdrFlds['collection_cd']);
			$this->hdrFlds['lateFee'] = $rslt['daily_late_fee'];

			$ptr = new MediaTypes;
			$rslt = $ptr->getOne($this->hdrFlds['material_cd']);
			$this->hdrFlds['media'] = $rslt['description'];
		}
	}
}
?>
