<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

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
					." WHERE (bc.`barcode_nmbr` = '".$barcd."')"
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
		$sql = "DELETE FROM `biblio_copy_fields` "
					." WHERE (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);
		$this->db->unlock();
		return T("Delete completed");
	}
	## ========================= ##
	function getBiblioFields() {
	  require_once(REL(__FILE__,"../catalog/biblioFields.php"));
	}
} // class

?>
