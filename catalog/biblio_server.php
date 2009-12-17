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
	
	// Load session data in case of OPAC (eg no user logged on)
	if(empty($_SESSION['show_checkout_mbr'])) $_SESSION['show_checkout_mbr'] = Settings::get('show_checkout_mbr');	
	if(empty($_SESSION['show_detail_opac'])) $_SESSION['show_detail_opac'] = Settings::get('show_detail_opac');	
	if(empty($_SESSION['show_copy_site'])) $_SESSION['show_copy_site'] = Settings::get('show_copy_site');
	if(empty($_SESSION['show_item_photos'])) $_SESSION['show_item_photos'] = Settings::get('show_item_photos');	
	if(empty($_SESSION['current_site'])) $_SESSION['current_site'] = Settings::get('library_name');
	if(empty($_SESSION['items_per_page'])) $_SESSION['items_per_page'] = Settings::get('items_per_page');
	

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
				$sqlSelect .= " LEFT JOIN biblio_field AS sortf ON sortf.bibid = `b`.`bibid`"
					." AND sortf.tag = '" . $item['orderTag'] . "'"
					." LEFT JOIN biblio_subfield AS sorts ON sorts.fieldid = sortf.fieldid"
					." AND sorts.subfield_cd = '" . $item['orderSuf'] . "'";
				$sqlOrder = " ORDER BY sorts.`subfield_data`;";					
			}
			if(isset($item['siteTag'])){
			//				$searchTags .= ',{"siteTag":"xxx","siteValue":"'. $_REQUEST['searchSite'] . '"}';
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
		$rcd = $this->db->select01($sql);
		$this->createDt = $rcd[create_dt];
		$this->daysDueBack = $rcd[days_due_back];
		$this->matlCd = $rcd[material_cd];
		$this->collCd = $rcd[collection_cd];
		$this->imageFile =$rcd[image_file];
		$this->opacFlg = $rcd[opac_flg];
		
		// If the show details OPAC  flag is set get info on the copies	
		if ($_SESSION['show_detail_opac'] == 'Y'){
			$copies = $this->getCopyInfo($bibid);
			// Need to add site specific code in here in here, for now just look for 
			// status options: available, available on other site, on hold, not available
			if (!empty($copies)) {
				$this->avIcon = null;
				foreach($copies as $copyEnc){
					$copy = json_decode($copyEnc, true);
					if($copy['statusCd'] == "in") {
						// See on which site
						if($_SESSION['current_site'] == $copy['siteid'] || $_SESSION['show_copy_site'] != 'Y'){
							$this->avIcon = "circle_green.png"; // one or more available
							break;						
						} else {
							$this->avIcon = "circle_orange.png"; // one or more available on another site
						}
					}
					else if($copy[statusCd] == "hld")
						if($this->avIcon == null) $this->avIcon = "circle_blue.png"; // only copy is on hold
					else
						if($this->avIcon == null) $this->avIcon = "circle_red.png"; // copy not available
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

		while ($copy = $bcopies->next()) {
			$status = $history->getOne($copy['histid']);
			$booking = $bookings->getByHistid($copy['histid']);
		if ($_SESSION['show_copy_site'] == 'Y') {
				$sites_table = new Sites;
				$sites = $sites_table->getSelect();
				$copy['site'] = $sites[$copy[siteid]];
			}
			$copy['status'] = $states[$status[status_cd]];
			$copy['statusCd'] = $status[status_cd];
			if($_SESSION['show_checkout_mbr'] == "Y" && ($status[status_cd] == "out" || $status[status_cd] == "hld")){
				$checkout_mbr = $copies->getCheckoutMember($copy[histid]);
				$copy['mbrId'] = $checkout_mbr[mbrid];
				$copy['mbrName'] = "$checkout_mbr[first_name] $checkout_mbr[last_name]";
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
		      ."`siteid` = ".$_SESSION['current_site']."," // set to current site
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
		return T('Update completed');
	}
	## ========================= ##
	function updateCopy($bibid,$copyid) {
		$this->db->lock();
		$sql = "UPDATE `biblio_copy` SET "
		      ."`barcode_nmbr` = '$_POST[barcode_nmbr]',"
		      ."`copy_desc` = '$_POST[copy_desc]' "
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
		$this->db->unlock();
		return T('Update completed');
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
		function getlabel($f) {
			global $LOC;
			$label = "";
			if ($f['label'] != "") {
				$label = $f['label'];
			} elseif ($f['subfield'] != "") {
				$idx = sprintf("%03d$%s", $f['tag'], $f['subfield']);
				$label = $LOC->getMarc($idx);
			} else {
				$label = $LOC->getMarc($f['tag']);
			}
			return $label;
		}
		function mkinput($fid, $sfid, $data, $f) {
			return array('fieldid' => $fid,
				'subfieldid' => $sfid,
				'data' => $data,
				'tag' => $f['tag'],
				'subfield' => $f['subfield_cd'],
				'label' => getlabel($f),
				'required' => $f['required'],
				'form_type' => $f['form_type'],
				'repeat' => $f['repeatable']);
		}
		function mkFldSet($n, $i, $marcInputFld, $mode) {
		  if ($mode == 'onlnCol') {
				echo "	<td valign=\"top\" class=\"primary filterable\"> \n";
				$namePrefix = "onln_$n";
		    echo "<input type=\"button\" value=\"<--\" id=\"$namePrefix"."_btn\" class=\"accptBtn\" /> \n";
			}
			else if ($mode == 'editCol') {
				echo "	<td valign=\"top\" class=\"primary\"> \n";
				$namePrefix = 'fields['.H($n).']';
				echo inputfield('hidden', $namePrefix."[tag]",         H($i['tag']))." \n";
				echo inputfield('hidden', $namePrefix."[subfield_cd]", H($i['subfield']))." \n";
				echo inputfield('hidden', $namePrefix."[fieldid]",     H($i['fieldid']),
												array('id'=>$marcInputFld.'_fieldid'))." \n";
				echo inputfield('hidden', $namePrefix."[subfieldid]",  H($i['subfieldid']),
												array('id'=>$marcInputFld.'_subfieldid'))." \n";
			}
			
			$attrs = array("id"=>"$marcInputFld");
			$attrStr = "marcBiblioFld";
			if ($i['required'])
			  $attrStr .= " reqd";
			if ($i['repeat'])
			  $attrStr .= " rptd";
			else
			  $attrStr .= " only1";
		  if ($mode == 'onlnCol')
		    $attrStr .= " online";
			else
			  $attrStr .= " offline";
			$attrs["class"] = $attrStr;

			if ($i['form_type'] == 'text') {
			  $attrs["size"] = "50"; $attrs["maxLength"] = "75";
				echo inputfield('text', $namePrefix."[data]", H($i['data']),$attrs)." \n";
			} else {
				// IE seems to make the font-size of a textarea overly small under
				// certain circumstances.  We force it to a sane value, even
				// though I have some misgivings about it.  This will make
				// the font smaller for some people.
				$attrs["style"] = "font-size:10pt; font-weight: normal;";
				$attrs["rows"] = "7"; $attrs["cols"] = "38";
				echo inputfield('textarea', $namePrefix."[data]", H($i['data']),$attrs)." \n";
			}
			echo "</td> \n";
		}
		
		$mf = new MaterialFields;

		// get field specs in 'display postition' order
		$fields = $mf->getMatches(array('material_cd'=>$_REQUEST[matlCd]), 'position');

		## anything to process for current media type (material_cd) ?
		if ($fields->count() == 0) {
			echo "<tr><td colspan=\"2\" class=\"primary\">.T('No fields to fill in.').</td></tr>\n";
		}

		## build an array of fields to be displayed on user form
		$inputs = array();
		while (($f=$fields->next())) {
		  #  make multiples of those so flagged
			for ($n=0; $n<=$f['repeatable']; $n++) {
				array_push($inputs, mkinput(NULL, NULL, NULL, $f));
			}
		}

		## now build html for those input fields
		foreach ($inputs as $n => $i) {
			$marcInputFld = H($i['tag']).H($i['subfield']);
			echo "<tr> \n";
			echo "	<td class=\"primary\" valign=\"top\"> \n";
			if ($i['required']) {
				echo '	<sup>*</sup>';
			}
			echo "		<label for=\"$marcInputFld\">".H($i['label'].":")."</label>";
			echo "	</td> \n";
			
			mkFldSet($n, $i, $marcInputFld, 'editCol');	// normal local edit column
			mkFldSet($n, $i, $marcInputFld, 'onlnCol');  // update on-line column

		echo "</tr> \n";
		}
	}
}
	#****************************************************************************
//
//if (in_array('lookup2',$_SESSION)) {
//	echo "Lookup2 system code available.<br />";
//}

	switch ($_REQUEST[mode]) {
	case 'getOpts':
		//setSessionFmSettings(); // only activate for debugging!
		echo "{'lookupAvail':'".in_array('lookup2',$_SESSION)."'"
				.",'showBiblioPhotos':'$_SESSION[show_item_photos]'}";
	  break;
	  
	case 'getCrntMbrInfo':
		require_once(REL(__FILE__, "../functions/info_boxes.php"));
		echo currentMbrBox();
	  break;

	case 'getMaterialList':
		require_once(REL(__FILE__, "../model/MaterialTypes.php"));
		$mattypes = new MaterialTypes;
		echo inputfield('select', 'mediaType', 'all', NULL, $mattypes->getSelect(true));
	  break;

	case 'getCollectionList':
		require_once(REL(__FILE__, "../model/Collections.php"));
		$collections = new Collections;
		echo inputfield('select', "collectionCd", $value, NULL, $collections->getSelect());
	  break;

	case 'getSiteList':
		require_once(REL(__FILE__, "../model/Sites.php"));
		$sites_table = new Sites;		
		echo inputfield('select', 'searchSite', 'all', NULL, $sites = $sites_table->getSelect(true));
	  break;	  
	  
	case 'doBarcdSearch':
	  $theDb = new SrchDB;
	  $rslt = $theDb->getBiblioByBarcd($_REQUEST[searchText]);
	  if ($rslt != NULL) {
	  	$theDb->getBiblioInfo($theDb->bibid);
			echo "{'barCd':'$theDb->barCd','bibid':'$theDb->bibid','imageFile':'$theDb->imageFile',"
					."'daysDueBack':'$theDb->daysDueBack', 'createDt':'$theDb->createDt',"
					."'matlCd':'$theDb->matlCd', 'collCd':'$theDb->collCd', 'opacFlg':'$theDb->opacFlg',"
					."'data':".json_encode($theDb->getBiblioDetail())
					."}";
		} else {
			echo "{'data':null}";
		}
	  break;

	case 'doPhraseSearch':
	  $theDb = new SrchDB;
	  switch ($_REQUEST[searchType]) {
	    case 'title': 		$type = 'phrase';
							$params = '{"tag":"245","suf":"a"},
								{"tag":"245","suf":"b"}'; 
							break;
			case 'author': 		$type = 'phrase';
								$params ='{"tag":"100","suf":"a"},
								{"tag":"245","suf":"c"}'; 
							break;
			case 'subject': 	$type = 'words';
								$params = '{"tag":"650","suf":"a"}'; 
								break;
			case 'keyword': 	$type = 'words';
								$params ='{"tag":"245","suf":"a"},
									{"tag":"650","suf":"a"},
									{"tag":"100","suf":"a"},
									{"tag":"245","suf":"b"},
									{"tag":"245","suf":"c"}'; 
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
				case 'title':  $searchTags .= '{"orderTag":"245","orderSuf":"a"}'; break;
				default: $searchTags .= '{"orderTag":"245","orderSuf":"a"}'; break;
			}
		}		
		if($_REQUEST['advanceQ']=='Y'){
			if(isset($_REQUEST['searchSite']) && $_REQUEST['searchSite'] != 'all'){
				$searchTags .= ',{"siteTag":"xxx","siteValue":"'. $_REQUEST['searchSite'] . '"}';
			}
			if(isset($_REQUEST['mediaType']) && $_REQUEST['mediaType'] != 'all'){
				//Not sure about the tag, but leave it as is for the moment, as it is a field in bibid (material_cd)
				$searchTags .= ',{"mediaTag":"099","mediaSuf":"a","mediaValue":"'. $_REQUEST['mediaType'] . '"}';
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
			$biblio[] = "{'totalNum':'" . sizeof($biblioLst) . "','firstItem':'" . $firstItem . "','lastItem':'". $lastItem . "','itemsPage':'". $_SESSION['items_per_page'] . "'}";
			// Only show as many as in the settings (not the most efficient way to get the whole result query, this should be rewritten
			$iterCounter = 0;		
			foreach ($biblioLst as $bibid) {
				// Skip if before requested items and break when amount of items is past - LJ
				$iterCounter++;
				if($iterCounter - 1 < $firstItem) continue;
				if($iterCounter > $lastItem) break;
				$theDb->getBiblioInfo($bibid);
				$copyData = "{'barCd':'$theDb->barCd','bibid':'$theDb->bibid','imageFile':'$theDb->imageFile',"
										."'daysDueBack':'$theDb->daysDueBack', 'createDt':'$theDb->createDt',"
										."'matlCd':'$theDb->matlCd', 'collCd':'$theDb->collCd', 'opacFlg':'$theDb->opacFlg',";
				if($_SESSION['show_detail_opac'] == 'Y') $copyData .= "'avIcon':'$theDb->avIcon',";
				$copyData .=			"'data':".json_encode($theDb->getBiblioDetail())
										."}";
				$biblio[] = $copyData;
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

	case 'getBarcdNmbr':
		require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
		$CopyNmbr= $copies->getNextCopy();
		echo "{'barcdNmbr':'".sprintf("%05s",$_REQUEST[bibid])."$CopyNmbr'}";
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
	  require_once(REL(__FILE__,"biblio_updater.php"));
	  break;

	case 'deleteBiblio':
	  $theDb = new Biblios;
	  $theDb->deleteOne($_REQUEST[bibid]);
	  echo T("Delete completed");
	  break;

	case 'updateCopy':
	  $theDb = new SrchDB;
		echo $theDb->updateCopy($_REQUEST[bibid],$_REQUEST[copyid]);
		break;

	case 'newCopy':
	  $theDb = new SrchDB;
		echo $theDb->insertCopy($_REQUEST[bibid],$_REQUEST[copyid]);
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
