<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
	//print_r($_POST);echo "<br />";
	
	function moduleList () {
		$handl = opendir("..");
		while (false !== ($file = readdir($handl))) {
		  if ($file != '.' && $file != '..') {
				if ((is_dir('../'.$file)) && 
						(substr($file,0,1) != '.') && 
						($file != 'images') && 
						($file != 'font') && 
						($file != 'photos')   
					 ) {
					$mods[] = $file;
				}
			}
		}
		closedir($handl);
		sort($mods,SORT_LOCALE_STRING);
		return $mods;
	};
	function array_flat($array) {
		$tmp = array();
	  foreach($array as $a) {
	    if(is_array($a)) {
	      $tmp = array_merge($tmp, array_flat($a));
	    } else {
      	$tmp[] = $a;
    	}
  	}
  	return $tmp;
	}
	function getFileList($dir) {
  	$files = array();
  	if ($handle = opendir($dir)) {
    	while (false !== ($file = readdir($handle))) {
      	if ($file != "." && $file != ".." && 
						$file != 'sql' && 
						$file != 'legacy' && 
						$file != 'themes') {
          if(is_dir($dir.'/'.$file)) {
          	$dir2 = $dir.'/'.$file;
          	$files[] = getFileList($dir2);
          } else {
          	$aFile = $dir.'/'.$file;
            $files[] = $aFile;
          }
        }
    	}
    	closedir($handle);
  	}
  	return array_flat($files);
	}

	/* define T() string to match to a trans[] entry */
	/* - used to check for missing or needed entries */
	$allFileExp = "/(T\(\")(.*?)(\"(,|\)))/";
	$rptFileExp = "/(title\=\")(.*?)(\")/";

	switch ($_POST['mode']){
	  case 'fetchLocaleList':
	  	echo json_encode(Localize::getLocales());
	  	break;
	  	
	  case 'fetchModuleList':
	  	$mods = moduleList();
	  	echo json_encode($mods);
	  	break;
	  	
	  case 'ck4TransDupes':
	  	$arrKeys = array();
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
				echo T("No duplicates found");
			} else { 
				echo T("Found %errCount% duplicate(s)",array('errCount'=>$errCount));
			};
	  	break;
	  	
	  case 'ck4TransUnused':
	  	$modules = moduleList();
	  	$found = array();
			require(REL(__FILE__, "../locale/".$_POST['locale']."/trans.php"));
			foreach ($modules as $module) {	
				$files = getFileList('../'.$module);
				foreach ($files as $file) {
					preg_match('/\.[^.\/*?"<>|\r\n]+$/', $file,$grp);
					$ext = $grp[0];
				  $lines = file("../$module/$file");
				  foreach ($lines as $line_num => $line) {
						if ($ext == '.rpt') {
							preg_match_all($rptFileExp,$line,$out, PREG_PATTERN_ORDER);
//print_r($out);echo ",br />";
						} else {
							preg_match_all($allFileExp,$line,$out, PREG_PATTERN_ORDER);
						}
						foreach ($out[2] as $key) {
							if ($found[$key] == 'OK') {
								continue;
							} elseif (isset($trans[$key])) {
								$found[$key] = 'OK';
							}
						}
				  }
				}
			}
			if (count($trans) == count($found)) {
				echo T("All trans entries are in use.");
			} elseif (count($trans) > count($found)) {
				$nmbr = count($trans) - count($found);
				echo "<p>".T("The following %nmbr% are not being used and may be removed.",array('nmbr'=>$nmbr))."</p>";
				foreach ($trans as $key=>$v) {
					if ($found[$key] == 'OK') {
						continue;
					} else {
						echo $key."<br />";
					}
				}
			}
			break;
			
	  case 'ck4TransNeeded':
			require(REL(__FILE__, "../locale/".$_POST['locale']."/trans.php"));
			$module = $_POST['module'];		
			echo '<p class="bold">module: '.$module.'</p>';
			$files = getFileList('../'.$module);
			foreach ($files as $file) {
				preg_match('/\.[^.\/*?"<>|\r\n]+$/', $file,$grp);
				$ext = $grp[0];
			  $lines = file("../$module/$file");
			  foreach ($lines as $line_num => $line) {
						if ($ext == '.rpt') {
							preg_match_all($rptFileExp,$line,$out, PREG_PATTERN_ORDER);
						} else {
							preg_match_all($allFileExp,$line,$out, PREG_PATTERN_ORDER);
						}
					foreach ($out[2] as $key) {
//if ($ext == '.rpt')print_r($key);echo "<br />";
						if (!isset($trans[$key])) {
							$linenum[$errCount]['key']=$key;		
							$linenum[$errCount]['filename']=$file;
							$linenum[$errCount]['linenum']=$line_num+1;
							$errCount++;
						}
					}	
			  }
			
			}
			if ($errCount > 0){
				echo T("The following %errs% entries are neeeded:", array('errs'=>$errCount))."<br />";
				while ($i < $errCount) {
					echo $linenum[$i]['filename']." - ";
					echo ($linenum[$i]['linenum'])." - ";
					echo $linenum[$i]['key']. "<br />";
					$i++;
				}
			} else {
				echo T("All trans requests are translated for locale %loc%", array('loc'=>$_POST['locale']))."<br />";
			}
			break;

		case 'ck4TransMaybe':
			require(REL(__FILE__, "../locale/".$_POST['locale']."/trans.php"));
			$module = $_POST['module'];
			//$module = 'admin';
			echo '<p class="bold">module: '.$module.'</p>';
			$files = getFileList('../'.$module);
			foreach ($files as $file) {
			  $lines = file("../$module/$file");
			  foreach ($lines as $line_num => $line) {
			  	preg_match_all("/(?<=value=)(\")([^$#.\/<>\)].*?)(\")/",$line,$grps, PREG_PATTERN_ORDER);
					foreach ($grps[2] as $key) {
							echo "$file - ".($line_num+1)." - $key<br />";
					}
			  }
			}
			break;

		default:
		  echo "<h4>".T("invalid mode").": &gt;$_POST[mode]&lt;</h4><br />";
		break;
	}

?>
