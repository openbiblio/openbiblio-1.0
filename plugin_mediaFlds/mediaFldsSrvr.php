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
			echo json_encode($list);
			break;

	  case 'inportLayout':
			$db = new MaterialFields;
			$recs = json_decode($_POST['layout']);
			$nRecs = count($recs);
			echo "got to server with $nRecs records.<br />";
			$rec = [];
			## one row at a time
			foreach ($recs as $recO) {
				## convert 'std obj' to array
				foreach ($recO as $k=>$v) {
					$rec[$k] = $v;
					$rec['material_field_id'] = null;
					$rec['material_cd'] = 999;
				}
				$err = $db->insert($rec);
				echo $err;
			}
			break;

		default:
		  echo "<h4>".T("invalid mode")." @mediaFldsSrvr.php: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}

?>
