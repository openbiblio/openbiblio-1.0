<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));

	require_once(REL(__FILE__, "../lookup2/AdminDBs.php"));


	switch ($_REQUEST[mode]){
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

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'd-3-L-3-tHost':
			## delete host database entry
			$hptr = new Hosts;
			$key = $hptr->key;
			$sql = "DELETE FROM $hptr->name WHERE `id`=$_GET[id]";
			$hptr->db->act($sql);
			break;

  	#-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: $_REQUEST[mode]</h4><br />";
		break;
	}
