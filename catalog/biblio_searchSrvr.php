<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	
	## --------------------- ##
require_once(REL(__FILE__, "../classes/Query.php"));
class SrchDb {
	function SrchDb () {
		$this->db = new Query;
	}
	function getBiblioByBarcd($barcd){
		$sql = "SELECT b.bibid "
					."	FROM `biblio_copy` bc,`biblio` b "
					." WHERE (bc.`barcode_nmbr` = $barcd)"
					."	 AND (b.`bibid` = bc.`bibid`)";
		//echo "sql=$sql<br />";
		$rcd = $this->db->select1($sql);
		$this->barCd = $barcd;
		$this->bibid = $rcd[bibid];
		return $rcd;
	}
	function getBiblioByPhrase($jsonSpec) {
	  $spec = json_decode($jsonSpec, true);
	  $sql = "SELECT DISTINCT bs.bibid "
					."  FROM `biblio_field` bf, `biblio_subfield` as bs "
					." WHERE (1=1) AND ";
		$firstLine = true;
		foreach ($spec as $item) {
		  if (!$firstLine) $sql .= " OR ";
				else
			$firstLine = false;
			$sql .= "   ( (bf.`tag` = '$item[tag]') "
			      . "	AND (bs.`bibid` = bf.`bibid`) "
			      . "	AND (bs.`fieldid` = bf.`fieldid`) "
			      . "	AND (bs.`subfield_cd` = '$item[suf]') "
			      . " AND (bs.`subfield_data` LIKE '%$_REQUEST[searchText]%') "
			      . " )";
		}
		//echo "sql=$sql<br />";
		$rows = $this->db->select($sql);
		while (($row = $rows->next()) !== NULL) {
			$rslt[] = $row[bibid];
		}
		return $rslt;
	}
	function getBiblioInfo($bibid) {
	  $this->bibid =$bibid;
		$sql = "SELECT DISTINCT b.*, m.description, cd.description, cc.`days_due_back`, m.image_file "
					."	FROM `biblio_copy` bc,`biblio` b,`material_type_dm` m,"
					."			 `collection_dm` cd, `collection_circ` cc"
					." WHERE (b.`bibid` = '$bibid')"
					."	 AND (m.`code` = b.`material_cd`)"
					."	 AND (cd.`code` = b.`collection_cd`)"
					."	 AND (cc.`code` = b.`collection_cd`)";
		//echo "sql=$sql<br />";
		$rcd = $this->db->select1($sql);
		$this->createDt = $rcd[create_dt];
		$this->daysDueBack = $rcd[days_due_back];
		$this->matlcd = $rcd[material_cd];
		$this->collCd = $rcd[collection_cd];
		$this->imageFile =$rcd[image_file];
		return $rcd;
	}
	function getBiblioDetail() {
		$sql = "SELECT  CONCAT(bf.tag,bs.subfield_cd) AS marcTag, "
				 . "				m.label, bs.subfield_data AS value "
				 . "  FROM `material_fields` m, `biblio_field` bf, `biblio_subfield` bs "
				 . " WHERE (bf.`bibid` = $this->bibid) "
				 . "	 AND (bs.`bibid` = bf.`bibid`) "
				 . "	 AND (bs.`fieldid` = bf.`fieldid`) "
				 . "	 AND (m.`material_cd` = $this->matlcd) "
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
	function getCopyInfo ($bibid) {
		$sql = "SELECT * "
		      ."	FROM `biblio_copy` c, `biblio_status_hist` h"
		      ." WHERE (c.`bibid` = $bibid)"
		      ."	 AND (h.`bibid` = c.`bibid`)"
		      ."	 AND (h.`copyid` = c.`copyid`)"
		      ." ORDER BY c.`create_dt`";
		//echo "sql=$sql<br />";
		$rows = $this->db->select($sql);
		while (($row = $rows->next()) !== NULL) {
			$rslt[] = json_encode($row);
		}
		return $rslt;
	}
	function insertCopy($bibid) {
		$this->db->lock();
		$sql = "INSERT `biblio_copy` SET "
		      ."`bibid` = $bibid,"
		      ."`barcode_nmbr` = '$_POST[barcode_nmbr]',"
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
	function deleteCopy($bibid,$copyid) {
		$this->db->lock();
		$sql = "DELETE FROM `biblio_copy` "
					." WHERE (`bibid` = $bibid) AND (`copyid` = $copyid) ";
		//echo "sql=$sql<br />";
		$rows = $this->db->act($sql);
		$this->db->unlock();
		return T('Delete completed');
	}
}

	#****************************************************************************
	switch ($_REQUEST[mode]) {
	case 'getBarcdNmbr':
		require_once(REL(__FILE__, "../model/Copies.php"));
		$copies = new Copies;
		$CopyNmbr= $copies->getNextCopy();
		echo "{'barcdNmbr':'".sprintf("%05s",$_REQUEST[bibid])."$CopyNmbr'}";
	  break;
	  
	case 'deleteCopy':
	  $theDb = new SrchDB;
		echo $theDb->deleteCopy($_REQUEST[bibid],$_REQUEST[copyid]);
		break;

	case 'updateCopy':
	  $theDb = new SrchDB;
		echo $theDb->updateCopy($_REQUEST[bibid],$_REQUEST[copyid]);
		break;

	case 'newCopy':
	  $theDb = new SrchDB;
		echo $theDb->insertCopy($_REQUEST[bibid]);
		break;

	case 'getCrntMbrInfo':
		require_once(REL(__FILE__, "../functions/info_boxes.php"));
		echo currentMbrBox();
	  break;
	  
	case 'getMaterialList':
		require_once(REL(__FILE__, "../functions/inputFuncs.php"));
		require_once(REL(__FILE__, "../model/MaterialTypes.php"));
		$mattypes = new MaterialTypes;
		echo inputfield('select', 'mediaType', 'all', NULL, $mattypes->getSelect(true));
	  break;

	case 'getCopyInfo':
	  $theDb = new SrchDB;
	  echo json_encode($theDb->getCopyInfo($_REQUEST[bibid]));
	  break;

	case 'doBarcdSearch':
	  $theDb = new SrchDB;
	  $theDb->getBiblioByBarcd($_REQUEST[searchText]);
	  $theDb->getBiblioInfo($theDb->bibid);
		echo "{'barCd':'$theDb->barCd','bibid':'$theDb->bibid','imageFile':'$theDb->imageFile',"
				."'daysDueBack':'$theDb->daysDueBack', 'createDt':'$theDb->createDt',"
				."'data':".json_encode($theDb->getBiblioDetail())
				."}";
	  break;

	case 'doPhraseSearch':
	  $theDb = new SrchDB;
	  switch ($_REQUEST[searchType]) {
	    case 'title': 		$biblioLst = $theDb->getBiblioByPhrase('[{"tag":"245","suf":"a"},
																											 {"tag":"245","suf":"b"}]'); break;
			case 'author': 		$biblioLst = $theDb->getBiblioByPhrase('[{"tag":"100","suf":"a"},
					 																						 {"tag":"245","suf":"c"}]'); break;
			case 'subject': 	$biblioLst = $theDb->getBiblioByPhrase('[{"tag":"650","suf":"a"}]'); break;
			case 'keyword': 	$biblioLst = $theDb->getBiblioByPhrase('[{"tag":"000","suf":"a"}]'); break;
//		case 'series': 		$rslts = $theDb->getBiblioByPhrase('[{"tag":"000","suf":"a"}]'); break;
			case 'publisher': $biblioLst = $theDb->getBiblioByPhrase('[{"tag":"260","suf":"b"}]'); break;
			case 'callno': 		$biblioLst = $theDb->getBiblioByPhrase('[{"tag":"099","suf":"a"}]'); break;
	    
	  	default:
	  		echo "<h5>Invalid Search Type: $_REQUEST[srchBy]</h5>";
	  		exit;
		}
		foreach ($biblioLst as $bibid) {
			$theDb->getBiblioInfo($bibid);
			$biblio[] =  "{'barCd':'$theDb->barCd','bibid':'$theDb->bibid','imageFile':'$theDb->imageFile',"
									."'daysDueBack':'$theDb->daysDueBack', 'createDt':'$theDb->createDt',"
									."'data':".json_encode($theDb->getBiblioDetail())
									."}";
		}
		echo json_encode($biblio);
		break;
		
	default:
	  echo "<h5>Invalid mode: $_REQUEST[mode]</h5>";
	}
