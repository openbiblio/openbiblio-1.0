<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once("../model/MaterialFields.php");
	//print_r($_REQUEST);echo "<br />";


	switch ($_REQUEST['mode']){
	  case 'exportLayout':
			$db = new MaterialFields;
			$set = $db->getMatches(array('material_cd'=>$_GET['material_cd']));
			while ($row = $set->next()) {
			  $list[] = $row;
			}
//print_r($list);
			echo json_encode($list);
			break;
			
		default:
		  echo "<h4>".T("invalid mode").": &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}

?>
