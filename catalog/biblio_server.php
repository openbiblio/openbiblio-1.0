<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../classes/Query.php"));
	require_once(REL(__FILE__, "../model/Biblios.php"));
	require_once(REL(__FILE__, "../model/Sites.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
	require_once(REL(__FILE__, "../model/CopyStates.php"));
	require_once(REL(__FILE__, "../model/CopiesCustomFields.php"));	
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
	
	
	// Load session data in case of OPAC (eg no user logged on)
	if(empty($_SESSION['show_checkout_mbr'])) $_SESSION['show_checkout_mbr'] = Settings::get('show_checkout_mbr');	
	if(empty($_SESSION['show_detail_opac'])) $_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');	
	if(empty($_SESSION['multi_site_func'])) $_SESSION['multi_site_func'] = Settings::get('multi_site_func');
	if(empty($_SESSION['show_item_photos'])) $_SESSION['show_item_photos'] = Settings::get('show_item_photos');	
	if(empty($_SESSION['items_per_page'])) $_SESSION['items_per_page'] = Settings::get('items_per_page');
	
	// Adjusted, so that if 'library_name' contains a string, the site is put by default on 1.
	if(empty($_SESSION['current_site'])) {
		if(isset($_COOKIE['OpenBiblioSiteID'])) {
			$_SESSION['current_site'] = $_COOKIE['OpenBiblioSiteID'];				
		} elseif($_SESSION['multi_site_func'] > 0){
			$_SESSION['current_site'] = $_SESSION['multi_site_func']; 			
		} else {
			$_SESSION['current_site'] = 1;
		}		
	}

	## --------------------- ##
class SrchDb {
	public $bibid;
	public $createDt;
	public $daysDueBack;
	public $matlCd;
	public $collCd;
	public $imageFile;
	public $opacFlg;
	public $avIcon = "circle_green.png";

	function SrchDb () {
		$this->db = new Query;
	}
	## ========================= ##
	function getBiblioByBarcd($barcd){
		$sql = "SELECT b.bibid "
					."	FROM `biblio_copy` bc,`biblio` b "
					." WHERE (bc.`barcode_nmbr` = $barcd)"
					."	 AND (b.`bibid` = bc.`bibid`)";
		//echo "sql=$sql<br />";
		$rcd = $this->db->select01($sql);
		$this->barCd = $barcd;
		$this->bibid = $rcd[bibid];
		return $rcd;
	}
	## ========================= ##
	function getBiblioByPhrase($mode, $jsonSpec) {
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
			$sqlWhere .= " AND bf$keywordnr.bibid = b.bibid "
									." AND bs$keywordnr.fieldid = bf$keywordnr.fieldid "
									." AND bs$keywordnr.`subfield_data` LIKE '%$kwd%'";
			$termnr = 1;
			$sqlWhere .= " AND (";
			$firstWhere = true;
			foreach ($spec as $item) {
				// Continue when the item has to do with selecting and ordering
				if(!isset($item['tag'])) continue;
				if(!$firstWhere) $sqlWhere .= " OR";
				$firstWhere = false;
				$sqlWhere .= " bf$keywordnr.tag='" . $item['tag'] . "' AND bs$keywordnr.subfield_cd = '" . $item['suf'] . "'";
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
		$rows = $this->db->select($sql);
		while (($row = $rows->next()) !== NULL) {
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
		$rcd = $this->db->select01($sql);
//print_r($rcd);echo "<br />\n";
		$this->createDt = $rcd['create_dt'];
		$this->daysDueBack = $rcd['days_due_back'];
		$this->matlCd = $rcd['material_cd'];
		$this->collCd = $rcd['collection_cd'];
		$this->imageFile =$rcd['image_file'];
		$this->opacFlg = $rcd['opac_flg'];
		
		// If the show details OPAC  flag is set get info on the copies	
		if ($_SESSION['show_detail_opac'] == 'Y'){
			$copies = $this->getCopyInfo($bibid);
			// Need to add site specific code in here in here, for now just look for 
			// status options: available, available on other site, on hold, not available
			if (!empty($copies)) {
				// default copy not available
				$this->avIcon = "circle_red.png";
				foreach($copies as $copyEnc){
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
			$rcd['avIcon'] = $this->avIcon;
		}
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
		$rows = $this->db->select($sql);
		while (($row = $rows->next()) !== NULL) {
			$rslt[] = json_encode($row);
			//$rslt[] = "{'marcTag':'$row[marcTag]','label':'$row[label]','value':'" . addslashes($row[value]) . "'"
			//				 .",'fieldid':'$row[fieldid]','subfieldid':'$row[subfieldid]'}";
		}
		return $rslt;
	}
	## ========================= ##
	function getCopyInfo ($bibid) {
		$copies = new Copies; // needed later
		$bcopies = $copies->getMatches(array('bibid'=>$bibid));
		$copy_states = new CopyStates;
		$states = $copy_states->getSelect();
		$history = new History;
		$bookings = new Bookings;

		$BCQ = new BiblioCopyFields;
		$custRows = $BCQ->getAll();			
		$custFieldList = array();
		while ($row = $custRows->next()) {
			$custFieldList[$row["code"]] = "";
		}			
		
		while ($copy = $bcopies->next()) {
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
			while ($row = $custom->next() ) {
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
		$this->db->lock();
		$sql = "INSERT `biblio_copy` SET "
		      ."`bibid` = $bibid,"
		      ."`barcode_nmbr` = '$_POST[barcode_nmbr]',"
//		      ."`siteid` = ".$_SESSION['current_site']."," // set to current site
		      ."`siteid` = '$_POST[copy_site]'," // set to current site
		      ."`create_dt` = NOW(),"
		      ."`last_change_dt` = NOW(),"
		      ."`last_change_userid` = $_SESSION[userid],"
		      ."`copy_desc` = '$_POST[copy_desc]' ";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);
		
		$copyid = $this->db->getInsertID();
		$sql = "Insert `biblio_status_hist` SET "
		      ."`bibid` = $bibid,"
		      ."`copyid` = $copyid,"
		      ."`status_cd` = '$_POST[status_cd]',"
		      ."`status_begin_dt` = NOW()";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);
		$histid = $this->db->getInsertID();
		
		$sql = "Update `biblio_copy` SET "
		      ."`histid` = '$histid' "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);
		$this->db->unlock();
		
		// Update custom fields if set
		$copies = new Copies;
		$custom = array();
		$BCQ = new BiblioCopyFields;
		$rows = $BCQ->getAll();
		while ($row = $rows->next()) {
			if (isset($_POST['custom_'.$row["code"]])) {
				$custom[$row["code"]] = $_POST['custom_'.$row["code"]];
			}
		}
		$copies->setCustomFields($copyid, $custom);
		return "!!success!!";
	}
	## ========================= ##
	function updateCopy($bibid,$copyid) {
		$this->db->lock();
		$sql = "UPDATE `biblio_copy` SET "
		      ."`barcode_nmbr` = '$_POST[barcode_nmbr]', "
		      ."`copy_desc` = '$_POST[copy_desc]', "
		      ."`siteid` = '$_POST[siteid]' "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);

		$sql = "SELECT `status_cd` FROM `biblio_status_hist` "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid)";
		//echo "sql=$sql<br />";
		$rcd = $this->db->select1($sql);
		if ($rcd[status_cd] != $_POST[status_cd]) {
			$sql = "UPDATE `biblio_status_hist` SET "
			      ."`status_cd` = '$_POST[status_cd]',"
			      ."`status_begin_dt` = NOW() "
						." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
			//echo "sql=$sql<br />";
			$rows = $this->db->act($sql);
		}
		// Update custom fields if set
		$copies = new Copies;
		$custom = array();
		$BCQ = new BiblioCopyFields;
		$rows = $BCQ->getAll();		
		while ($row = $rows->next()) {
			if (isset($_REQUEST['custom_'.$row["code"]])) {
				$custom[$row["code"]] = $_POST['custom_'.$row["code"]];
			}			
		}		
		$copies->setCustomFields($copyid, $custom);		
		
		$this->db->unlock();
		// Changed this to nothing, so any message/output is taken as an error message - LJ
		// Changed to specific success text to be looked for in JS - FL
		echo "!!success!!";
		return;
	}
	## ========================= ##
	function deleteCopy($bibid,$copyid) {
		$this->db->lock();
		$sql = "DELETE FROM `biblio_copy` "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);
		$this->db->unlock();
		return T('Delete completed');
	}
	## ========================= ##
	function getBiblioFields() {
	  require_once(REL(__FILE__,"../catalog/biblio_fields.php"));
	}
} // class

function mkBiblioArray($dbObj) {
 	$rslt['barCd'] = $dbObj->barCd;
 	$rslt['bibid'] = $dbObj->bibid;
 	$rslt['imageFile'] = $dbObj->imageFile;
 	$rslt['daysDueBack'] = $dbObj->daysDueBack;
 	$rslt['createDt'] = $dbObj->createDt;
 	$rslt['matlCd'] = $dbObj->matlCd;
 	$rslt['collCd'] = $dbObj->collCd;
 	$rslt['opacFlg'] = $dbObj->opacFlg;
 	$rslt['data'] = $dbObj->getBiblioDetail();
 	return $rslt;
}

	#****************************************************************************
	switch ($_REQUEST[mode]) {
	case 'getOpts':
		//setSessionFmSettings(); // only activate for debugging!
		$opts['lookupAvail'] = in_array('lookup2',$_SESSION);
		$opts['current_site'] = $_SESSION[current_site];
		$opts['showBiblioPhotos'] = $_SESSION[show_item_photos];
		$opts['barcdWidth'] = $_SESSION[item_barcode_width];
		echo json_encode($opts);
	  break;

	case 'getCrntMbrInfo':
		require_once(REL(__FILE__, "../functions/info_boxes.php"));
		echo currentMbrBox();
	  break;

	case 'getMaterialList':
		require_once(REL(__FILE__, "../model/MaterialTypes.php"));
		if(isset($_REQUEST['selectedMt'])){
			$selectedMt = $_REQUEST['selectedMt'];
		} else {
			$selectedMt = 'all';
		}
		$mattypes = new MaterialTypes;
		echo inputfield('select', 'materialCd', $selectedMt, NULL, $mattypes->getSelect(true));
	  break;

	case 'getCollectionList':
		require_once(REL(__FILE__, "../model/Collections.php"));
		if(isset($_REQUEST['selectedCt'])){
			$selectedCt = $_REQUEST['selectedCt'];
		} else {
			$selectedCt = null;
		}		
		$collections = new Collections;
		echo inputfield('select', "collectionCd", $selectedCt, NULL, $collections->getSelect());
	  break;

	case 'getSiteList':
		$sites_table = new Sites;		
		$sites = $sites_table->getSelect();
		foreach ($sites as $val => $desc) {
			$s .= '<option value="'.H($val).'" '.">".H($desc)."</option>\n";
		}
		echo $s;
	  break;
	  
	case 'doBarcdSearch':
	  $theDb = new SrchDB;
	  $rslt = $theDb->getBiblioByBarcd($_REQUEST['searchBarcd']);
	  if ($rslt != NULL) {
	  	$theDb->getBiblioInfo($theDb->bibid);
	  	echo json_encode(mkBiblioArray($theDb));
		} else {
			echo '{"data":null}';
		}
	  break;

	case 'doBibidSearch':
	  $theDb = new SrchDB;
  	$theDb->getBiblioInfo($_REQUEST[bibid]);
	  	echo json_encode(mkBiblioArray($theDb));
	  break;

	case 'doPhraseSearch':
	  $theDb = new SrchDB;
	  switch ($_REQUEST[searchType]) {
	    case 'title': 		$type = 'phrase';
							$params = '{"tag":"245","suf":"a"},
								{"tag":"245","suf":"b"}'; 
							break;
			case 'author': 		$type = 'words';
								$params ='{"tag":"100","suf":"a"},
								{"tag":"245","suf":"c"}'; 
							break;
			case 'subject': 	$type = 'words';
								$params = '{"tag":"650","suf":"a"},
								{"tag":"505","suf":"a"}'; 
								break;
			case 'keyword': 	$type = 'words';
								$params ='{"tag":"245","suf":"a"},
									{"tag":"650","suf":"a"},
									{"tag":"100","suf":"a"},
									{"tag":"245","suf":"b"},
									{"tag":"245","suf":"c"},
									{"tag":"505","suf":"a"}'; 
								break;
//		case 'series': 		$rslts = $theDb->getBiblioByPhrase('[{"tag":"000","suf":"a"}]'); break;
			case 'publisher': 	$type = 'phrase';
								$params = '{"tag":"260","suf":"b"}'; 
								break;
			case 'callno': 		$type = 'phrase';
								$params = '{"tag":"099","suf":"a"}'; 
								break;
	    
	  	default:
	  		echo "<h5>Invalid Search Type: $_REQUEST[srchBy]</h5>";
	  		exit;
		}
		// Add search params
		$searchTags = "";
		
		if(isset($_REQUEST['sortBy'])){
				switch ($_REQUEST['sortBy']){
				case 'author': $searchTags .= '{"orderTag":"100","orderSuf":"a"}'; break;
				case 'callno': $searchTags .= '{"orderTag":"099","orderSuf":"a"}'; break;
				case 'title':  $searchTags .= '{"orderTag":"245","orderSuf":"a"},{"orderTag":"245","orderSuf":"b"}'; break;
				default: $searchTags .= '{"orderTag":"245","orderSuf":"a"},{"orderTag":"245","orderSuf":"b"}'; break;
			}
		}		

		if($_REQUEST['advanceQ']=='Y'){
			if(isset($_REQUEST['srchSites']) && $_REQUEST['srchSites'] != 'all'){
				$searchTags .= ',{"siteTag":"xxx","siteValue":"'. $_REQUEST['srchSites'] . '"}';
			}
			if(isset($_REQUEST['materialCd']) && $_REQUEST['materialCd'] != 'all'){
				//Not sure about the tag, but leave it as is for the moment, as it is a field in bibid (material_cd)
				$searchTags .= ',{"mediaTag":"099","mediaSuf":"a","mediaValue":"'. $_REQUEST['materialCd'] . '"}';
			}			
			if(isset($_REQUEST['audienceLevel'])){
				//Not sure which field this, so leave this for now - LJ
				//$searchTags .= ',{"audienceTag":"099","audienceSuf":"a","audienceValue":"'. $_REQUEST['audienceLevel'] . '"}';
			}
			if(isset($_REQUEST['to']) && strlen($_REQUEST['to']) == 4){
				$searchTags .= ',{"toTag":"260","toSuf":"c","toValue":"'. $_REQUEST['to'] . '"}';
			}
			if(isset($_REQUEST['from']) && strlen($_REQUEST['from']) == 4){
				$searchTags .= ',{"fromTag":"260","fromSuf":"c","fromValue":"'. $_REQUEST['from'] . '"}';		
			}
		}			
				
		$paramStr = "[" . $params . "," . $searchTags . "]";
		$biblioLst = $theDb->getBiblioByPhrase($type, $paramStr);
		if (sizeof($biblioLst) > 0) {
			// Add amount of search results.
			if($_REQUEST['firstItem'] == null){
				$firstItem = 0;
			} else {
				$firstItem = $_REQUEST['firstItem'];
			}
			if($_SESSION['items_per_page'] <= sizeof($biblioLst) - $firstItem){
				$lastItem = $firstItem + $_SESSION['items_per_page'];
			} else {
				$lastItem = sizeof($biblioLst);
			}
			
			## multi-page record header
			$rcd['totalNum'] = sizeof($biblioLst);
			$rcd['firstItem'] = $firstItem;
			$rcd['lastItem'] = $lastItem;
			$rcd['itemsPage'] = $_SESSION['items_per_page'];
			$biblio[] = json_encode($rcd);

			// Only show as many as in the settings (not the most efficient way to get the whole result query, this should be rewritten
			$iterCounter = 0;		
			foreach ($biblioLst as $bibid) {
				// Skip if before requested items and break when amount of items is past - LJ
				$iterCounter++;
				if($iterCounter - 1 < $firstItem) continue;
				if($iterCounter > $lastItem) break;
				$theDb->getBiblioInfo($bibid);
	  		$rslt = mkBiblioArray($theDb);
				if($_SESSION['show_detail_opac'] == 'Y')
					$rslt['avIcon'] = $theDb->avIcon;
	  		$biblio[] = json_encode($rslt);
			}
			echo json_encode($biblio);
		} else {
			echo '[]';
		}
		break;
		
	case 'addToCart':
		require_once(REL(__FILE__, "../model/Cart.php"));
		$name = $_REQUEST['name'];
		$cart = getCart($name);
		if (isset($_REQUEST['id'])) {
			foreach ($_REQUEST['id'] as $id) {
				$rslt = $cart->contains($id);
				if (!$rslt) $cart->add($id);
			}
		}
	  break;

	case 'getNewBarcd':
		require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
		$temp['barcdNmbr'] = $copies->getNewBarCode($_SESSION[item_barcode_width]);
		echo json_encode($temp);
	  break;	  
	  
	case 'chkBarcdForDupe':
	  $copies = new Copies;
	  if ($copies->isDuplicateBarcd($_REQUEST[barcode_nmbr],$_REQUEST[copyid]))
			echo "Barcode $_REQUEST[barcode_nmbr]: ". T("Barcode number already in use.");
		break;
		
	case 'getBiblioFields':
		require_once(REL(__FILE__, "../model/MaterialFields.php"));
		$theDb = new SrchDB;
		$theDb->getBiblioFields();
		break;
		
	case 'getCopyInfo':
	  $theDb = new SrchDB;
	  echo json_encode($theDb->getCopyInfo($_REQUEST[bibid]));
	  break;

	case 'updateBiblio':
	  $nav = '';
	  require_once(REL(__FILE__,"biblio_change.php"));
	  echo $msg;
	  break;

	case 'deleteBiblio':
	  $theDb = new Biblios;
	  $theDb->deleteOne($_REQUEST[bibid]);
	  echo T("Delete completed");
	  break;

	case 'updateCopy':
	case 'newCopy':
	  $theDb = new SrchDB;
	  $copies = new Copies;
	  if ($copies->isDuplicateBarcd($_POST[barcode_nmbr], $_POST[copyid])) {
			echo "Barcode $_REQUEST[barcode_nmbr]: ". T("Barcode number already in use.");
			return;
		}
		switch ($_REQUEST[mode]) {
		  case 'updateCopy':
	  		echo $theDb->updateCopy($_REQUEST[bibid],$_REQUEST[copyid]);
				break;
			
		  case 'newCopy':
				echo $theDb->insertCopy($_REQUEST[bibid],$_REQUEST[copyid]);
				break;
		}
		break;
		
	case 'deleteCopy':
	  $theDb = new SrchDB;
		echo $theDb->deleteCopy($_REQUEST[bibid],$_REQUEST[copyid]);
		break;

	case 'getPhoto':
	  ## place keeper for this release
	  echo "<img src=\"../images/shim.gif\" />";
	  break;
	  
	default:
	  echo "<h5>Invalid mode: $_REQUEST[mode]</h5>";
	}
