<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));
//  require_once(REL(__FILE__, "../functions/errorFuncs.php"));

	require_once(REL(__FILE__, "../lookup2/lookupDBs.php"));

	$optr = new Opts;

	switch ($_REQUEST[mode]){
	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'getOpts':
			## prepare list of hosts
	  	$opts = array();
			$oSet = $optr->getAll();
			$row = $oSet->next();
			//print_r($hosts);
			echo json_encode($row);
		break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		case 'update':
			## update host database entry
		  $_POST[id] = 1;
			if (empty($_POST[autoDewey])) $_POST[autoDewey] = 'n';
			if (empty($_POST[defaultDewey])) $_POST[defaultDewey] = 'n';
			if (empty($_POST[autoCutter])) $_POST[autoCutter] = 'n';
			if (empty($_POST[autoCollect])) $_POST[autoCollect] = 'n';
			echo $optr->update($_POST);
		break;

	  #-.-.-.-.-.-.-.-.-.-.-.-.-
		default:
		  echo "<h4>invalid mode: $_REQUEST[mode]</h4><br />";
		break;
	}
