<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  //require_once(REL(__FILE__, "../functions/errorFuncs.php"));

	require_once(REL(__FILE__, "../lookup2/lookupDBs.php"));

$hptr = new Hosts;

switch ($_REQUEST[mode]){
  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'getHosts':
		## prepare list of hosts
	  $hosts = array();
		$hSet = $hptr->getAll();
		while ($row = $hSet->next()) {
		  $hosts[] = $row;
		}
		//print_r($hosts);
		echo json_encode($hosts);
	break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'addNew':
		## add new host database entry
		if (empty($_POST[active])) $_POST[active] = 'n';
		echo $hptr->insert($_POST);
	break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'update':
		## update host database entry
		if (empty($_POST[active])) $_POST[active] = 'n';
		echo $hptr->update($_POST);
	break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	case 'd-3-L-3-t':
		## delete host database entry
		$key = $hptr->key;
		$sql = "DELETE FROM $hptr->name WHERE `id`=$_GET[id]";
		$hptr->db->act($sql);
	break;

  #-.-.-.-.-.-.-.-.-.-.-.-.-
	default:
	  echo "<h4>invalid mode: $_REQUEST[mode]</h4><br />";
	break;
}

?>
