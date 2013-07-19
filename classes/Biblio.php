<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/BiblioImages.php"));
require_once(REL(__FILE__, "../model/MarcStore.php"));

class Biblio {
	private $bibid;
	private $hdrFlds = array();
	private $marcFlds = array();
	##----------------------##
	public function __construct ($bibid) {
		$this->bibid = $bibid;
		$this->fetch_biblio();
		$this->fetch_marc();
		$this->fetch_photoData();
	}
	public function getData () {
		$data = [];
		$data['hdr'] = $this->hdrFlds;
		$data['marc'] = $this->marcFlds;
		return $data;
	}
	##----------------------##
	private function fetch_biblio () {
		$ptr = new Biblios;
		$rslt = $ptr->getOne($this->bibid);
		$this->hdrFlds['bibid'] = $rslt['bibid'];
		$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
		$this->hdrFlds['material_cd'] = $rslt['material_cd'];
		$this->hdrFlds['opac_flg'] = $rslt['opac_flg'];
	}
	private function fetch_marc () {
		$mrc = new MarcStore;
		$rslt = $mrc->fetchMarcFlds ($this->bibid);
		while ($row = $rslt->fetch_assoc()) {
			$theTag = $row['tag'];
			$theSuff = $row['subfield_cd'];
			$theValue = $row['subfield_data'];
			$this->marcFlds[$theTag.'$'.$theSuff] = $theValue;
		}
	}
	private function fetch_photoData () {
		$img = new BiblioImages;
    $rslt = $img->getByBibid($this->bibid);
		$row = $rslt->fetch_assoc();
		$this->hdrFlds['img'] = $row['url'];
	}
}

?>
