<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../model/BiblioImages.php"));
require_once(REL(__FILE__, "../catalog/biblioChange.php"));
require_once(REL(__FILE__, "../model/MarcStore.php"));
require_once(REL(__FILE__, "../model/MaterialFields.php"));

/**
 * Provides a view of a single biblio - all relevent data in a single place.
 * First call of getData() will populate all parts of the object and return an
 * array of header ['hdr'], marc fields ['marc'], and a list of copy ids ['cpys'].
 * Among other thngs, the hdr array contains the biblio title extracted from the marc fields.
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
	 * returns a complete description of current Biblio as an array of 3 arrays
	 * 'hdr' an associative array of all primitive basic data
	 * 'marc' an associative array of all existing tags with a xxx$x key; each
	 *      tag has an array containing position, label, and value.
	 * ['cpys'] contains a list of copy ids.
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
	/**
	 * Populates an empty biblio with header data.
	 */
	public function setHdr($data) {
		$this->hdrFlds['bibid'] = $data['bibid'];
		$this->hdrFlds['collection_cd'] = $data['collection_cd'];
		$this->hdrFlds['material_cd'] = $data['material_cd'];
		$this->hdrFlds['opac_flg'] = $data['opac_flg'];
		if ($data['create_dt'])
			$this->hdrFlds['createDt'] = $data['create_dt'];
	}
	/**
	 * Popuulates an empty biblio with Marc fields,
	 * and causes the header title field to be populated.
	 */
	public function setMarc($data) {
		foreach ($data as $key=>$val){
			$this->marcFlds[$key]['value'] = $val['data'];
			$parts = explode('&', $val['codes']);
			$fldParts = (explode('=',$parts[1]));
			$subParts = (explode('=',$parts[0]));
			$this->marcFlds[$key]['fieldid'] = $fldParts[1];
			$this->marcFlds[$key]['subfieldid'] = $subParts[1];
			$this->fetch_title();  ## re-builds from above entries
		}
	}
	/**
	 * causes the database to be updated with current biblio data
	 */
	public function updateDB () {
/*
		$ptr = new Biblios;
		$biblioRec = array('bibid'=>$this->hdrFlds['bibid'],
                       'collection_cd'=>$this->hdrFlds['material_cd'],
                       'material_cd'=>$this->hdrFlds['material_cd'],
                       'opac_flg'=>$this->hdrFlds['opac_flg'],
                       'createDt'=>$this->hdrFlds['create_dt'],
											 'marc'=>$this->marcFlds,
											);
		$msg = $ptr->update($biblioRec);
*/
    $msg = postBiblioChange('');
		echo $msg;
	}

	## ------------------------------------------------------------------------ ##
	private function fetch_biblio () {
		## get data from db
		$ptr = new Biblios;
		$rslt = $ptr->getOne($this->bibid);
		## post relevent info to this object
		$this->hdrFlds['bibid'] = $rslt['bibid'];
		$this->hdrFlds['collection_cd'] = $rslt['collection_cd'];
		$this->hdrFlds['material_cd'] = $rslt['material_cd'];
		$this->hdrFlds['opac_flg'] = $rslt['opac_flg'];
		$this->hdrFlds['createDt'] = $rslt['create_dt'];
	}
	private function fetch_marc () {
		## get marc field list more this biblio's media type
		$mat = new MaterialFields;
		$this->marcFlds = $mat->getMediaTags($this->hdrFlds['material_cd']);
		## retrieve all existing marc data for this biblio
		$mrc = new MarcStore;
		$rslt = $mrc->fetchMarcFlds($this->bibid);
		if ($rslt->num_rows <= 1) return 'MARC '.T("Nothing Found");
		$firstRep = true;
		while ($row = $rslt->fetch_assoc()) {
			$tag = $row['tag'].'$'.$row['subfield_cd'];
			if ($this->marcFlds[$tag.'$1']['repeatable'] > 0) {
				if($firstRep) {
					$firstRep = false;
					$rep = 1;
				} else {
					$rep++;
				}
				$tag .= '$'.$rep;
//echo"Biblio: marcFlds {$tag} row===>";print_r($row);echo"<br/>\n";
			}
			## merge data with structure and post to this object
			$this->marcFlds[$tag]['value'] = $row['subfield_data'];
			$this->marcFlds[$tag]['fieldid'] = $row['fieldid'];
			$this->marcFlds[$tag]['subfieldid'] = $row['subfieldid'];
		}
	}
	private function fetch_title () {
		## select all 'title' field material previously collected
		$bibMarc = $this->marcFlds;
		$a = $bibMarc['240$a']['value'];
		$b = $bibMarc['245$a']['value'];
		$c = $bibMarc['245$b']['value'];
		$d = $bibMarc['246$a']['value'];
		$e = $bibMarc['246$b']['value'];
		$title = T("Nothing Found");
		## build second-choice title string
		if (!empty($d) || !empty($e)) $title = $d.' '.$e;
		## build first-choice title string
		if (!empty($a) || !empty($b) || !empty($c)) $title = $a.' '.$b.' '.$c;
		## post title to this object
		$this->hdrFlds['title'] = $title;
	}
	private function fetch_photoData () {
		## get photo link from db
		$img = new BiblioImages;
    $rslt = $img->getByBibid($this->bibid);
		$row = $rslt->fetch_assoc();
		## post photo link to this object
		$this->hdrFlds['img'] = $row['url'];
	}
	private function fetch_copyList () {
		## get copy ids from db
		$cpys = new Copies;
		## post list to this object
		$this->cpyList = $cpys->getCpyList($this->bibid);
		## determine copy availability from db status records
		$info = $cpys->lookupAvability ($this->bibid);
		## post availability icon and number of copies to this object
		$this->hdrFlds['avIcon'] = $info['avIcon'];
		$this->hdrFlds['ncpys'] = $info['nCpy'];
	}
}

?>
