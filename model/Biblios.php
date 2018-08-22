<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/CoreTable.php"));
require_once(REL(__FILE__, "../classes/Iter.php"));
require_once(REL(__FILE__, "../functions/marcFuncs.php"));
require_once(REL(__FILE__, "../classes/MarcStore.php"));
require_once(REL(__FILE__, "../model/BiblioImages.php"));

/**
 * Biblio specification & search facilities
 * additional functions from SrchDb integrated 3 Aug 2013 - FL
 * some duplication apparent, re-factoring is probably desirable. - FL
 * @author Micah Stetson
 */

class Biblios extends CoreTable {
	private $avIcon = "../images/circle_green.png";

	public function __construct() {
		parent::__construct();
        $this->setName('biblio');
        $this->setFields(array(
        	'bibid'=>'number',
        	'material_cd'=>'number',
        	'collection_cd'=>'number',
        	'opac_flg'=>'string',
        ));
        $this->setKey('bibid');
        $this->setSequenceField('bibid');
        $this->setIter('BiblioIter');

        $this->marc = new MarcStore;
        $this->marcRec = new MarcRecord;
	}

	## ========================= ##
	public function getBiblioByBarcd($barcd){
		$sql = "SELECT b.bibid "
			."  FROM `biblio_copy` bc,`biblio` b "
			."  WHERE (bc.`barcode_nmbr` = '".$barcd."')"
			."	AND (b.`bibid` = bc.`bibid`)";
		$rcd = $this->select01($sql);
		$this->barCd = $barcd;
		$this->bibid = $rcd[bibid];
		return $rcd;
	}

	## ========================= ##
	public function getBiblioByPhrase($criteria, $mode=null) {
		// actual sort string for a search is created by the following line
		// warning! not straight-forward, be careful requires good understanding of MARC field intentions

		$jsonSpec = $this->makeParamStr($criteria);   // see routine near bottom of this file
		/* mode may be null at times */
	    $spec = json_decode($jsonSpec, true);

	    $srchTxt = strtolower($_POST['searchText']);
	    if ($mode == 'words')
			$keywords = explode(' ',$srchTxt);
		else
			$keywords[] = $srchTxt;

		// Add Slashes for search - LJ
		for($i = 0; $i < count($keywords); $i++){
			$keywords[$i] = addslashes(stripslashes($keywords[$i]));
		}

		$sqlSelect= "SELECT DISTINCT b.bibid FROM `biblio` AS b";
		$sqlWhere = " WHERE (1=1)";
		$keywordnr = 1;
		foreach ($keywords as $kwd) {
			// Add Join
			$sqlSelect .= " JOIN `biblio_field` bf$keywordnr JOIN `biblio_subfield` bs$keywordnr";
			$sqlWhere  .= "  AND bf$keywordnr.bibid = b.bibid "
						 ."  AND bs$keywordnr.fieldid = bf$keywordnr.fieldid "
						 ."  AND bs$keywordnr.`subfield_data` LIKE '%$kwd%'";
			$termnr = 1;
			$sqlWhere .= " AND (";
			$firstWhere = true;
			//$temp = '';
			foreach ($spec as $item) {
				// Continue when the item has to do with selecting and ordering
				if(!isset($item['tag'])) continue;
				if(!$firstWhere) $sqlWhere .= " OR";
				$firstWhere = false;
				$sqlWhere .= " (bf$keywordnr.tag='" . $item['tag'] . "' AND bs$keywordnr.subfield_cd = '" . $item['suf'] . "')";
				//$temp .= " bf$keywordnr.tag='" . $item['tag'] . "' AND bs$keywordnr.subfield_cd = '" . $item['suf'] . "'";
				$termnr++;
			}

			$sqlWhere .= ")";
			$keywordnr++;
		}
		// Now do the selecting and ordering
		$selectNr = 1;
		foreach ($spec as $item) {

/*
    You can't do this here. - FL May 2016
    All items in 'Order by' must also be in 'Select' list

			if(isset($item['orderTag'])){
				// If order is already specified, add a secondairy
				if(!isset($sqlOrder)){
					$orderNo = 1;
					$sqlSelect .= " LEFT JOIN biblio_field AS sortf" . $orderNo . " ON sortf" . $orderNo . ".bibid = `b`.`bibid`"
						." AND sortf" . $orderNo . ".tag = '" . $item['orderTag'] . "'"
						." LEFT JOIN biblio_subfield AS sorts" . $orderNo . " ON sorts" . $orderNo . ".fieldid = sortf" . $orderNo . ".fieldid"
						." AND sorts" . $orderNo . ".subfield_cd = '" . $item['orderSuf'] . "'";
					$sqlOrder = " ORDER BY sorts" . $orderNo . ".`subfield_data`";
				} else {
					$orderNo++;
					$sqlSelect .= " LEFT JOIN biblio_field AS sortf" . $orderNo . " ON sortf" . $orderNo . ".bibid = `b`.`bibid`"
						." AND sortf" . $orderNo . ".tag = '" . $item['orderTag'] . "'"
						." LEFT JOIN biblio_subfield AS sorts" . $orderNo . " ON sorts" . $orderNo . ".fieldid = sortf" . $orderNo . ".fieldid"
						." AND sorts" . $orderNo . ".subfield_cd = '" . $item['orderSuf'] . "'";
					$sqlOrder .= ", sorts" . $orderNo . ".`subfield_data`";
				}
			}
*/

			if(isset($item['siteTag'])){
				$sqlSelect .= " JOIN `biblio_copy` bc";
				$sqlWhere .= " AND bc.bibid = b.bibid AND bc.siteid = '" . $item['siteValue'] . "' ";
			}
			if(isset($item['mediaTag'])){
				$sqlWhere .= " AND b.material_cd = '" . $item['mediaValue'] ."'";
			}
			if(isset($item['collTag'])){
				$sqlWhere .= " AND b.collection_cd = '" . $item['collValue'] ."'";
			}
			//if (isset($item['marcTag'])){
			//	$sqlSelect .= " JOIN `biblio_field` sf$selectNr JOIN `biblio_subfield` ss$selectNr";
			//	$sqlWhere .= " AND (sf$selectNr.tag='".$item['tag']."' AND ss$selectNr.subfield_cd='".$item['suf']."')";
			//}
			if(isset($item['audienceTag'])){

			$sqlSelect .= " JOIN `biblio_field` bf$keywordnr JOIN `biblio_subfield` bs$keywordnr";
			$sqlWhere  .= "  AND bf$keywordnr.bibid = b.bibid "
									 ."  AND bs$keywordnr.fieldid = bf$keywordnr.fieldid "
									 ."  AND bs$keywordnr.`subfield_data` LIKE '%" .$item['audienceValue']."%'";
			$sqlWhere .= " AND (bf$keywordnr.tag='521' AND bs$keywordnr.subfield_cd = 'a')";

			}
			if(isset($item['toTag'])){
				$sqlSelect .= " JOIN `biblio_field` sf$selectNr JOIN `biblio_subfield` ss$selectNr";
				$sqlWhere .= " AND (sf$selectNr.bibid = b.bibid "
									." AND ss$selectNr.fieldid = sf$selectNr.fieldid"
									." AND sf$selectNr.tag='" . $item['toTag'] . "' AND ss$selectNr.subfield_cd = '" . $item['toSuf'] . "'"
									." AND ss$selectNr.`subfield_data` < " . $item['toValue'] . ")";
			}
			if(isset($item['fromTag'])){
				$sqlSelect .= " JOIN `biblio_field` sf$selectNr JOIN `biblio_subfield` ss$selectNr";
				$sqlWhere .= " AND (sf$selectNr.bibid = b.bibid "
									." AND ss$selectNr.fieldid = sf$selectNr.fieldid"
									." AND sf$selectNr.tag='" . $item['fromTag'] . "' AND ss$selectNr.subfield_cd = '" . $item['fromSuf'] . "'"
									." AND ss$selectNr.`subfield_data` > " . $item['fromValue'] . ")";
			}
			$selectNr++;
		}

		$sql = $sqlSelect . $sqlWhere . $sqlOrder;
//echo "in Biblios::getBiblioByPhrase(); sql = $sql<br>\n";
		$rows = $this->select($sql);
        //if ($rows->num_rows < 1) return NULL;
		//while (($row = $rows->fetch_assoc()) !== NULL) {
        foreach ($rows as $row) {
			$rslt[] = $row[bibid];
		}
		return $rslt;
	}
	## ========================= ##
	public function getBiblioInfo($bibid) {
	  $this->bibid =$bibid;
		$sql = "SELECT DISTINCT b.*, m.description, cd.description, cc.`days_due_back`, m.image_file "
					."	FROM `biblio` b,`material_type_dm` m,"
					."			 `collection_dm` cd, `collection_circ` cc"
					." WHERE (b.`bibid` = '$bibid')"
					."	 AND (m.`code` = b.`material_cd`)"
					."	 AND (cd.`code` = b.`collection_cd`)"
					."	 AND (cc.`code` = b.`collection_cd`)";
		$rcd = $this->select01($sql);

		$this->createDt = $rcd['create_dt'];
		$this->daysDueBack = $rcd['days_due_back'];
		$this->imageFile =$rcd['image_file'];
		$this->opacFlg = $rcd['opac_flg'];

		#### following intended to deal with a bad database, these conditions should never happen ####
		if ( empty($rcd['material_cd'])) {
			$ptr = new MediaTypes;
			$this->matlCd = $ptr->getDefault();
		} else {
			$this->matlCd = $rcd['material_cd'];
		}
		if (empty($rcd['collection_cd'])) {
			$ptr = new Collections;
			$this->collCd = $ptr->getDefault();
		} else {
			$this->collCd = $rcd['collection_cd'];
		}
		#### end of bad data fix ####

		// If the show details OPAC  flag is set get info on the copies
			$copies = $this->getCopyInfo($bibid);
			// Need to add site specific code in here in here, for now just look for
			// status options: available, available on other site, on hold, not available
			$this->nCpy = 0;
			if (!empty($copies)) {
				// default copy not available
				$this->avIcon = "../images/circle_red.png";
				foreach($copies as $copyEnc){
					$this->nCpy = $this->nCpy+1;
					$copy = json_decode($copyEnc, true);
					if($copy['statusCd'] == OBIB_STATUS_IN) {
						// See on which site
						if($_SESSION['current_site'] == $copy['siteid'] || !($_SESSION['multi_site_func'] > 0)){
							$this->avIcon = "../images/circle_green.png"; // one or more available
							break;
						} else {
							$this->avIcon = "../images/circle_orange.png"; // one or more available on another site
						}
					}
					// Removed && $this->avIcon != "../images/circle_orange.png" as and extra clause, as it is better to show the book is there, even if not available
					else if($copy[statusCd] == OBIB_STATUS_ON_HOLD || $copy[statusCd] == OBIB_STATUS_NOT_ON_LOAN)
						$this->avIcon = "../images/circle_blue.png"; // only copy is on hold
				}
			} else {
				$this->avIcon = "../images/circle_red.png"; // no copy found
			}
			$rcd['nCpy'] = $this->nCpy;
			$rcd['avIcon'] = $this->avIcon;
		return $rcd;
	}
	## ========================= ##
	public function getBiblioDetail() {
		$sql = "SELECT  CONCAT(bf.tag,bs.subfield_cd) AS marcTag, "
				 . "				m.label, bs.subfield_data AS value, "
				 . "				bs.fieldid, bs.subfieldid "
				 . "  FROM `material_fields` m, `biblio_field` bf, `biblio_subfield` bs "
				 . " WHERE (bf.`bibid` = $this->bibid) "
				 . "	 AND (bs.`bibid` = bf.`bibid`) "
				 . "	 AND (bs.`fieldid` = bf.`fieldid`) "
				 . "	 AND (m.`material_cd` = $this->matlCd) "
				 . "	 AND (m.`tag` = bf.`tag`) "
				 . "	 AND (m.`subfield_cd` = bs.`subfield_cd`) "
				 . " ORDER BY m.position ";
		$rows = $this->select($sql);
		while (($row = $rows->fetch_assoc()) !== NULL) {
			$rslt[] = json_encode($row);
		}
		return $rslt;
	}

	## ========================= ##
	public function getBiblioFields() {
	  require_once(REL(__FILE__,"../catalog/biblioFields.php"));
	}

	function deleteOne() {
		$arg0 = func_get_args(0);
        $bibid = $arg0[0];
        //echo "in Biblios::deleteOne, bibid = ";print_r($bibid);echo "<br />\n";
		$this->lock();
		$imgs = new BiblioImages;
		$imgs->deleteByBibid($bibid);

		$this->marc->delete($bibid);

		parent::deleteOne($bibid);
		$this->unlock();
	}

	function deleteMatches($fields) {
		$this->lock();
		$rows = parent::getMatches($fields);
		while ($r = $rows->fetch_assoc()) {
			$this->deleteOne($r['bibid']);
		}
		$this->unlock();
	}

	## ======================================================================== ##
	protected function validate_el($rec, $insert) {
		/*return array();*/
	}

	public function insert_el($rec, $confirmed=false) {
		$this->lock();
		if (!isset($rec['marc']) or !is_a($rec['marc'], 'MarcRecord')) {
			return array(NULL, array(new FieldError('marc', T("No MARC record set"))));
		}
		list($bibid, $errors) = parent::insert_el($rec, $confirmed);
		if ($errors) {
			return array($bibid, $errors);
		}
		$this->marc->put($bibid, $rec['marc']);

		$this->unlock();
		return array($bibid, NULL);
	}
	public function update_el($rec, $confirmed=false) {
		$this->lock();
		if (!isset($rec['bibid'])) {
			Fatal::internalError(T("No bibid set in biblio update"));
		}
		if (isset($rec['marc']) and is_a($rec['marc'], 'MarcRecord')) {
			$this->marc->put($rec['bibid'], $rec['marc']);
		}
		$r = parent::update_el($rec);
		$this->unlock();
		return $r;
	}


//	public function xupdate ($updtData) {
		/** -----------------experimental-----------------------------
		 *	currently does not post new entries to database, do NOT use
		 *  TODO should be fixed to replace mess in biblioChange.php
		 * -----------------do not remove yet ------------------------
		 */
/*
		## get user screen content
		$updts = $updtData['marc'];
		## get database content
		$crntRec = $this->marc->get($updtData['bibid']);
		//$flds = $crntRec->fields;

		function updateExisting($updts, $crntRec) {
			## scan for differences between two data sets
			## all (but only) fields present in DB are considered here.
			## mark user fields when/if scanned
			for($i=0; $i<count($crntRec->fields); $i++) {
				$field = $crntRec->fields[$i];
				foreach ($crntRec->fields[$i]->subfields as $n=>$subfld) {
					$tag = $field->tag.'$'.$subfld->identifier;
					if ($updts[$tag]) {
						$newData = $updts[$tag]['value'];
					} else {
						$tag = $field->tag.'$'.$subfld->identifier.'$'.($n+1);
						$newData = $updts[$tag]['value'];
					}
					$updts[$tag]['scanned']='yes';
					if ($newData != $subfld->data) {
						if ($crntRec->fields[$i]->subfields[$n]->data) {
	          		$crntRec->fields[$i]->subfields[$n]->data = $newData;
						}
					}
				}
			}
			return array($updts, $crntRec);
		}

		function addNewMarcFlds ($updts, $crntRec){
			## step B. review user dataset for fields not scanned above
			foreach ($updts as $key=>$value) {
				if (!array_key_exists('scanned', $value)) {
					list($tag, $suf, $seq) = explode('$', $key);
					$mrcRec = new MarcField($tag, $suf);
	        $crntRec->fields[] = $mrcRec;
				}
			}
			return array($updts, $crntRec);
		}

		list($updts, $crntRec) = updateExisting($updts, $crntRec);
		list($updts, $crntRec) = addNewMarcFlds($updts, $crntRec);
		list($updts, $crntRec) = updateExisting($updts, $crntRec);
//return "stopping as requested";

		$newBiblio = $updtData; ## has needed structure
		$newBiblio['marc'] = $crntRec; ## replaces Marc portion
		parent::update($newBiblio);
	}
*/
	## ========================= ##
	protected function makeParamStr($criteria) {
		/* typical form of $paramStr:
			[{"tag":"240","suf":"a"},{"tag":"245","suf":"a"},{"tag":"245","suf":"b"},
			 {"tag":"245","suf":"c"},{"tag":"246","suf":"a"},{"tag":"246","suf":"b"},
			 {"tag":"502","suf":"a"},{"tag":"505","suf":"a"},{"tag":"650","suf":"a"},.............
		*/
		$params = makeTagObj(getSrchTags($criteria[searchType]));

		# Add search params
		$searchTags = "";

		if(isset($criteria['sortBy'])){
				switch ($criteria['sortBy']){
				case 'author': $searchTags .= '{"orderTag":"100","orderSuf":"a"}'; break;
				case 'callno': $searchTags .= '{"orderTag":"099","orderSuf":"a"}'; break;
				case 'title':  $searchTags .= '{"orderTag":"245","orderSuf":"a"},
											   {"orderTag":"245","orderSuf":"b"},
											   {"orderTag":"240","orderSuf":"a"}'; break;
				default: $searchTags .= '{"orderTag":"245","orderSuf":"a"},
										 {"orderTag":"245","orderSuf":"b"}'; break;
			}
		}

		if($criteria['advanceQ']=='Y'){
			if(isset($criteria['srchSites']) && $criteria['srchSites'] != 'all'){
				$searchTags .= ',{"siteTag":"xxx","siteValue":"'. $criteria['srchSites'] . '"}';
			}
			if(isset($criteria['materialCd']) && $criteria['materialCd'] != 'all'){
				//Not sure about the tag, but leave it as is for the moment, as it is a field in bibid (material_cd)
				$searchTags .= ',{"mediaTag":"099","mediaSuf":"a","mediaValue":"'. $criteria['materialCd'] . '"}';
			}
			if(isset($criteria['collectionCd']) && $criteria['collectionCd'] != 'all'){
				//Not sure about the tag, but leave it as is for the moment, as it is a field in bibid (material_cd)
				$searchTags .= ',{"collTag":"099","collSuf":"a","collValue":"'. $criteria['collectionCd'] . '"}';
			}
			//if(isset($criteria['marcTag']) && $criteria['marcTag'] != 'all'){
			//	$parts = explode('$', $criteria['marcTag']);
			//	$searchTags .= ',{"marcTag":"'.$parts[0].'", "marcSuf":"'.$parts[1].'"}';
			//}
			if(isset($criteria['audienceLevel']) && $criteria['audienceLevel'] != 'all'){
				$searchTags .= ',{"audienceTag":"521","audienceSuf":"a","audienceValue":"'. $criteria['audienceLevel'] . '"}';
			}
			if(isset($criteria['to']) && strlen($criteria['to']) == 4){
				//$searchTags .= ',{"toTag":"260","toSuf":"c","toValue":"'. $criteria['to'] . '"}';
				$searchTags .= ',{"toTag":"260","toSuf":"c","toTag":"773","toSuf":"d","toValue":"'. $criteria['to'] . '"}';
			}
			if(isset($criteria['from']) && strlen($criteria['from']) == 4){
				//$searchTags .= ',{"fromTag":"260","fromSuf":"c","fromValue":"'. $criteria['from'] . '"}';
				$searchTags .= ',{"fromTag":"260","fromSuf":"c","fromTag":"773","fromSuf":"d","fromValue":"'. $criteria['from'] . '"}';
			}
		}

		/* - - - - - - - - - - - - - */
		/* Actual Search begins here */
		$paramStr = "[" . $params . "," . $searchTags . "]";
		//echo "srch params: $paramStr <br>\n";
		return $paramStr;
	}

}

class BiblioIter extends Iter {
	public function __construct($rows) {
		parenr::__construct();
		$this->rows = $rows;
		$this->marc = new MarcStore;
	}
	function next() {
		$row = $this->rows->next();
		if (!$row)
			return NULL;
		$row['marc'] = $this->marc->get($row['bibid']);
		return $row;
	}
	function skip() {
		$this->rows->skip();
	}
	function count() {
		return $this->rows->count();
	}
}

