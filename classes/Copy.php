<?php
/**
 * provides a view of a single copy of a biblio - all relevent data in a single place.
 * upon creation with a copyid, the objet will be fully populated with all data.
 * @author Fred LaPlante 24 July 2013
 */

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
	/**
	 * creates a new Copy object, complete with relevent data
	 */
	public function __construct($identifier, $is_barcode=False) {
		if ($is_barcode) {
			$ptr = new Copies;
			$cpy = $ptr->getByBarcode($identifier);
			if(!$cpy) {
				die(T("No copy with barcode")." ".$barcode);
			}
			$copyid = $cpy['copyid'];
		} else {
			$copyid = $identifier;
		}
		$this->copyid = $copyid;
		$this->fetch_copy();
		$this->fetch_status();
		$this->fetch_custom();
	}

	/**
	 * returns an associtive array of this copy's data
	 */
	public function getData() {
		return $this->hdrFlds;
	}

	/**
	 * sets the status of this copy to 'checkedin' (in),
	 * 	 and adjusts other DB tables as necessary
	 */
	public function setCheckedIn() {
		$this->hdrFlds['status'] = OBIB_STATUS_SHELVING_CART;
		$this->insert_statusHist();
		$this->update_copy();
		$this->update_booking();
	}

	/**
	 * sets the status of this copy to shelved (crt),
	 * 	 and adjusts other DB tables as necessary
	 */
	public function setShelved() {
		$this->hdrFlds['status'] = OBIB_STATUS_IN;
		$this->insert_statusHist();
		$this->update_copy();
	}

    /**
     * delete copy and all related records
     */
    public function deleteCopy() {
		$ptr = new Copies;
		$ptr->deleteCopy($this->copyid);
    }

	##----------------------##
	private function insert_statusHist () {
		$newHistid = $this->hist->insert(array(
			'bibid'=>$this->hdrFlds['bibid'],
			'copyid'=>$this->copyid,
			'status_cd'=>$this->hdrFlds['status'],
			'bookingid'=>$this->hdrFlds['bookingid'],
		));
		$this->hdrFlds['histid'] = $newHistid[0];
//echo "newHistid=";print_r($newHistid);echo "<br>\n";

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
	private function fetch_custom() {
		$ptr = new Copies;
		$flds = $ptr->getCustomFields($this->copyid, true);
		if (!$flds) return;
		foreach ($flds as $fld) {
            $this->hdrFlds['custom'][$fld['code']] = $fld['data'];
		}
	}
	private function fetch_status() {
		$ptr = new Biblios;
		$this->bib = $ptr;
		$rslt = $ptr->getOne($this->hdrFlds['bibid']);
		$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
		$this->hdrFlds['material_cd'] = $rslt['material_cd'];

		$ptr = new MediaTypes;
		$this->media = $ptr;
		$rslt = $ptr->getOne($this->hdrFlds['material_cd']);
		$this->hdrFlds['media'] = $rslt['description'];

		$ptr = new Holds;
		$this->hold = $ptr;
		$rslt = $ptr->getFirstHold($this->hdrFlds['copyid']);
		$this->hdrFlds['hold_cd'] = $rslt;

		$ptr = new History;
		$this->hist = $ptr;
		$rslt = $ptr->getOne($this->hdrFlds['histid']);
		$this->hdrFlds['status'] = $rslt['status_cd'];
		$this->hdrFlds['status_dt'] = $rslt['status_begin_dt'];

		if ($rslt['status_cd'] == 'out') {
			$mbr = $this->cpy->getCheckoutMember($this->hdrFlds['histid']);
			$this->hdrFlds['ckoutMbr'] = $mbr['mbrid'];
			$this->hdrFlds['mbrName'] = $mbr['first_name'].' '.$mbr['last_name'];

			$ptr = new CircCollections;
			$this->cCol = $ptr;
			$rslt = $ptr->getOne($this->hdrFlds['collection_cd']);
            $rslt['daily_late_fee']==null?$this->hdrFlds['lateFee'] =0:$this->hdrFlds['lateFee'] = $rslt['daily_late_fee'];

			$ptr = new Bookings;
			$this->book = $ptr;
			$rslt = $ptr->getByHistid($this->hdrFlds['histid']);
            $this->hdrFlds['bookingid'] = $rslt['bookingid'];
            $this->hdrFlds['out_dt'] = explode(' ', $rslt['out_dt'])[0];
            $this->hdrFlds['due_dt'] = $rslt['due_dt'];
            $this->hdrFlds['daysLate'] = $ptr->getDaysLate($rslt);
		}
	}
}
