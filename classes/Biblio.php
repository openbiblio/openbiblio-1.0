<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/BiblioImages.php"));
require_once(REL(__FILE__, "../model/MarcStore.php"));
require_once(REL(__FILE__, "../model/MaterialFields.php"));

/**
 * provides a view of a single biblio - all relevent data in a single place.
 * @author: Fred LaPlante, 25 July 2013
 */

class Biblio {
	private $bibid;
	private $hdrFlds = array();
	private $marcFlds = array();
	private $cpyList = array();

	## ------------------------------------------------------------------------ ##
	public function __construct ($bibid) {
		$this->bibid = $bibid;
	}
	/**
	 * returns a complete description of current Biblio as an array of 2 arrays
	 * 'hdr' an associative array of all primitive basic data
	 * 'marc' an associative array of all existing tags with a xxx$x key; each
	 * tag has an array containing position, label, and value
	 */
	public function getData () {
		$this->fetch_biblio();
		$this->fetch_marc();
		$this->fetch_title();
		$this->fetch_photoData();
		$this->fetch_copyList();
		$data = [];
		$data['hdr'] = $this->hdrFlds;
		$data['marc'] = $this->marcFlds;
		$data['cpys'] = $this->cpyList;
		return $data;
	}
	public function setData ($data) {
		$this->hdrFlds['bibid'] = $rslt['bibid'];
		$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
		$this->hdrFlds['material_cd'] = $rslt['material_cd'];
		$this->hdrFlds['opac_flg'] = $rslt['opac_flg'];
	}

	## ------------------------------------------------------------------------ ##
	private function fetch_biblio () {
		$ptr = new Biblios;
		$rslt = $ptr->getOne($this->bibid);
		$this->hdrFlds['bibid'] = $rslt['bibid'];
		$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
		$this->hdrFlds['material_cd'] = $rslt['material_cd'];
		$this->hdrFlds['opac_flg'] = $rslt['opac_flg'];
		$this->hdrFlds['createDt'] = $rslt['create_dt'];
	}
	private function fetch_marc () {
		$mat = new MaterialFields;
		$this->marcFlds = $mat->getMediaTags($this->hdrFlds['material_cd']);

		$mrc = new MarcStore;
		$rslt = $mrc->fetchMarcFlds($this->bibid);
		if ($rslt->num_rows <= 1) die(T("Nothing Found"));
		$repFlgs = [];
		while ($row = $rslt->fetch_assoc()) {
			$tag = $row['tag'].'$'.$row['subfield_cd'];
			if ($this->marcFlds[$tag]['repeatable'] > 0) {
				if (!$repFlgs[$tag])
          $repFlgs[$tag] = 0;
				else
					$repFlgs[$tag]++;
        if ($repFlgs[$tag] > 0) $tag .= '#'.$repFlgs[$tag];
			}
			$this->marcFlds[$tag]['value'] = $row['subfield_data'];
			$this->marcFlds[$tag]['fieldid'] = $row['fieldid'];
			$this->marcFlds[$tag]['subfieldid'] = $row['subfieldid'];
		}
	}
	private function fetch_title () {
		$bibMarc = $this->marcFlds;
		$a = $bibMarc['240$a']['value'];
		$b = $bibMarc['245$a']['value'];
		$c = $bibMarc['245$b']['value'];
		$d = $bibMarc['246$a']['value'];
		$e = $bibMarc['246$b']['value'];
		$title = T("Nothing Found");
		if (!empty($a) || !empty($b) || !empty($c)) $title = $a.' '.$b.' '.$c;
		if (!empty($d) || !empty($e)) $title = $d.' '.$e;
		$this->hdrFlds['title'] = $title;
	}
	private function fetch_photoData () {
		$img = new BiblioImages;
    $rslt = $img->getByBibid($this->bibid);
		$row = $rslt->fetch_assoc();
		$this->hdrFlds['img'] = $row['url'];
	}
	private function fetch_copyList () {
		$cpys = new Copies;
		$this->cpyList = $cpys->getCpyList($this->bibid);
	}
}

?>
