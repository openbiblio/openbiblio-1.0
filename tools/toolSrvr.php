<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_REQUEST);echo "<br />";
	
	function moduleList () {
			$handl = opendir("..");
			while (false !== ($file = readdir($handl))) {
			  if ($file != '.' && $file != '..') {
					if ((is_dir('../'.$file)) && 
							(substr($file,0,1) != '.') && 
							($file != 'images') && ($file != 'font') && ($file != 'photos')   
						 ) {
						$mods[] = $file;
					}
				}
			}
			closedir($handl);
			sort($mods,SORT_LOCALE_STRING);
			return $mods;
	};
	
	
	switch ($_REQUEST['cat']) {
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
				  	
	  #-.-.-.-.-.- locale -.-.-.-.-.-.-
	  case 'fetchLocaleList':
	  	echo json_encode(Localize::getLocales());
	  	break;
	  	
	  case 'fetchModuleList':
	  	$mods = moduleList();
	  	echo json_encode($mods);
	  	break;
	  	
	  case 'ck4TransDupes':
	  	$arrKeys = [];
			$lines = file("../locale/".$_POST['locale']."/trans.php");
			foreach ($lines as $line_num => $line) {
				//echo substr($line,0,1)."<br />";
				if (   substr($line,0,1)!="#" 
						&& substr($line,0,2)!=" *" 
						&& substr($line,0,1)!="<" 
						&& substr($line,0,1)!="?" 
						&& substr($line,0,1)!="\n") {
					if (strpos($line,"]")>1) {
						list($key,$value)=explode("]",$line);
						$key = str_replace("\$trans[\"","",$key);
						$key = str_replace("\"","",$key);
						if (in_array($key, $arrKeys, true)) {
							echo "Line #{$line_num}: " . chop($line) . " is a duplicate<br />";
							$errCount++;
						} else {				
							$arrKeys[]=$key;
						}
					}
				}
			}
			if ($errCount < 1) {
				echo "No duplicates found<br />";
			} else { 
				echo "Found $errCount duplicate(s)<br />";
			};
	  	break;
	  	
	  case 'ck4TransOrfan':
	  	$modules = moduleList();
	  	$found = [];
			require(REL(__FILE__, "../locale/".$_POST['locale']."/trans.php"));
			foreach ($modules as $module) {	
				$files = [];	
				$handler = opendir("../$module");
				while (false !== ($file = readdir($handler))) {
				  if ($file != '.' && $file != '..')
					$files[] = $file;
				}
				closedir($handler);
				foreach ($files as $file) {
				  $lines = file("../$module/$file");
				  foreach ($lines as $line_num => $line) {
						preg_match_all("|T\((.*)\)|U",$line,$out, PREG_PATTERN_ORDER);
						foreach ($out[1] as $key) {
							$key = str_replace("\"","",$key);
							$key = str_replace("\'","",$key);
							if ($found[$key] == 'OK') {
								continue;
							} elseif (isset($trans[$key])) {
								$found[$key] = 'OK';
							}
						}	
				  }
				}
			}
//echo "trans: ".count($trans)."; found: ".count($found)."<br />"; 	
//print_r($found);echo "<br /><br />";	 
			if (count($trans) == count($found)) {
				echo "All trans entries are in use.";
			} elseif (count($trans) > count($found)) {
			echo "<p>The following ".(count($trans) - count($found))." are not being used and may be removed.</p>";
				foreach ($trans as $k=>$v) {
					if ($found[$k] == 'OK') {
						continue;
					} else {
						echo $k."<br />";
					}
				}
			}
			break;
			
	  case 'ck4TransAbsnt':
			require(REL(__FILE__, "../locale/".$_POST['locale']."/trans.php"));
			$module = $_POST['module'];		
			echo '<p class="bold">module: '.$module.'</p>';
			$handler = opendir("../$module");
			while (false !== ($file = readdir($handler))) {
			  if ($file != '.' && $file != '..')
				$files[] = $file;
			}
			closedir($handler);
			foreach ($files as $file) {
			  $lines = file("../$module/$file");
			  foreach ($lines as $line_num => $line) {
					preg_match_all("|T\((.*)\)|U",$line,$out, PREG_PATTERN_ORDER);
					foreach ($out[1] as $key) {
						$key = str_replace("\"","",$key);
						$key = str_replace("\'","",$key);
						if (!array_key_exists($key, $trans)) {
							$linenum[$errCount]['key']=$key;		
							$linenum[$errCount]['filename']=$file;
							$linenum[$errCount]['linenum']=$line_num+1;
							$errCount++;
						}
					}	
			  }
			
			}
			if ($errCount > 0){
				echo "The following errors were found:<br />";
				while ($i < $errCount) {
					echo $linenum[$i]['filename']." - ";
					echo ($linenum[$i]['linenum'])." - ";
					echo $linenum[$i]['key']. "<br />";
					$i++;
				}
			} else {
				echo "All trans call in $module are translated in ".$_POST['locale']."<br />";
			}
			echo ' - - - - - - - - - <br />';
			break;
			
		default:
		  echo "<h4>invalid mode: &gt;$_REQUEST[mode]&lt;</h4><br />";
		break;
	}
/*
, array('count'=>$count)
*/
