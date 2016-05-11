<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_REQUEST);echo "<br />";
	
	
	switch ($_REQUEST['cat']) {
		case 'validation':
			require_once(REL(__FILE__, "../model/Validations.php"));
	  	$db = new Validations;
			break;

		case 'locale':
			require_once(REL(__FILE__, "../classes/Queryi.php"));
			require_once(REL(__FILE__, "../classes/Localize.php"));
	  	$db = new Queryi;
			break;

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
		case 'getDbSrvrInfo':
			$info = array();
			$set = array();
			$rslt = $db->select1("SELECT VERSION()");
			$info['version'] = $rslt;

			$set = array();
			$rslt = $db->select("SELECT * FROM Information_Schema.Engines ORDER by ENGINE");
			while ($row = $rslt->fetch_assoc()) {
				$engine = array('support'=>$row['SUPPORT'], 'transactions'=>$row['TRANSACTIONS']);
				$set[$row['ENGINE']] = $engine;
			}
			$info['engines'] = $set;

			$set = array();
			$rslt = $db->select("SHOW Variables LIKE 'character\_set\_%'");
			while ($row = $rslt->fetch_assoc()) {
				$set[$row['Variable_name']] = $row['Value'];
			}
			$info['charSets'] = $set;

			$set = array();
			$rslt = $db->select("SHOW Variables LIKE 'collation\_%'");
			while ($row = $rslt->fetch_assoc()) {
				$set[$row['Variable_name']] = $row['Value'];
			}
			$info['collations'] = $set;

			$set = array();
			$rslt = $db->select("SHOW Variables LIKE 'max_%'");
			while ($row = $rslt->fetch_assoc()) {
				$set[$row['Variable_name']] = $row['Value'];
			}
			$info['misc'] = $set;
			echo json_encode($info);

			break;

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
				  	
	  #-.-.-.-.-.- validations -.-.-.-.-.-.-
		case 'getAll_validation':
		  $valids = array();
			$set = $db->getAll('description');
			while ($row = $set->fetch_assoc()) {
			  $valids[] = $row;
			}
			echo json_encode($valids);
			break;
		case 'addNew_validation':
			echo $db->insert($_REQUEST);
			break;
		case 'update_validation':
			echo $db->update($_REQUEST);
			break;
		case 'd-3-L-3-t_validation':
			echo $db->deleteOne($_POST['code']);
			break;

		default:
		  echo "<h4>".T("invalid mode").": &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
