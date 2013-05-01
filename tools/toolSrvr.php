<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_REQUEST);echo "<br />";
	
	switch ($_REQUEST['cat']) {
		case 'database':
			require_once(REL(__FILE__, "../classes/Queryi.php"));
	  	$db = new Queryi;
			break;

		default:
		  echo "<h4>invalid category: &gt;".$_REQUEST['cat']."&lt;</h4><br />";
		  exit;
			break;
	}

	switch ($_REQUEST['mode']){
	  #-.-.-.-.-.- database -.-.-.-.-.-.-
	  case 'fetchCollSet':
		  $set = array();
			$result = $db->select('SHOW COLLATION');
			while ($row = $result->fetch_assoc()) {
			  $set[$row['Collation']] = $row['Charset'];
			}
			ksort($set);
			echo json_encode($set);
	  	break;
	  	
		case 'changeColl':
			$charset = $_REQUEST['charset'];
			$collation = $_REQUEST['collation'];
			$rslt = $db->select('SHOW TABLES');
			while ($row = $rslt->fetch_array()) {
				$tbl = $row[0];
				$sql = "ALTER TABLE `$tbl` DEFAULT CHARACTER SET $charset COLLATE $collation";
				//echo "sql=$sql<br />\n";
				$result = $db->act($sql);
				echo '<p>'.T("Table")." ".$tbl." ".T("updated to")." ".$collation.'</p>';
			}
			break;
				  	
		default:
		  echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
/*
, array('count'=>$count)
*/
