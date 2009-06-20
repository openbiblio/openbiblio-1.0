<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));

	if ((empty($_REQUEST[mode]))&& (!empty($_REQUEST[editMode]))) {
    $_REQUEST[mode] = $_REQUEST[editMode];
	}
	
	switch ($_REQUEST[mode]){
	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMtlTypes':
			## prepare list of Material Types
			require_once(REL(__FILE__, "../model/MaterialTypes.php"));
			$tptr = new MaterialTypes;
		  $matls = array();
			$tSet = $tptr->getAll('code');
			while ($row = $tSet->next()) {
			  $matls[] = $row;
			}
			//print_r($matls);
			echo json_encode($matls);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getMatlFlds':
			## prepare list of Material fields in use
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$fptr = new MaterialFields;
			$typeCd = array('material_cd' => $_GET['matlCd']);
		  $flds = array();
			$fSet = $fptr->getMatches($typeCd,'position');
			while ($row = $fSet->next()) {
			  $flds[] = $row;
			}
			//print_r($hosts);
			echo json_encode($flds);
			break;
			
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updateFldSet':
			## update material fields for a specific material type
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$fptr = new MaterialFields;
			if (empty($_POST[required])) $_POST[required] = '0';
			echo $fptr->update($_POST);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'd-3-L-3-tFld':
			## delete Material_fields database entry
			require_once(REL(__FILE__, "../model/MaterialFields.php"));
			$fptr = new MaterialFields;
			//$sql = "DELETE FROM $fptr->name WHERE `material_field_id`=$_GET[material_field_id]";
			//$fptr->db->act($sql);
			echo $fptr->deleteOne($_GET[material_field_id]);
			break;
/*
	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getOpts':
			## prepare list of hosts
			$optr = new Opts;
	  	$opts = array();
			$oSet = $optr->getAll();
			$row = $oSet->next();
			//print_r($hosts);
			echo json_encode($row);
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updateOpts':
			## update host database entry
			$optr = new Opts;
		  $_POST[id] = 1;
			if (empty($_POST[autoDewey])) $_POST[autoDewey] = 'n';
			if (empty($_POST[defaultDewey])) $_POST[defaultDewey] = 'n';
			if (empty($_POST[autoCutter])) $_POST[autoCutter] = 'n';
			if (empty($_POST[autoCollect])) $_POST[autoCollect] = 'n';
			$rslt = $optr->update($_POST);
			if(empty($rslt)) $rslt = '1';
			echo $rslt;
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getHosts':
			## prepare list of hosts
			$hptr = new Hosts;
		  $hosts = array();
			$hSet = $hptr->getAll('seq');
			while ($row = $hSet->next()) {
			  $hosts[] = $row;
			}
			//print_r($hosts);
			echo json_encode($hosts);
			break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'addNewHost':
			## add new host database entry
			$hptr = new Hosts;
			if (empty($_POST[active])) $_POST[active] = 'n';
			echo $hptr->insert($_POST);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'updateHost':
			## update host database entry
			$hptr = new Hosts;
			if (empty($_POST[active])) $_POST[active] = 'n';
			echo $hptr->update($_POST);
			break;

*/
  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: $_REQUEST[mode]</h4><br />";
		break;
	}
