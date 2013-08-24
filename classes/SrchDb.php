<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/marcFuncs.php"));
	require_once(REL(__FILE__, '../classes/Queryi.php'));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

/**
 * This class provides Biblio search facilities
 * @author Luuk Jansen
 * @author Fred LaPlante
 */

class SrchDb extends Queryi {
	public $bibid;
	private $createDt;
	private $daysDueBack;
	private $matlCd;
	private $collCd;
	private $imageFile;
	private $opacFlg;
	private $nCpy;
	private $avIcon = "circle_green.png";

	public function __construct() {
		parent::__construct();
	}
	public function getData (){
		//function mkBiblioArray($dbObj) {
	 	$rslt['barCd'] = $this->barCd;
	 	$rslt['bibid'] = $this->bibid;
	 	$rslt['imageFile'] = $this->imageFile;
	 	$rslt['daysDueBack'] = $this->daysDueBack;
	 	$rslt['createDt'] = $this->createDt;
	 	$rslt['matlCd'] = $this->matlCd;
	 	$rslt['collCd'] = $this->collCd;
	 	$rslt['opacFlg'] = $this->opacFlg;
		$rslt['avIcon'] = $this->avIcon;
		$rslt['nCpy'] = $this->nCpy;
	 	$rslt['data'] = $this->getBiblioDetail();
	 	return $rslt;
	}

	## ========================= ##
	function getBiblioByBarcd($barcd){
		$sql = "SELECT b.bibid "
					."	FROM `biblio_copy` bc,`biblio` b "
					." WHERE (bc.`barcode_nmbr` = '".$barcd."')"
					."	 AND (b.`bibid` = bc.`bibid`)";
		//echo "sql=$sql<br />";
		$rcd = $this->select01($sql);
		$this->barCd = $barcd;
		$this->bibid = $rcd[bibid];
		return $rcd;
	}
	## ========================= ##
	function makeParamStr($criteria) {
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
			if(isset($criteria['audienceLevel'])){
				//Not sure which field this, so leave this for now - LJ
				//$searchTags .= ',{"audienceTag":"099","audienceSuf":"a","audienceValue":"'. $criteria['audienceLevel'] . '"}';
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
		return $paramStr;
	}
	## ========================= ##
	function getBiblioByPhrase($criteria, $mode=null) {
		$jsonSpec = $this->makeParamStr($criteria);
		/* mode may be null at times */
	  $spec = json_decode($jsonSpec, true);
	  $srchTxt = strtolower($_REQUEST[searchText]);
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
			if(isset($item['siteTag'])){
					$sqlSelect .= " JOIN `biblio_copy` bc";
					$sqlWhere .= " AND bc.bibid = b.bibid "
									." AND bc.siteid = '" . $item['siteValue'] . "' ";					
			}
			if(isset($item['mediaTag'])){
				$sqlWhere .= " AND b.material_cd = '" . $item['mediaValue'] ."'";
			}
			if(isset($item['collTag'])){
				$sqlWhere .= " AND b.collection_cd = '" . $item['collValue'] ."'";
			}
			if(isset($item['audienceTag'])){
//				$searchTags .= '{"audienceTag":"099","audienceSuf":"a","audienceValue":"'. $_REQUEST['audienceLevel'] . '"}';
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
		//echo "sql=$sql<br />";
		$rows = $this->select($sql);
		while (($row = $rows->fetch_assoc()) !== NULL) {
			$rslt[] = $row[bibid];
		}
		return $rslt;
	}
	## ========================= ##
	function getBiblioInfo($bibid) {
	  $this->bibid =$bibid;
		$sql = "SELECT DISTINCT b.*, m.description, cd.description, cc.`days_due_back`, m.image_file "
					."	FROM `biblio` b,`material_type_dm` m,"
					."			 `collection_dm` cd, `collection_circ` cc"
					." WHERE (b.`bibid` = '$bibid')"
					."	 AND (m.`code` = b.`material_cd`)"
					."	 AND (cd.`code` = b.`collection_cd`)"
					."	 AND (cc.`code` = b.`collection_cd`)";
		//echo "sql=$sql<br />\n";
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
				$this->avIcon = "circle_red.png";
				foreach($copies as $copyEnc){
					$this->nCpy = $this->nCpy+1;
					$copy = json_decode($copyEnc, true);
					if($copy['statusCd'] == OBIB_STATUS_IN) {
						// See on which site
						if($_SESSION['current_site'] == $copy['siteid'] || !($_SESSION['multi_site_func'] > 0)){
							$this->avIcon = "circle_green.png"; // one or more available
							break;						
						} else {
							$this->avIcon = "circle_orange.png"; // one or more available on another site
						}
					}
					// Removed && $this->avIcon != "circle_orange.png" as and extra clause, as it is better to show the book is there, even if not available
					else if($copy[statusCd] == OBIB_STATUS_ON_HOLD || $copy[statusCd] == OBIB_STATUS_NOT_ON_LOAN)
						$this->avIcon = "circle_blue.png"; // only copy is on hold
				}
			} else {
				$this->avIcon = "circle_red.png"; // no copy found
			}
			$rcd['nCpy'] = $this->nCpy;
			$rcd['avIcon'] = $this->avIcon;
		return $rcd;
	}
	## ========================= ##
	function getBiblioDetail() {		
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
		//echo "sql=$sql<br />";
		$rows = $this->select($sql);
		while (($row = $rows->fetch_assoc()) !== NULL) {
			$rslt[] = json_encode($row);
			//$rslt[] = "{'marcTag':'$row[marcTag]','label':'$row[label]','value':'" . addslashes($row[value]) . "'"
			//				 .",'fieldid':'$row[fieldid]','subfieldid':'$row[subfieldid]'}";
		}
		return $rslt;
	}
	
	## ========================= ##
	function getBibsForCpys ($barcode_list) {
		global $opts;
		$copies = new Copies;
		# build an array of barcodes
		$barcodes = array();
		foreach (explode("\n", $barcode_list) as $b) {
			if (trim($b) != "") {
				$barcodes[] = str_pad(trim($b), $opts['barcdWidth'], '0', STR_PAD_LEFT);
			}
		}
		$rslt = $copies->lookupBulk_el($barcodes);
		return $rslt;
	}
	
	## ========================= ##
	function getCopyInfo ($bibid) {
		$copies = new Copies; // needed later
		$bcopies = $copies->getMatches(array('bibid'=>$bibid),'barcode_nmbr');
		$copy_states = new CopyStatus;
		$states = $copy_states->getSelect();
		$history = new History;
		$bookings = new Bookings;

		$BCQ = new BiblioCopyFields;
		$custRows = $BCQ->getAll();			
		$custFieldList = array();
		while ($row = $custRows->fetch_assoc()) {
			$custFieldList[$row["code"]] = "";
		}			

		while ($copy = $bcopies->fetch_assoc()) {
			$status = $history->getOne($copy['histid']);
			$booking = $bookings->getByHistid($copy['histid']);
			if ($_SESSION['multi_site_func'] > 0) {
				$sites_table = new Sites;
				$sites = $sites_table->getSelect();
				$copy['site'] = $sites[$copy[siteid]];
			}
			$copy['status'] = $states[$status[status_cd]];
			$copy['statusCd'] = $status[status_cd];
			if($_SESSION['show_checkout_mbr'] == "Y" && ($status[status_cd] == OBIB_STATUS_OUT || $status[status_cd] == OBIB_STATUS_ON_HOLD)){
				if($status[status_cd] == OBIB_STATUS_OUT){
					$checkout_mbr = $copies->getCheckoutMember($copy[histid]);
				} else {
					$checkout_mbr = $copies->getHoldMember($copy[copyid]);
				}
				$copy['mbrId'] = $checkout_mbr[mbrid];
				$copy['mbrName'] = "$checkout_mbr[first_name] $checkout_mbr[last_name]";				
			}
			// Add custom fields - Bit complicated, but seems the easiest way to populate empty fields (list compiled at beginning of procedure to lower databse queries)		
			// Now populate data				
			$custom = $copies->getCustomFields($copy[copyid]);
			$copy['custFields'] = array();
			$fieldList = $custFieldList;
			//while ($row = $custom->fetch_assoc() ) {
			while ($row = $custom->fetch_assoc() ) {
				$fieldList[$row["code"]] = $row["data"];
			}

			//Finally add to copy
			foreach($fieldList as $key => $value){
				$copy['custFields'][] = array('code' => $key, 'data' => $value);
			}			
			$rslt[] = json_encode($copy);
		}
		return $rslt;
	}
	## ========================= ##
	function insertCopy($bibid,$copyid) {
		//$this->lock();
		if (empty($_POST['copy_site'])) {
			$theSite = $_SESSION['current_site'];
		} else {
			$theSite = $_POST['copy_site'];
		}
		$sql = "INSERT `biblio_copy` SET "
		      ."`bibid` = $bibid,"
		      ."`barcode_nmbr` = '".$_POST['barcode_nmbr']."',"
		      ."`siteid` = '$theSite'," // set to current site
		      ."`create_dt` = NOW(),"
		      ."`last_change_dt` = NOW(),"
		      ."`last_change_userid` = $_SESSION[userid],"
		      ."`copy_desc` = '".$_POST['copy_desc']."' ";
//echo "sql=$sql<br />";
		$rows = $this->act($sql);
		
		$copyid = $this->getInsertID();
		$sql = "Insert `biblio_status_hist` SET "
		      ."`bibid` = $bibid,"
		      ."`copyid` = $copyid,"
		      ."`status_cd` = '$_POST[status_cd]',"
		      ."`status_begin_dt` = NOW()";
//echo "sql=$sql<br />";
		$rows = $this->act($sql);
		$histid = $this->getInsertID();
		
		$sql = "Update `biblio_copy` SET "
		      ."`histid` = '$histid' "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
//echo "sql=$sql<br />";
		$rows = $this->act($sql);
//echo"rows===>{$rows}<br/>\n";
		//$this->unlock();
		
		// Update custom fields if set
		$custom = array();
		$ptr = new BiblioCopyFields;
		$rows = $ptr->getAll();
		while ($row = $rows->fetch_assoc()) {
			if (isset($_POST['custom_'.$row["code"]])) {
				$custom[$row["code"]] = $_POST['custom_'.$row["code"]];
			}
		}
		$copies = new Copies;
		$copies->setCustomFields($copyid, $custom);
		return "!!success!!";
	}
	## ========================= ##
	function updateCopy($bibid,$copyid) {
		$this->lock();
		$sql = "SELECT `status_cd`, `histid` FROM `biblio_status_hist` "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid)"
					." ORDER BY status_begin_dt";
//echo "sql=$sql<br />";
		$rslt = $this->select($sql);
		$rcd = $rslt->fetch_assoc();  // only first (most recent) response wanted
		$histid = $rcd['histid'];

		if ($rcd[status_cd] != $_POST[status_cd]) {
			$sql = "INSERT `biblio_status_hist` SET "
			      ."`status_cd` = '$_POST[status_cd]',"
			      ."`status_begin_dt` = NOW(),"
						."`bibid` = $bibid,"
						."`copyid` = $copyid ";
//echo "sql=$sql<br />";
			$rslt = $this->act($sql);
			$histid = $this->getInsertID();
		}

		$sql = "UPDATE `biblio_copy` SET "
		      ."`barcode_nmbr` = '$_POST[barcode_nmbr]', "
		      ."`copy_desc` = '$_POST[copy_desc]', "
		      ."`siteid` = '$_POST[siteid]', "
					."`histid` = $histid "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
//echo "sql=$sql<br />";
		$rows = $this->act($sql);
//echo"rows===>{$rows}<br/>\n";
		// Update custom fields if set
		$custom = array();
		$ptr = new BiblioCopyFields;
		$rows = $ptr->getAll();
		while ($row = $rows->fetch_assoc()) {
			if (isset($_REQUEST['copyCustom_'.$row["code"]])) {
				$custom[$row["code"]] = $_POST['copyCustom_'.$row["code"]];
			}			
		}		
		$copies = new Copies;
		$copies->setCustomFields($copyid, $custom);
		
		$this->unlock();
		// Changed this to nothing, so any message/output is taken as an error message - LJ
		// Changed to specific success text to be looked for in JS - FL
		echo "!!success!!";
		return;
	}
	## ========================= ##
	function deleteCopy($copyid) {
		$this->lock();
		$sql = "DELETE FROM `biblio_copy` "
					." WHERE (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->act($sql);
		if ($rows == '0') die ("copy# {$copyid} delete failed");

		$sql = "DELETE FROM `biblio_copy_fields` "
					." WHERE (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->act($sql);
		if ($rows == '0') die ("copy_field# {$copyid} delete failed");

		$this->unlock();
		return T("Delete completed");
	}
	## ========================= ##
	function getBiblioFields() {
	  require_once(REL(__FILE__,"../catalog/biblioFields.php"));
	}
} // class

?>
